<?php

namespace App\Console\Commands;

use App\Models\Commune;
use App\Models\Localite;
use App\Models\LocaliteReport;
use App\Models\Moughata;
use App\Models\Operateur;
use App\Models\Projet;
use App\Models\Wilaya;
use App\Support\Concerns\NormalizesAdministrativeNames;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

/**
 * Imports the legacy household-level WASH operations (Déclenchement, Etat
 * des lieux, Certification FDAL, Dernier suivi) sitting in the 9 per-wilaya
 * "Transfert données {Wilaya} dans BADAM_xxxxxx.xlsx" files at the project
 * root into `localite_operations`. Those files predate the 2026-07-17
 * nomenclature migration and carry the OLD dashed ANSADE code + the OLD
 * wilaya/moughataa/commune hierarchy, so every row is re-resolved against
 * the CURRENT hierarchy rather than trusting the file's own admin columns:
 * localite is matched first via `ancien_code_ansade`, then via a
 * wilaya-scoped normalized-name fallback, and wilaya_id/moughata_id/
 * commune_id are always derived from the matched localite's current
 * parent chain, never from the file.
 *
 * Deliberately does not reuse app/Imports/BaseReportImport.php: that class
 * matches localite by exact `code_ansade` (which now holds the NEW code
 * post-migration, not these files' old dashed one) and resolves the
 * hierarchy top-down using the file's own moughata/commune names — the
 * opposite of what's needed here.
 *
 * Dry-run by default (rolled back transaction); --apply commits.
 */
class ImportLegacyLocaliteOperationsCommand extends Command
{
    use NormalizesAdministrativeNames;

    protected $signature = 'operations:import-legacy
                            {--wilaya= : Nom de wilaya (ex: ADRAR) ; toutes les 9 si omis}
                            {--apply : Applique réellement les changements (sans cette option : dry-run)}
                            {--report= : Chemin d\'un CSV où écrire le détail des lignes sautées/ambiguës}
                            {--user-id=1 : user_id attribué aux lignes importées}';

    protected $description = "Importe les opérations localité historiques des fichiers BADAM vers la nouvelle arborescence";

    /** @var array<string,string> wilaya intitule (DB) => nom de fichier xlsx */
    private const WILAYA_FILES = [
        'ADRAR' => 'Transfert données Adrar dans BADAM_181023.xlsx',
        'ASSABA' => 'Transfert données Assaba dans BADAM_010823.xlsx',
        'BRAKNA' => 'Transfert données Brakna dans BADAM_161023.xlsx',
        'GORGOL' => 'Transfert données Gorgol dans BADAM_021023.xlsx',
        'GUIDIMAKHA' => 'Transfert données Guidimagha dans BADAM_051023.xlsx',
        'HODH CHARGUI' => 'Transfert données HEC dans BADAM_301023.xlsx',
        'HODH GHARBI' => 'Transfert données HEG dans BADAM_301023.xlsx',
        'TAGANT' => 'Transfert données Tagant dans BADAM_101023.xlsx',
        'TRARZA' => 'Transfert données Trarza BADAM_181023.xlsx',
    ];

    /** @var array<string,int> sheet-name prefix (lowercased, spaces stripped) => operation_id */
    private const SHEET_PREFIX_OPERATION = [
        'op1-decl' => 1,
        'op2-edl' => 2,
        'op3-certif' => 3,
        'op4-derniersuivi' => 4,
    ];

    /** @var array<string, \App\Models\Localite> localite keyed by ancien_code_ansade */
    private array $localitesByAncienCode = [];
    /** @var \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<int, \App\Models\Localite>> localites grouped by wilaya_id */
    private \Illuminate\Support\Collection $localitesByWilaya;
    /** @var \Illuminate\Support\Collection<int, \App\Models\Commune> keyed by id */
    private \Illuminate\Support\Collection $communes;
    /** @var \Illuminate\Support\Collection<int, \App\Models\Moughata> keyed by id */
    private \Illuminate\Support\Collection $moughatas;
    /** @var \Illuminate\Support\Collection<int, \App\Models\Projet> */
    private \Illuminate\Support\Collection $projets;
    /** @var \Illuminate\Support\Collection<int, \App\Models\Operateur> */
    private \Illuminate\Support\Collection $operateurs;
    /** @var \App\Models\Projet|null the "NR" (Non renseigné) placeholder project */
    private ?Projet $nrProjet = null;
    /** @var \App\Models\Operateur|null the "NR" (Non renseigné) placeholder operateur */
    private ?Operateur $nrOperateur = null;

    /** @var array<string,true> in-run dedup guard */
    private array $seen = [];
    /** @var array<string,true> rows already present in `localite_operations` from a previous run of this command — makes re-running safe (e.g. after fixing the matcher) instead of duplicating what's already imported */
    private array $existingKeys = [];

    private array $stats = [
        'read' => 0,
        'inserted' => 0,
        'localite_not_found' => 0,
        'localite_ambiguous' => 0,
        'projet_not_found' => 0,
        'operateur_not_found' => 0,
        'duplicate_skipped' => 0,
        'already_in_db' => 0,
    ];

    /** @var array<int,array<string,mixed>> */
    private array $reportRows = [];

    public function handle(): int
    {
        $wilayaFilter = $this->option('wilaya') ? mb_strtoupper(trim($this->option('wilaya'))) : null;
        $userId = (int) $this->option('user-id');
        $apply = (bool) $this->option('apply');

        $wilayaNames = $wilayaFilter ? [$wilayaFilter] : array_keys(self::WILAYA_FILES);
        foreach ($wilayaNames as $name) {
            if (!isset(self::WILAYA_FILES[$name])) {
                $this->error("Wilaya inconnue: {$name}. Valeurs possibles: " . implode(', ', array_keys(self::WILAYA_FILES)));
                return self::FAILURE;
            }
        }

        $projectRoot = dirname(base_path());
        foreach ($wilayaNames as $name) {
            $path = $projectRoot . DIRECTORY_SEPARATOR . self::WILAYA_FILES[$name];
            if (!is_file($path)) {
                $this->error("Fichier introuvable pour {$name}: {$path}");
                return self::FAILURE;
            }
        }

        $this->loadReferenceData();

        DB::beginTransaction();
        try {
            foreach ($wilayaNames as $wilayaName) {
                $this->processWilaya($wilayaName, $projectRoot . DIRECTORY_SEPARATOR . self::WILAYA_FILES[$wilayaName], $userId);
            }

            $this->printStats();

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
            $this->writeReportCsv($absolutePath);
            $this->info("Rapport écrit dans {$absolutePath}");
        }

        return self::SUCCESS;
    }

    private function loadReferenceData(): void
    {
        $localites = Localite::select('id', 'intitule', 'intitule_fr', 'commune_id', 'code_ansade', 'ancien_code_ansade')->get();

        foreach ($localites as $localite) {
            if (!empty($localite->ancien_code_ansade)) {
                $this->localitesByAncienCode[(string) $localite->ancien_code_ansade] = $localite;
            }
        }

        $this->communes = Commune::select('id', 'intitule', 'intitule_fr', 'moughata_id')->get()->keyBy('id');
        $this->moughatas = Moughata::select('id', 'intitule', 'intitule_fr', 'wilaya_id')->get()->keyBy('id');

        // Group localites by wilaya_id (derived from commune->moughata->wilaya) for the name fallback.
        $this->localitesByWilaya = collect();
        foreach ($localites as $localite) {
            $commune = $this->communes->get($localite->commune_id);
            if (!$commune) {
                continue;
            }
            $moughata = $this->moughatas->get($commune->moughata_id);
            if (!$moughata) {
                continue;
            }
            $wilayaId = $moughata->wilaya_id;
            if (!$this->localitesByWilaya->has($wilayaId)) {
                $this->localitesByWilaya->put($wilayaId, collect());
            }
            $this->localitesByWilaya->get($wilayaId)->push($localite);
        }

        $this->projets = Projet::select('id', 'intitule_resume', 'intitule_complet')->get();
        $this->operateurs = Operateur::select('id', 'intitule')->get();
        $this->nrProjet = $this->projets->first(fn ($p) => $this->normalizeString($p->intitule_resume) === 'nr');
        $this->nrOperateur = $this->operateurs->first(fn ($o) => $this->normalizeString($o->intitule) === 'nr');

        foreach (LocaliteReport::select('localite_id', 'operation_id', 'projet_id', 'operateur_id', 'date_op')->get() as $existing) {
            $key = implode('|', [
                $existing->localite_id, $existing->operation_id, $existing->projet_id, $existing->operateur_id, $existing->date_op,
            ]);
            $this->existingKeys[$key] = true;
        }
    }

    private function processWilaya(string $wilayaName, string $path, int $userId): void
    {
        $wilaya = Wilaya::where('intitule', $wilayaName)->first();
        if (!$wilaya) {
            $this->error("Wilaya introuvable en base: {$wilayaName}");
            return;
        }

        $this->info("=== {$wilayaName} ({$path}) ===");

        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);

        foreach ($spreadsheet->getSheetNames() as $sheetName) {
            $operationId = $this->operationIdForSheet($sheetName);
            if ($operationId === null) {
                continue;
            }

            $sheet = $spreadsheet->getSheetByName($sheetName);
            $rows = $this->readSheetByHeader($sheet);

            $this->info("  - {$sheetName}: " . count($rows) . ' lignes');

            foreach ($rows as $row) {
                $this->stats['read']++;
                $this->processRow($row, $operationId, $wilaya, $userId, $wilayaName, $sheetName);
            }
        }
    }

    private function operationIdForSheet(string $sheetName): ?int
    {
        $key = str_replace(' ', '', mb_strtolower(trim($sheetName)));
        // Strip a trailing sub-sheet number/suffix like "1", "2", " (2)".
        $key = preg_replace('/\(\d+\)$/', '', $key);
        $key = preg_replace('/\d+$/', '', $key);

        foreach (self::SHEET_PREFIX_OPERATION as $prefix => $operationId) {
            if (str_starts_with($key, $prefix)) {
                return $operationId;
            }
        }

        return null;
    }

    /**
     * @return array<int,array<string,mixed>> rows keyed by Str::slug header name,
     *   matching the same keys app/Imports/*.php already rely on.
     */
    private function readSheetByHeader($sheet): array
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn());

        $headerMap = [];
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $value = trim((string) $sheet->getCellByColumnAndRow($col, 1)->getValue());
            if ($value === '') {
                continue;
            }
            $headerMap[Str::slug($value, '_')] = $col;
        }

        $rows = [];
        for ($r = 2; $r <= $highestRow; $r++) {
            $row = [];
            $hasData = false;
            foreach ($headerMap as $key => $col) {
                $cell = $sheet->getCellByColumnAndRow($col, $r);
                $value = $cell->getValue();
                if ($value !== null && $value !== '') {
                    $hasData = true;
                }
                $row[$key] = $value;
            }
            if ($hasData) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    private function processRow(array $row, int $operationId, Wilaya $wilaya, int $userId, string $wilayaName, string $sheetName): void
    {
        $localite = $this->resolveLocalite($row, $wilaya);
        if ($localite === null) {
            $this->stats['localite_not_found']++;
            $this->reportRows[] = $this->reportRow($wilayaName, $sheetName, $row, 'localite_introuvable');
            return;
        }
        if ($localite === false) {
            $this->stats['localite_ambiguous']++;
            $this->reportRows[] = $this->reportRow($wilayaName, $sheetName, $row, 'localite_ambigue');
            return;
        }

        $commune = $this->communes->get($localite->commune_id);
        $moughata = $commune ? $this->moughatas->get($commune->moughata_id) : null;
        if (!$commune || !$moughata) {
            $this->stats['localite_not_found']++;
            $this->reportRows[] = $this->reportRow($wilayaName, $sheetName, $row, 'hierarchie_incoherente');
            return;
        }

        $projetId = null;
        $projetRaw = trim((string) ($row['int_projet_resum'] ?? ''));
        if ($this->isPlaceholderValue($projetRaw)) {
            if (!$this->nrProjet) {
                $this->stats['projet_not_found']++;
                $this->reportRows[] = $this->reportRow($wilayaName, $sheetName, $row, 'projet_vide_et_pas_de_nr');
                return;
            }
            $projetId = $this->nrProjet->id;
        } else {
            $projet = $this->findProjet($projetRaw);
            if (!$projet) {
                $this->stats['projet_not_found']++;
                $this->reportRows[] = $this->reportRow($wilayaName, $sheetName, $row, "projet_introuvable: {$projetRaw}");
                return;
            }
            $projetId = $projet->id;
        }

        $operateurId = null;
        $operateurRaw = trim((string) ($row['intitule_se'] ?? ''));
        if ($this->isPlaceholderValue($operateurRaw)) {
            if (!$this->nrOperateur) {
                $this->stats['operateur_not_found']++;
                $this->reportRows[] = $this->reportRow($wilayaName, $sheetName, $row, 'operateur_vide_et_pas_de_nr');
                return;
            }
            $operateurId = $this->nrOperateur->id;
        } else {
            $operateur = $this->findOperateur($operateurRaw, $wilayaName);
            if (!$operateur) {
                $this->stats['operateur_not_found']++;
                $this->reportRows[] = $this->reportRow($wilayaName, $sheetName, $row, "operateur_introuvable: {$operateurRaw}");
                return;
            }
            $operateurId = $operateur->id;
        }

        $dateOp = $this->convertDate($row['date_op'] ?? null);

        $dedupKey = implode('|', [$localite->id, $operationId, $projetId, $operateurId, $dateOp]);
        if (isset($this->seen[$dedupKey])) {
            $this->stats['duplicate_skipped']++;
            $this->reportRows[] = $this->reportRow($wilayaName, $sheetName, $row, 'doublon_intra_run');
            return;
        }
        if (isset($this->existingKeys[$dedupKey])) {
            $this->stats['already_in_db']++;
            $this->seen[$dedupKey] = true;
            return;
        }
        $this->seen[$dedupKey] = true;

        $baseData = [
            'localite_id' => $localite->id,
            'commune_id' => $commune->id,
            'moughata_id' => $moughata->id,
            'wilaya_id' => $moughata->wilaya_id,
            'operation_id' => $operationId,
            'projet_id' => $projetId,
            'operateur_id' => $operateurId,
            'user_id' => $userId,
            'date_op' => $dateOp,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $specificData = $this->specificDataForOperation($operationId, $row);

        LocaliteReport::create(array_merge($baseData, $specificData));
        $this->stats['inserted']++;
    }

    /**
     * @return Localite|null|false null = introuvable, false = ambigu
     */
    private function resolveLocalite(array $row, Wilaya $wilaya)
    {
        $rawCode = trim((string) ($row['code_ansade'] ?? ''));
        if ($rawCode !== '') {
            $ancienCode = ltrim(str_replace('-', '', $rawCode), '0');
            if ($ancienCode !== '' && isset($this->localitesByAncienCode[$ancienCode])) {
                return $this->localitesByAncienCode[$ancienCode];
            }
        }

        $candidates = $this->localitesByWilaya->get($wilaya->id, collect());
        $nameTarget = trim((string) ($row['localite'] ?? ''));
        if ($nameTarget === '') {
            return null;
        }

        // Prefer an exact normalized-name match first. stringsMatch() below
        // is substring/similarity based, so searching for "Rag" also pulls
        // in "Rag Veroune", "Rag Izeghrane", "Rag ould el Weli", etc., and
        // "Eljedida" pulls in "Jweilatt Eljedida" and the near-miss "El
        // Jedida" — turning an otherwise single, exact, unambiguous match
        // into a spurious tie. Only fall back to the fuzzy set when nothing
        // matches exactly.
        $normTarget = $this->normalizeString($nameTarget);
        $exactMatches = $candidates->filter(function ($item) use ($normTarget) {
            return $this->normalizeString($item->intitule) === $normTarget
                || $this->normalizeString($item->intitule_fr) === $normTarget;
        });

        $matches = $exactMatches->isNotEmpty() ? $exactMatches : $candidates->filter(function ($item) use ($nameTarget) {
            return $this->stringsMatch($item->intitule, $nameTarget)
                || $this->stringsMatch($item->intitule_fr, $nameTarget);
        });

        if ($matches->isEmpty()) {
            return null;
        }
        if ($matches->count() === 1) {
            return $matches->first();
        }

        // Ambiguous within the wilaya: disambiguate using the file's own
        // (old) commune name as a similarity tiebreaker against each
        // candidate's current commune name.
        $communeHint = trim((string) ($row['commune'] ?? ''));
        if ($communeHint !== '') {
            $scored = $matches->map(function ($item) use ($communeHint) {
                $commune = $this->communes->get($item->commune_id);
                $percent = 0;
                if ($commune) {
                    similar_text($this->normalizeString($commune->intitule), $this->normalizeString($communeHint), $percent);
                }
                return ['localite' => $item, 'score' => $percent];
            })->sortByDesc('score')->values();

            if ($scored->count() >= 1 && $scored[0]['score'] >= 80 && (count($scored) === 1 || $scored[0]['score'] - $scored[1]['score'] >= 15)) {
                return $scored[0]['localite'];
            }
        }

        return false;
    }

    /**
     * Vrai pour une valeur vide ou un placeholder de type "Inconnu"/"NR"/
     * "Non renseigné" — ces lignes doivent retomber sur l'entité "NR"
     * plutôt qu'être rejetées.
     */
    private function isPlaceholderValue(string $value): bool
    {
        $normalized = $this->normalizeString($value);
        return $normalized === '' || in_array($normalized, ['inconnu', 'inconnue', 'nr', 'non renseigne', 'non renseignee'], true);
    }

    private function findProjet(string $raw)
    {
        $normTarget = $this->normalizeString($raw);
        $exact = $this->projets->first(function ($item) use ($normTarget) {
            return $this->normalizeString($item->intitule_resume) === $normTarget
                || $this->normalizeString($item->intitule_complet) === $normTarget;
        });
        if ($exact) {
            return $exact;
        }

        return $this->projets->first(function ($item) use ($raw) {
            return $this->stringsMatch($item->intitule_resume, $raw) || $this->stringsMatch($item->intitule_complet, $raw);
        });
    }

    private function findOperateur(string $raw, string $wilayaName)
    {
        // Prefer an exact normalized-name match first — stringsMatch()'s
        // substring/similarity fallback below matches "Nafore" against both
        // "NAFORE" and "GRDR-NAFORE" (which ends with "nafore"), turning an
        // exact single match into a spurious ambiguity.
        $normTarget = $this->normalizeString($raw);
        $exactMatches = $this->operateurs->filter(function ($item) use ($normTarget) {
            return $this->normalizeString($item->intitule) === $normTarget;
        });

        $matches = $exactMatches->isNotEmpty() ? $exactMatches : $this->operateurs->filter(function ($item) use ($raw) {
            return $this->stringsMatch($item->intitule, $raw);
        });

        if ($matches->isEmpty()) {
            return null;
        }
        if ($matches->count() === 1) {
            return $matches->first();
        }

        // Ambiguous bare names like "DRHA"/"CRS" exist once per wilaya
        // ("DRHA ADRAR", "DRHA ASSABA", ...) — prefer the one naming the
        // wilaya currently being processed.
        $withWilaya = $matches->filter(function ($item) use ($wilayaName) {
            return stripos($item->intitule, $wilayaName) !== false;
        });

        if ($withWilaya->count() === 1) {
            return $withWilaya->first();
        }

        return null;
    }

    private function convertDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        $timestamp = strtotime((string) $value);
        return $timestamp !== false ? date('Y-m-d', $timestamp) : null;
    }

    private function specificDataForOperation(int $operationId, array $row): array
    {
        switch ($operationId) {
            case 2: // Etat des lieux — mirrors App\Imports\EtatLieuxImport::getSpecificData()
                return [
                    'nbr_h_c' => $row['nb_hom_compt'] ?? null,
                    'nbr_f_c' => $row['nb_fem_compt'] ?? null,
                    'nbr_enfant_inf_5_ans' => $row['nb_enf_inf_5_ans_compt'] ?? null,
                    'nb_tot_pop' => $row['nb_tot_pop_compt'] ?? null,
                    'nb_menages_comptabilises' => $row['nb_menages_compt'] ?? null,
                    'approv_eau' => $row['approv_eau_loc'] ?? null,
                    'nb_tot_latr_fam' => $row['nb_tot_latr_fam'] ?? null,
                    'nb_latrine_amelior' => $row['nbr_latr_amel'] ?? null,
                    'nb_latrine_non_amelior' => $row['nb_latrine_non_amelior'] ?? null,
                    'nbr_menages_latr_amel_inadeq' => $row['nb_men_latr_amel_vid_inadeq'] ?? null,
                    'nbr_menages_latr_amel_adeq' => $row['nb_men_latr_amel_vid_adeq'] ?? null,
                    'nbr_menages_ltr_vsn' => $row['nbr_men_latr_voisin'] ?? null,
                    'nbr_menages_latr_amel_inadeq_part' => $row['nb_men_latr_amel_vid_inadeq_part'] ?? null,
                    'nbr_menages_latr_amel_vid_adeq_part' => $row['nb_men_latr_amel_vid_adeq_part'] ?? null,
                    'nbr_menages_ltr_end_phm' => $row['nb_men_latr_endommag'] ?? null,
                    'nbr_menages_dal' => $row['nb_men_dal'] ?? null,
                    'nbr_menages_dlm' => $row['nb_latr_dlm'] ?? null,
                    'nbr_menages_dlm_complet' => $row['nb_men_dlm_complet'] ?? null,
                    'nbr_menages_dlm_incomplet' => $row['nb_men_dlm_incomplet'] ?? null,
                    'nbr_menages_sans_dlm' => $row['nb_men_sans_dlm'] ?? null,
                ];

            case 4: // Dernier suivi — mirrors App\Imports\DernierSuiviImport::getSpecificData() (no comptage fields, those are EDL-only)
                return [
                    'nb_tot_latr_fam' => $row['nb_tot_latr_fam'] ?? null,
                    'nb_latrine_amelior' => $row['nbr_latr_amel'] ?? null,
                    'nb_latrine_non_amelior' => $row['nb_latrine_non_amelior'] ?? null,
                    'nbr_menages_latr_amel_inadeq' => $row['nb_men_latr_amel_vid_inadeq'] ?? null,
                    'nbr_menages_latr_amel_adeq' => $row['nb_men_latr_amel_vid_adeq'] ?? null,
                    'nbr_menages_ltr_vsn' => $row['nbr_men_latr_voisin'] ?? null,
                    'nbr_menages_latr_amel_inadeq_part' => $row['nb_men_latr_amel_vid_inadeq_part'] ?? null,
                    'nbr_menages_latr_amel_vid_adeq_part' => $row['nb_men_latr_amel_vid_adeq_part'] ?? null,
                    'nbr_menages_ltr_end_phm' => $row['nb_men_latr_endommag'] ?? null,
                    'nbr_menages_dal' => $row['nb_men_dal'] ?? null,
                    'nbr_menages_dlm' => $row['nb_latr_dlm'] ?? null,
                    'nbr_menages_dlm_complet' => $row['nb_men_dlm_complet'] ?? null,
                    'nbr_menages_dlm_incomplet' => $row['nb_men_dlm_incomplet'] ?? null,
                    'nbr_menages_sans_dlm' => $row['nb_men_sans_dlm'] ?? null,
                    'nbr_latrine_realise' => $row['nb_latr_realise'] ?? null,
                    'nbr_latrine_autoconst' => $row['nb_latr_autoconst'] ?? null,
                    'nbr_latrine_realise_macon' => $row['nb_latr_realise_macon'] ?? null,
                    'nbr_latrine_entraid' => $row['nb_latr_entraid'] ?? null,
                    'nbr_latrine_subv' => $row['nb_latr_subv'] ?? null,
                    'nbr_latrine_en_constr' => $row['nb_latr_en_constr'] ?? null,
                    'nbr_latrine_autofin' => $row['nb_latr_autofin'] ?? null,
                    'mont_invest_men' => $row['mont_invest_men'] ?? null,
                    'nb_latr_invest_men_dispo' => $row['nb_latr_invest_men_dispo'] ?? null,
                ];

            case 1: // Déclenchement — mirrors DeclenchementImport (date_op only, handled in processRow)
            case 3: // Certification FDAL — mirrors CertificationImport (date_op only, handled in processRow)
            default:
                return [];
        }
    }

    private function reportRow(string $wilaya, string $sheet, array $row, string $reason): array
    {
        return [
            'wilaya' => $wilaya,
            'sheet' => $sheet,
            'localite' => $row['localite'] ?? '',
            'code_ansade' => $row['code_ansade'] ?? '',
            'commune' => $row['commune'] ?? '',
            'int_projet_resum' => $row['int_projet_resum'] ?? '',
            'intitule_se' => $row['intitule_se'] ?? '',
            'reason' => $reason,
        ];
    }

    private function writeReportCsv(string $path): void
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $handle = fopen($path, 'w');
        fputcsv($handle, ['wilaya', 'sheet', 'localite', 'code_ansade', 'commune', 'int_projet_resum', 'intitule_se', 'reason']);
        foreach ($this->reportRows as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
    }

    private function printStats(): void
    {
        $this->newLine();
        $this->info('--- Résumé ---');
        foreach ($this->stats as $key => $value) {
            $this->line("  {$key}: {$value}");
        }
    }
}
