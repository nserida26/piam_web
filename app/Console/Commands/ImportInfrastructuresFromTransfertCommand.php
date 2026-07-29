<?php

namespace App\Console\Commands;

use App\Models\Commune;
use App\Models\Infrastructure;
use App\Models\InfrastructureReport;
use App\Models\Localite;
use App\Models\Moughata;
use App\Models\Operateur;
use App\Models\Projet;
use App\Models\Wilaya;
use App\Support\Concerns\NormalizesAdministrativeNames;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

/**
 * Imports school/health-facility infrastructures and their operation history
 * (Inventaire / Réalisation de travaux / Programmation de travaux) from a
 * "Transfert données ... dans BADAM" workbook — the "Ecole" and "Structures
 * santé" sheets feed `infrastructures`, and the "Op5/Op6/Op7*" sheets feed
 * `infrastructure_operations`.
 *
 * Reuses the matching approach proven out on the localités side: accent/case
 * -insensitive fuzzy name matching, a same-moughata sibling-commune fallback
 * for localities reorganised since the file was produced, and an "NR"
 * placeholder fallback for a blank/"Inconnu" projet or opérateur rather than
 * dropping the row outright.
 *
 * Dry-run by default (rolled back transaction); --apply commits.
 */
class ImportInfrastructuresFromTransfertCommand extends Command
{
    use NormalizesAdministrativeNames;

    protected $signature = 'infrastructures:import-transfert
                            {--file= : Chemin du fichier "Transfert données ... dans BADAM.xlsx"}
                            {--wilaya= : Nom de la wilaya concernée (ex: Tagant)}
                            {--apply : Applique réellement les changements (sans cette option : dry-run)}
                            {--report= : Chemin d\'un CSV où écrire le détail des lignes ignorées}';

    protected $description = "Importe les infrastructures et leurs opérations (Op5/6/7) depuis un fichier Transfert ... BADAM";

    private $wilayas;
    private $moughatas;
    private $communes;
    private $localites;
    private $infrastructures;
    private $projets;
    private $operateurs;
    private $nrProjet;
    private $nrOperateur;
    private ?Wilaya $wilaya = null;

    public function handle(): int
    {
        $filePath = $this->option('file');
        if (!$filePath || !is_file($filePath)) {
            $this->error("Fichier introuvable: {$filePath}");
            return self::FAILURE;
        }

        $wilayaName = $this->option('wilaya');
        if (!$wilayaName) {
            $this->error('Merci de préciser --wilaya=NomDeLaWilaya');
            return self::FAILURE;
        }

        $this->wilayas = Wilaya::all();
        $this->wilaya = $this->wilayas->first(fn ($w) => $this->stringsMatch($w->intitule, $wilayaName) || $this->stringsMatch($w->intitule_fr, $wilayaName));
        if (!$this->wilaya) {
            $this->error("Wilaya introuvable: {$wilayaName}");
            return self::FAILURE;
        }

        $this->moughatas = Moughata::select('id', 'intitule', 'intitule_fr', 'wilaya_id')->get();
        $this->communes = Commune::select('id', 'intitule', 'intitule_fr', 'moughata_id', 'wilaya_id')->get();
        $this->localites = Localite::select('id', 'intitule', 'intitule_fr', 'commune_id', 'wilaya_id', 'code_ansade')->get();
        $this->infrastructures = Infrastructure::select('id', 'intitule_infra_publ', 'localite_id', 'code_men')->get();
        $this->projets = Projet::select('id', 'intitule_resume')->get();
        $this->operateurs = Operateur::select('id', 'intitule')->get();
        $this->nrProjet = $this->projets->first(fn ($p) => $this->normalizeString($p->intitule_resume) === 'nr');
        $this->nrOperateur = $this->operateurs->first(fn ($o) => $this->normalizeString($o->intitule) === 'nr');

        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $sheetNames = $spreadsheet->getSheetNames();

        $stats = [
            'infra_created' => 0, 'infra_existing' => 0, 'infra_skipped' => 0,
            'op_created' => 0, 'op_existing' => 0, 'op_skipped' => 0,
        ];
        $skipped = [];

        $apply = (bool) $this->option('apply');

        DB::beginTransaction();
        try {
            foreach ($sheetNames as $name) {
                if (in_array($name, ['Ecole', 'Structures santé'], true)) {
                    $this->importInfrastructureSheet($spreadsheet->getSheetByName($name), $name, $stats, $skipped);
                } elseif (preg_match('/^Op[567]\b/u', $name)) {
                    $operationId = (int) $name[2];
                    $this->importOperationSheet($spreadsheet->getSheetByName($name), $name, $operationId, $stats, $skipped);
                }
            }

            $this->table(['Indicateur', 'Valeur'], [
                ['Infrastructures créées', $stats['infra_created']],
                ['Infrastructures déjà présentes', $stats['infra_existing']],
                ['Infrastructures ignorées (localité introuvable, etc.)', $stats['infra_skipped']],
                ['Opérations créées', $stats['op_created']],
                ['Opérations déjà présentes', $stats['op_existing']],
                ['Opérations ignorées', $stats['op_skipped']],
            ]);

            if ($apply) {
                DB::commit();
                $this->info('Changements appliqués.');
            } else {
                DB::rollBack();
                $this->comment('Dry-run : aucun changement écrit. Relancez avec --apply pour appliquer.');
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Erreur — rollback complet : ' . $e->getMessage());
            throw $e;
        }

        if ($reportPath = $this->option('report')) {
            $absolutePath = str_starts_with($reportPath, '/') || preg_match('/^[A-Za-z]:/', $reportPath)
                ? $reportPath
                : base_path($reportPath);
            $fh = fopen($absolutePath, 'w');
            fputcsv($fh, ['sheet', 'row', 'reason', 'detail']);
            foreach ($skipped as $row) {
                fputcsv($fh, $row);
            }
            fclose($fh);
            $this->info("Rapport écrit dans {$absolutePath}");
        }

        return self::SUCCESS;
    }

    private function importInfrastructureSheet($sheet, string $sheetName, array &$stats, array &$skipped): void
    {
        [$header, $firstDataRow] = $this->readHeader($sheet);
        $highestRow = $sheet->getHighestRow();

        for ($r = $firstDataRow; $r <= $highestRow; $r++) {
            $row = $this->readRow($sheet, $header, $r);
            if (empty($row['localite']) || empty($row['intitule_infra_publ'])) {
                continue;
            }

            $chain = $this->resolveChain($row);
            if (!$chain) {
                $stats['infra_skipped']++;
                $skipped[] = [$sheetName, $r, 'chaîne administrative introuvable', $row['localite'] . ' / ' . ($row['commune'] ?? '')];
                continue;
            }
            [, $moughata, $commune, $localite] = $chain;

            $normTarget = $this->normalizeString($row['intitule_infra_publ']);
            $existing = $this->infrastructures->first(function ($item) use ($localite, $normTarget, $row) {
                if ($item->localite_id != $localite->id) {
                    return false;
                }
                if (!empty($row['code_men']) && !empty($item->code_men)) {
                    return (string) $item->code_men === (string) $row['code_men'];
                }
                return $this->normalizeString($item->intitule_infra_publ) === $normTarget;
            });

            if ($existing) {
                $stats['infra_existing']++;
                continue;
            }

            $newInfra = Infrastructure::create([
                'infra_publ' => $row['infra_publ'] ?? null,
                'intitule_infra_publ' => $row['intitule_infra_publ'],
                'code_infra_publ' => $row['code_infra_publ'] ?? ($localite->code_ansade . '-INF-' . ($this->infrastructures->count() + 1)),
                'code_men' => $row['code_men'] ?? null,
                'lat_infra_publ' => is_numeric($row['lat_infra_publ'] ?? null) ? (float) $row['lat_infra_publ'] : null,
                'long_infra_publ' => is_numeric($row['long_infra_publ'] ?? null) ? (float) $row['long_infra_publ'] : null,
                'localite_id' => $localite->id,
                'commune_id' => $commune->id,
                'moughata_id' => $moughata->id,
                'wilaya_id' => $this->wilaya->id,
            ]);
            $this->infrastructures->push($newInfra);
            $stats['infra_created']++;
        }
    }

    private function importOperationSheet($sheet, string $sheetName, int $operationId, array &$stats, array &$skipped): void
    {
        [$header, $firstDataRow] = $this->readHeader($sheet);
        $highestRow = $sheet->getHighestRow();

        for ($r = $firstDataRow; $r <= $highestRow; $r++) {
            $row = $this->readRow($sheet, $header, $r);
            if (empty($row['localite'])) {
                continue;
            }

            $chain = $this->resolveChain($row);
            if (!$chain) {
                $stats['op_skipped']++;
                $skipped[] = [$sheetName, $r, 'chaîne administrative introuvable', $row['localite'] . ' / ' . ($row['commune'] ?? '')];
                continue;
            }
            [, , $commune, $localite] = $chain;

            $infraName = $row['int_infra_publ'] ?? $row['intitule_infra_publ'] ?? null;
            $normInfra = $this->normalizeString((string) $infraName);
            $infrastructure = $this->infrastructures->first(function ($item) use ($localite, $normInfra, $row) {
                if ($item->localite_id != $localite->id) {
                    return false;
                }
                if (!empty($row['code_men']) && !empty($item->code_men)) {
                    return (string) $item->code_men === (string) $row['code_men'];
                }
                return $this->normalizeString($item->intitule_infra_publ) === $normInfra;
            });

            if (!$infrastructure) {
                $stats['op_skipped']++;
                $skipped[] = [$sheetName, $r, 'infrastructure introuvable', $infraName . ' @ ' . $row['localite']];
                continue;
            }

            $projetRaw = trim((string) ($row['int_projet_resum'] ?? ''));
            $projet = $this->isPlaceholderValue($projetRaw) ? null : $this->findProjet($projetRaw);
            $projet = $projet ?? $this->nrProjet;

            $operateurRaw = trim((string) ($row['intitule_se'] ?? ''));
            $operateur = $this->isPlaceholderValue($operateurRaw) ? null : $this->findOperateur($operateurRaw);
            $operateur = $operateur ?? $this->nrOperateur;

            if (!$projet || !$operateur) {
                $stats['op_skipped']++;
                $skipped[] = [$sheetName, $r, 'projet/opérateur introuvable et pas de NR', $projetRaw . ' / ' . $operateurRaw];
                continue;
            }

            $dateOp = $this->parseDate($row['date_op'] ?? null);

            $existingReport = InfrastructureReport::withoutGlobalScopes()
                ->where('infrastructure_id', $infrastructure->id)
                ->where('operation_id', $operationId)
                ->where('projet_id', $projet->id)
                ->where('operateur_id', $operateur->id)
                ->where(function ($q) use ($dateOp) {
                    $dateOp ? $q->whereDate('date_op', $dateOp) : $q->whereNull('date_op');
                })
                ->exists();

            if ($existingReport) {
                $stats['op_existing']++;
                continue;
            }

            $data = [
                'localite_id' => $localite->id,
                'commune_id' => $commune->id,
                'moughata_id' => $commune->moughata_id,
                'wilaya_id' => $this->wilaya->id,
                'operation_id' => $operationId,
                'projet_id' => $projet->id,
                'operateur_id' => $operateur->id,
                'infrastructure_id' => $infrastructure->id,
                'date_op' => $dateOp,
                'confirmed' => true,
            ];

            if ($operationId === 5) {
                $data += [
                    'effectif_garcon' => $this->toInt($row['effectif_garcon'] ?? null),
                    'effectif_fille' => $this->toInt($row['effectif_fille'] ?? null),
                    'effectif' => $this->toInt($row['effectif_tot'] ?? null),
                    'dist_approv_eau' => $this->toInt($row['dist_approv_eau'] ?? null),
                    'approv_eau' => $this->toBool($row['approv_eau_infra_publ'] ?? null),
                    'nbr_bloc' => $this->toInt($row['nb_bloc_latr'] ?? null),
                    'type_latr_publ' => $row['type_latr_publ'] ?? null,
                    'nbr_cabine_exist' => $this->toInt($row['nb_cab_latr_exist'] ?? null),
                    'nbr_cabine_fonct' => $this->toInt($row['nb_cab_latr_fonct'] ?? null),
                    'dist_cab_hom_fem' => $this->toInt($row['dist_cab_hom_fem'] ?? null),
                    'nbr_cabine_fem_hyg' => $this->toInt($row['nb_cab_fem_hyg_menstr'] ?? null),
                    'gest_dechet' => $this->toBool($row['nb_ecole_gest_dechet'] ?? null),
                    'gest_dechet_biomed' => $this->toBool($row['nb_struct_sante_gest_dechet_biomed'] ?? null),
                    'nbr_cabine_pmr' => $this->toInt($row['nb_cab_pmr'] ?? null),
                    'nb_dlm' => $this->toInt($row['nb_dlm_infra_publ'] ?? null),
                    'nb_dlm_complet' => $this->toInt($row['nb_infra_publ_dlm_complet'] ?? null),
                    'nb_dlm_incomplet' => $this->toInt($row['nb_infra_publ_dlm_incomplet'] ?? null),
                    'nb_sans_dlm' => $this->toInt($row['nb_infra_publ_sans_dlm'] ?? null),
                    'nbr_cabine_a_rehab' => $this->toInt($row['nb_cab_a_rehab'] ?? null),
                    'nbr_bloc_a_const' => $this->toInt($row['nb_bloc_a_const'] ?? null),
                    'nbr_cabine_a_const' => $this->toInt($row['nb_cab_a_const'] ?? null),
                ];
            } elseif ($operationId === 6) {
                $data += [
                    'an_recept' => $row['an_recept'] ?? null,
                    'nb_dlm_instal' => $this->toInt($row['nb_dlm_instal'] ?? null),
                    'nbr_cabine_rehab' => $this->toInt($row['nb_cab_rehab'] ?? null),
                    'nbr_bloc_const' => $this->toInt($row['nb_bloc_const'] ?? null),
                    'nbr_cabine_const' => $this->toInt($row['nb_cab_const'] ?? null),
                    'type_latr_publ' => $row['type_latr_publ'] ?? null,
                ];
            } else { // 7
                $data += [
                    'nbr_cabine_a_rehab' => $this->toInt($row['nb_cab_a_rehab'] ?? null),
                    'nbr_bloc_a_const' => $this->toInt($row['nb_bloc_a_const'] ?? null),
                    'nbr_cabine_a_const' => $this->toInt($row['nb_cab_a_const'] ?? null),
                    'an_program' => $row['an_program'] ?? null,
                ];
            }

            InfrastructureReport::create($data);
            $stats['op_created']++;
        }
    }

    /**
     * @return array{0: Wilaya, 1: Moughata, 2: Commune, 3: Localite}|null
     */
    private function resolveChain(array $row): ?array
    {
        $moughata = $this->moughatas->first(fn ($m) => $m->wilaya_id == $this->wilaya->id
            && ($this->stringsMatch($m->intitule, $row['moughataa'] ?? '') || $this->stringsMatch($m->intitule_fr, $row['moughataa'] ?? '')));
        if (!$moughata) {
            return null;
        }

        $commune = $this->communes->first(fn ($c) => $c->moughata_id == $moughata->id
            && ($this->stringsMatch($c->intitule, $row['commune'] ?? '') || $this->stringsMatch($c->intitule_fr, $row['commune'] ?? '')));
        if (!$commune) {
            return null;
        }

        $normLoc = $this->normalizeString($row['localite'] ?? '');

        $localite = $this->localites->first(fn ($l) => $l->commune_id == $commune->id
            && ($this->normalizeString($l->intitule) === $normLoc || $this->normalizeString($l->intitule_fr) === $normLoc));

        if (!$localite && !empty($row['code_ansade'])) {
            $codeAnsade = trim((string) $row['code_ansade']);
            $localite = $this->localites->first(fn ($l) => $l->commune_id == $commune->id && (string) $l->code_ansade === $codeAnsade);
        }

        // Same-moughata sibling-commune fallback: catches localities that a
        // later reorganisation moved to a different commune of the same
        // moughata, which this older transfer file doesn't know about yet.
        if (!$localite) {
            $siblingCommuneIds = $this->communes->where('moughata_id', $moughata->id)->pluck('id')->all();
            $localite = $this->localites->first(fn ($l) => in_array($l->commune_id, $siblingCommuneIds, true)
                && $this->normalizeString($l->intitule) === $normLoc);
        }

        if (!$localite) {
            return null;
        }

        return [$this->wilaya, $moughata, $commune, $localite];
    }

    private function findProjet(string $intitule): ?Projet
    {
        return $this->projets->first(fn ($p) => $this->stringsMatch($p->intitule_resume, $intitule));
    }

    private function findOperateur(string $intitule): ?Operateur
    {
        return $this->operateurs->first(fn ($o) => $this->stringsMatch($o->intitule, $intitule));
    }

    private function isPlaceholderValue($value): bool
    {
        $normalized = $this->normalizeString((string) $value);
        return $normalized === '' || in_array($normalized, ['inconnu', 'inconnue', 'nr', 'non renseigne', 'non renseignee'], true);
    }

    /**
     * @return array{0: array<string,string>, 1: int} [normalizedHeader => column letter, firstDataRow]
     */
    private function readHeader($sheet): array
    {
        $highestCol = $sheet->getHighestColumn();
        $headerRow = $sheet->rangeToArray('A1:' . $highestCol . '1')[0];
        $header = [];
        foreach ($headerRow as $i => $label) {
            if ($label === null || trim((string) $label) === '') {
                continue;
            }
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $header[$this->normalizeHeader((string) $label)] = $col;
        }

        return [$header, 2];
    }

    private function normalizeHeader(string $label): string
    {
        $label = mb_strtolower(trim($label), 'UTF-8');
        $search = ['é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'î', 'ï', 'ô', 'ö', 'ù', 'û', 'ü', 'ç'];
        $replace = ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'i', 'i', 'o', 'o', 'u', 'u', 'u', 'c'];
        $label = str_replace($search, $replace, $label);
        $label = preg_replace('/[^a-z0-9]+/', '_', $label);

        return trim($label, '_');
    }

    private function readRow($sheet, array $header, int $r): array
    {
        $row = [];
        foreach ($header as $key => $col) {
            $row[$key] = $sheet->getCell($col . $r)->getValue();
            if (is_string($row[$key])) {
                $row[$key] = trim($row[$key]);
            }
        }

        return $row;
    }

    private function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }
        if (is_numeric($value) && $value > 20000) {
            return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
        }
        $timestamp = strtotime((string) $value);

        return $timestamp !== false ? date('Y-m-d', $timestamp) : null;
    }

    private function toInt($value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    private function toBool($value): ?bool
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            return (bool) $value;
        }
        $normalized = $this->normalizeString((string) $value);

        return in_array($normalized, ['oui', 'yes', 'true', '1'], true) ? true : (in_array($normalized, ['non', 'no', 'false', '0'], true) ? false : null);
    }
}
