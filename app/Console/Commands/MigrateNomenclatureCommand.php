<?php

namespace App\Console\Commands;

use App\Models\Commune;
use App\Models\Localite;
use App\Models\LocaliteReport;
use App\Models\Moughata;
use App\Models\Wilaya;
use App\Support\AncienNouveauCodesParser;
use App\Support\Concerns\NormalizesAdministrativeNames;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

/**
 * Rebuilds the wilaya -> moughata -> commune -> localite hierarchy from the
 * "ancien/nouveau codes" reference file: creates the moughataa/communes that
 * don't exist yet, re-links existing ones to their correct parent (this also
 * self-heals communes that a previous, buggy import mis-linked by matching
 * on `code` alone instead of scoping by wilaya), and recodes every localite
 * it can confidently match by name.
 *
 * Runs as a dry-run by default: everything below runs for real inside a DB
 * transaction (so newly created moughataa/communes are immediately visible
 * to later rows in the same pass) and is rolled back at the end unless
 * --apply is given. Note: MySQL does not roll back AUTO_INCREMENT counters,
 * so a dry-run permanently burns a few ids — harmless, just cosmetic gaps.
 */
class MigrateNomenclatureCommand extends Command
{
    use NormalizesAdministrativeNames;

    protected $signature = 'localites:migrate-nomenclature
                            {--apply : Applique réellement les changements (sans cette option : dry-run)}
                            {--file= : Chemin du fichier de référence (défaut : Ancien_et_nouveau_codes.md à la racine du projet)}
                            {--report= : Chemin d\'un CSV où écrire le détail des localités non affectées automatiquement}';

    protected $description = "Reconstruit la hiérarchie wilaya/moughata/commune/localite à partir du fichier de référence ancien/nouveau codes";

    /** @var \Illuminate\Support\Collection */
    private $wilayas;
    /** @var \Illuminate\Support\Collection */
    private $moughatas;
    /** @var \Illuminate\Support\Collection */
    private $communes;
    /** @var \Illuminate\Support\Collection */
    private $localites;
    /** @var \Illuminate\Support\Collection keyed by commune_id, each value a Collection of localites */
    private $localitesByCommune;
    /** @var \Illuminate\Support\Collection keyed by wilaya_id, each value a Collection of localites */
    private $localitesByWilaya;

    private array $moughataCache = [];

    public function handle(): int
    {
        $filePath = $this->option('file') ?: $this->resolveDefaultFilePath();

        if (!is_file($filePath)) {
            $this->error("Fichier de référence introuvable: {$filePath}");
            return self::FAILURE;
        }

        $rows = str_ends_with(strtolower($filePath), '.xlsx')
            ? $this->readXlsxCodes($filePath)
            : (new AncienNouveauCodesParser())->parse($filePath);
        $this->info(count($rows) . ' lignes lues dans le fichier de référence (' . basename($filePath) . ').');

        $this->wilayas = Wilaya::all();
        $this->moughatas = Moughata::all();
        $this->communes = Commune::all();
        $this->localites = Localite::select(['id', 'intitule', 'intitule_fr', 'commune_id', 'moughata_id', 'wilaya_id'])->get();
        // resolveLocalite() is called once per reference row (thousands) and
        // needs to scan "localites in this commune/wilaya" — grouping up
        // front turns that from an O(rows * localites) full-collection scan
        // per row into an O(rows * localites-in-that-commune) lookup.
        $this->localitesByCommune = $this->localites->groupBy('commune_id');
        $this->localitesByWilaya = $this->localites->groupBy('wilaya_id');

        $stats = [
            'moughata_create' => 0, 'moughata_ok' => 0,
            'commune_create' => 0, 'commune_fixed' => 0, 'commune_ok' => 0,
            'localite_moved' => 0, 'localite_ok' => 0, 'localite_unmatched' => 0,
            'wilaya_not_found' => 0,
        ];
        $moughataCreations = [];
        $communeChanges = [];
        $unmatched = [];

        $apply = (bool) $this->option('apply');

        DB::beginTransaction();
        try {
            $bar = $this->output->createProgressBar(count($rows));
            $bar->start();

            foreach ($rows as $row) {
                $wilaya = $this->findWilaya($row['wilaya']);
                if (!$wilaya) {
                    $stats['wilaya_not_found']++;
                    $bar->advance();
                    continue;
                }

                $moughata = $this->resolveMoughata($row['moughataa'], $wilaya, $stats, $moughataCreations);
                $commune = $this->resolveCommune($row['commune'], $moughata, $wilaya, $stats, $communeChanges);
                $this->resolveLocalite($row, $commune, $moughata, $wilaya, $stats, $unmatched);

                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);

            $this->printReport($stats, $moughataCreations, $communeChanges, $unmatched);

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
            $this->writeUnmatchedCsv($absolutePath, $unmatched);
            $this->info("Rapport des localités non affectées écrit dans {$absolutePath}");
        }

        return self::SUCCESS;
    }

    private function resolveDefaultFilePath(): string
    {
        $root = dirname(base_path());
        $xlsxPath = $root . DIRECTORY_SEPARATOR . 'Ancien et nouveau codes.xlsx';

        return is_file($xlsxPath) ? $xlsxPath : $root . DIRECTORY_SEPARATOR . 'Ancien_et_nouveau_codes.md';
    }

    /**
     * @return array<int, array{wilaya:string,moughataa:string,commune:string,ancien_code:string,nouveau_code:string,nom_localite:string,population:?int}>
     */
    private function readXlsxCodes(string $path): array
    {
        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $sheet = $reader->load($path)->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        $rows = [];
        for ($r = 2; $r <= $highestRow; $r++) {
            $wilaya = trim((string) $sheet->getCell('A' . $r)->getValue());
            if ($wilaya === '') {
                continue;
            }

            $population = $sheet->getCell('G' . $r)->getValue();
            $rows[] = [
                'wilaya' => $wilaya,
                'moughataa' => trim((string) $sheet->getCell('B' . $r)->getValue()),
                'commune' => trim((string) $sheet->getCell('C' . $r)->getValue()),
                'ancien_code' => trim((string) $sheet->getCell('D' . $r)->getValue()),
                'nouveau_code' => trim((string) $sheet->getCell('E' . $r)->getValue()),
                'nom_localite' => trim((string) $sheet->getCell('F' . $r)->getValue()),
                'population' => is_numeric($population) ? (int) $population : null,
            ];
        }

        return $rows;
    }

    private function resolveMoughata(string $name, Wilaya $wilaya, array &$stats, array &$creations): Moughata
    {
        $key = $wilaya->id . '|' . $this->normalizeString($name);
        if (isset($this->moughataCache[$key])) {
            return $this->moughataCache[$key];
        }

        $moughata = $this->findMoughata($name, $wilaya->id);
        if ($moughata) {
            $stats['moughata_ok']++;
            return $this->moughataCache[$key] = $moughata;
        }

        $nextCode = ((int) $this->moughatas->where('wilaya_id', $wilaya->id)->max('code')) + 1;
        $moughata = Moughata::create([
            'intitule' => $name,
            'intitule_fr' => $name,
            'code' => $nextCode,
            'wilaya_id' => $wilaya->id,
        ]);
        $this->moughatas->push($moughata);
        $stats['moughata_create']++;
        $creations[] = ['wilaya' => $wilaya->intitule, 'moughataa' => $name, 'code' => $nextCode];

        return $this->moughataCache[$key] = $moughata;
    }

    private function resolveCommune(string $name, Moughata $moughata, Wilaya $wilaya, array &$stats, array &$changes): Commune
    {
        // Already correctly linked under the resolved moughata.
        $commune = $this->findCommune($name, $moughata->id);
        if ($commune) {
            $stats['commune_ok']++;
            return $commune;
        }

        // Fallback: same wilaya, regardless of its current moughata_id. This is
        // what lets us find (and repair) communes a previous import mis-linked
        // to the wrong moughata — their wilaya_id stayed correct.
        $commune = $this->communes->first(function ($item) use ($name, $wilaya) {
            return $item->wilaya_id == $wilaya->id && $this->stringsMatch($item->intitule, $name);
        });

        if ($commune) {
            if ($commune->moughata_id != $moughata->id) {
                $changes[] = [
                    'commune' => $commune->intitule,
                    'wilaya' => $wilaya->intitule,
                    'ancien_moughata_id' => $commune->moughata_id,
                    'nouveau_moughata' => $moughata->intitule,
                ];
                $commune->update(['moughata_id' => $moughata->id, 'wilaya_id' => $wilaya->id]);
                Localite::where('commune_id', $commune->id)->update([
                    'moughata_id' => $moughata->id,
                    'wilaya_id' => $wilaya->id,
                ]);
                // Cascade to any operations already recorded against
                // localites in this commune (localite_operations denormalizes
                // moughata_id/wilaya_id alongside localite_id/commune_id).
                LocaliteReport::where('commune_id', $commune->id)->update([
                    'moughata_id' => $moughata->id,
                    'wilaya_id' => $wilaya->id,
                ]);
                $stats['commune_fixed']++;
            } else {
                $stats['commune_ok']++;
            }

            return $commune;
        }

        $nextCode = ((int) $this->communes->where('moughata_id', $moughata->id)->max('code')) + 1;
        $commune = Commune::create([
            'intitule' => $name,
            'intitule_fr' => $name,
            'code' => $nextCode,
            'moughata_id' => $moughata->id,
            'wilaya_id' => $wilaya->id,
        ]);
        $this->communes->push($commune);
        $stats['commune_create']++;

        return $commune;
    }

    private function resolveLocalite(array $row, Commune $commune, Moughata $moughata, Wilaya $wilaya, array &$stats, array &$unmatched): void
    {
        $normTarget = $this->normalizeString($row['nom_localite']);

        $localite = ($this->localitesByCommune->get($commune->id) ?? collect())->first(function ($item) use ($normTarget) {
            return $this->normalizeString($item->intitule) === $normTarget
                || $this->normalizeString($item->intitule_fr) === $normTarget;
        });

        // Fallback: same wilaya, any commune — catches localites still sitting
        // under a commune that hadn't been (or wasn't yet) correctly re-linked.
        if (!$localite) {
            $localite = ($this->localitesByWilaya->get($wilaya->id) ?? collect())->first(function ($item) use ($normTarget) {
                return $this->normalizeString($item->intitule) === $normTarget
                    || $this->normalizeString($item->intitule_fr) === $normTarget;
            });
        }

        if (!$localite) {
            $stats['localite_unmatched']++;
            $unmatched[] = [
                'wilaya' => $wilaya->intitule,
                'moughataa' => $moughata->intitule,
                'commune' => $commune->intitule,
                'nom_localite' => $row['nom_localite'],
                'ancien_code' => $row['ancien_code'],
                'nouveau_code' => $row['nouveau_code'],
                'population' => $row['population'],
            ];
            return;
        }

        $moved = $localite->commune_id != $commune->id
            || $localite->moughata_id != $moughata->id
            || $localite->wilaya_id != $wilaya->id;

        $localite->update([
            'commune_id' => $commune->id,
            'moughata_id' => $moughata->id,
            'wilaya_id' => $wilaya->id,
            'code_ansade' => $row['nouveau_code'],
            'ancien_code_ansade' => $row['ancien_code'],
        ]);

        if ($moved) {
            // Any localite_operations rows already recorded against this
            // localite (e.g. from operations:import-legacy-prereorg, run
            // BEFORE this reorganization) need their denormalized
            // commune_id/moughata_id/wilaya_id moved along with it.
            LocaliteReport::where('localite_id', $localite->id)->update([
                'commune_id' => $commune->id,
                'moughata_id' => $moughata->id,
                'wilaya_id' => $wilaya->id,
            ]);
        }

        $moved ? $stats['localite_moved']++ : $stats['localite_ok']++;
    }

    private function findWilaya(string $name): ?Wilaya
    {
        if (empty($name)) {
            return null;
        }

        return $this->wilayas->first(function ($item) use ($name) {
            return $this->stringsMatch($item->intitule, $name) || $this->stringsMatch($item->intitule_fr, $name);
        });
    }

    private function findMoughata(string $name, int $wilayaId): ?Moughata
    {
        return $this->moughatas->first(function ($item) use ($name, $wilayaId) {
            $match = $this->stringsMatch($item->intitule, $name) || $this->stringsMatch($item->intitule_fr, $name);
            return $match && $item->wilaya_id == $wilayaId;
        });
    }

    private function findCommune(string $name, int $moughataId): ?Commune
    {
        return $this->communes->first(function ($item) use ($name, $moughataId) {
            $match = $this->stringsMatch($item->intitule, $name) || $this->stringsMatch($item->intitule_fr, $name);
            return $match && $item->moughata_id == $moughataId;
        });
    }

    private function printReport(array $stats, array $moughataCreations, array $communeChanges, array $unmatched): void
    {
        $this->table(['Indicateur', 'Valeur'], [
            ['Wilaya introuvable (ligne ignorée)', $stats['wilaya_not_found']],
            ['Moughataa déjà correctes', $stats['moughata_ok']],
            ['Moughataa créées', $stats['moughata_create']],
            ['Communes déjà correctes', $stats['commune_ok']],
            ['Communes créées', $stats['commune_create']],
            ['Communes corrigées (mauvais moughata_id)', $stats['commune_fixed']],
            ['Localités déjà correctes (juste recodées)', $stats['localite_ok']],
            ['Localités déplacées vers leur nouveau parent', $stats['localite_moved']],
            ['Localités non trouvées (à affecter manuellement)', $stats['localite_unmatched']],
        ]);

        if (!empty($moughataCreations)) {
            $this->comment('Nouvelles moughataa :');
            $this->table(['Wilaya', 'Moughataa', 'Code'], $moughataCreations);
        }

        if (!empty($communeChanges)) {
            $this->comment('Communes corrigées :');
            $this->table(['Commune', 'Wilaya', 'Ancien moughata_id', 'Nouvelle moughataa'], $communeChanges);
        }

        if (!empty($unmatched)) {
            $this->warn(count($unmatched) . " localités n'ont pas pu être affectées automatiquement (voir --report= pour le détail, ou l'écran d'affectation manuelle).");
        }
    }

    private function writeUnmatchedCsv(string $path, array $unmatched): void
    {
        $fh = fopen($path, 'w');
        fputcsv($fh, ['wilaya', 'moughataa', 'commune', 'nom_localite', 'ancien_code', 'nouveau_code', 'population']);
        foreach ($unmatched as $row) {
            fputcsv($fh, $row);
        }
        fclose($fh);
    }
}
