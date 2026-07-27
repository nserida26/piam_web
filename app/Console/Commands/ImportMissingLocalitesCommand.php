<?php

namespace App\Console\Commands;

use App\Models\Commune;
use App\Models\Localite;
use App\Models\Moughata;
use App\Models\Wilaya;
use App\Support\AncienNouveauCodesParser;
use App\Support\Concerns\NormalizesAdministrativeNames;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

/**
 * Fills in localites that are present in all_localites.xlsx but missing from
 * the database (e.g. Adrar was never imported at all) — this only ever
 * creates rows that don't already exist and, for rows that do already exist,
 * only ever touches lat_loc/long_loc when the stored value isn't numeric.
 * It never deletes or re-links anything, so it's safe to run alongside (and
 * after) localites:migrate-nomenclature without undoing its work.
 *
 * Some source coordinates are DMS strings (e.g. N16°39'28,0") instead of
 * decimal degrees; those are converted to a decimal double on the way in.
 *
 * Runs as a dry-run by default (rolled back transaction); --apply commits.
 */
class ImportMissingLocalitesCommand extends Command
{
    use NormalizesAdministrativeNames;

    protected $signature = 'localites:import-missing
                            {--apply : Applique réellement les changements (sans cette option : dry-run)}
                            {--file= : Chemin du fichier all_localites.xlsx (défaut : à la racine du projet)}
                            {--report= : Chemin d\'un CSV où écrire le détail des localités créées et des coordonnées invalides}';

    protected $description = "Ajoute les localités présentes dans all_localites.xlsx mais absentes de la base (ex: Adrar)";

    private $wilayas;
    private $moughatas;
    private $communes;
    private $localites;
    /** @var \Illuminate\Support\Collection keyed by code_ansade */
    private $localitesByCode;
    /** @var \Illuminate\Support\Collection keyed by ancien_code_ansade */
    private $localitesByAncienCode;

    private array $moughataCache = [];
    private array $communeCache = [];
    /** @var array<string, array{ancien_code:string, nouveau_code:string}> */
    private array $codeIndex = [];

    public function handle(): int
    {
        $filePath = $this->option('file') ?: dirname(base_path()) . DIRECTORY_SEPARATOR . 'all_localites.xlsx';

        if (!is_file($filePath)) {
            $this->error("Fichier introuvable: {$filePath}");
            return self::FAILURE;
        }

        $this->buildCodeIndex();

        $rows = $this->readXlsx($filePath);
        $this->info(count($rows) . ' lignes lues dans ' . basename($filePath) . '.');

        $this->wilayas = Wilaya::all();
        $this->moughatas = Moughata::all();
        $this->communes = Commune::all();
        $this->localites = Localite::select(['id', 'intitule', 'intitule_fr', 'commune_id', 'wilaya_id', 'lat_loc', 'long_loc', 'code_ansade', 'ancien_code_ansade'])->get();
        $this->localitesByCode = $this->localites->filter(fn($item) => !empty($item->code_ansade))->keyBy('code_ansade');
        $this->localitesByAncienCode = $this->localites->filter(fn($item) => !empty($item->ancien_code_ansade))->keyBy('ancien_code_ansade');

        $stats = [
            'wilaya_not_found' => 0,
            'moughata_create' => 0,
            'commune_create' => 0,
            'localite_created' => 0,
            'localite_existing_ok' => 0,
            'localite_existing_elsewhere' => 0,
            'localite_coords_fixed' => 0,
            'coord_unparseable' => 0,
        ];
        $created = [];
        $coordIssues = [];

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

                $moughata = $this->resolveMoughata($row['moughataa'], $wilaya, $stats);
                $commune = $this->resolveCommune($row['commune'], $moughata, $wilaya, $stats);
                $this->resolveLocalite($row, $commune, $moughata, $wilaya, $stats, $created, $coordIssues);

                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);

            $this->printReport($stats);

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
            $this->writeReportCsv($absolutePath, $created, $coordIssues);
            $this->info("Rapport écrit dans {$absolutePath}");
        }

        return self::SUCCESS;
    }

    /**
     * @return array<int, array{wilaya:string,moughataa:string,commune:string,localite:string,code_loc:?string,code_ansade:?string,long_loc:mixed,lat_loc:mixed,nb_pop_ansade:mixed}>
     */
    private function readXlsx(string $path): array
    {
        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        $rows = [];
        for ($r = 2; $r <= $highestRow; $r++) {
            $wilaya = trim((string) $sheet->getCell('A' . $r)->getValue());
            if ($wilaya === '') {
                continue;
            }

            $rows[] = [
                'wilaya' => $wilaya,
                'moughataa' => trim((string) $sheet->getCell('C' . $r)->getValue()),
                'commune' => trim((string) $sheet->getCell('E' . $r)->getValue()),
                'localite' => trim((string) $sheet->getCell('G' . $r)->getValue()),
                'code_loc' => trim((string) $sheet->getCell('H' . $r)->getValue()) ?: null,
                'code_ansade' => trim((string) $sheet->getCell('I' . $r)->getValue()) ?: null,
                'long_loc' => $sheet->getCell('J' . $r)->getValue(),
                'lat_loc' => $sheet->getCell('K' . $r)->getValue(),
                'nb_pop_ansade' => $sheet->getCell('L' . $r)->getValue(),
            ];
        }

        return $rows;
    }

    private function buildCodeIndex(): void
    {
        $refPath = dirname(base_path()) . DIRECTORY_SEPARATOR . 'Ancien_et_nouveau_codes.md';
        if (!is_file($refPath)) {
            return;
        }

        foreach ((new AncienNouveauCodesParser())->parse($refPath) as $row) {
            $key = $this->normalizeString($row['wilaya']) . '|' . $this->normalizeString($row['commune']) . '|' . $this->normalizeString($row['nom_localite']);
            $this->codeIndex[$key] = ['ancien_code' => $row['ancien_code'], 'nouveau_code' => $row['nouveau_code']];
        }
    }

    private function resolveMoughata(string $name, Wilaya $wilaya, array &$stats): Moughata
    {
        $key = $wilaya->id . '|' . $this->normalizeString($name);
        if (isset($this->moughataCache[$key])) {
            return $this->moughataCache[$key];
        }

        $moughata = $this->moughatas->first(function ($item) use ($name, $wilaya) {
            $match = $this->stringsMatch($item->intitule, $name) || $this->stringsMatch($item->intitule_fr, $name);
            return $match && $item->wilaya_id == $wilaya->id;
        });

        if ($moughata) {
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

        return $this->moughataCache[$key] = $moughata;
    }

    private function resolveCommune(string $name, Moughata $moughata, Wilaya $wilaya, array &$stats): Commune
    {
        $key = $moughata->id . '|' . $this->normalizeString($name);
        if (isset($this->communeCache[$key])) {
            return $this->communeCache[$key];
        }

        $commune = $this->communes->first(function ($item) use ($name, $moughata) {
            $match = $this->stringsMatch($item->intitule, $name) || $this->stringsMatch($item->intitule_fr, $name);
            return $match && $item->moughata_id == $moughata->id;
        });

        if (!$commune) {
            $commune = $this->communes->first(function ($item) use ($name, $wilaya) {
                return $item->wilaya_id == $wilaya->id && $this->stringsMatch($item->intitule, $name);
            });
        }

        if ($commune) {
            return $this->communeCache[$key] = $commune;
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

        return $this->communeCache[$key] = $commune;
    }

    private function resolveLocalite(array $row, Commune $commune, Moughata $moughata, Wilaya $wilaya, array &$stats, array &$created, array &$coordIssues): void
    {
        $normTarget = $this->normalizeString($row['localite']);
        $existingElsewhere = false;

        // Authoritative check first: does the ancien/nouveau reference file
        // know this exact locality, and has it already been placed in the
        // DB (by yesterday's nomenclature migration or otherwise) under its
        // real ANSADE code — possibly under a *different* commune than this
        // xlsx row says? all_localites.xlsx predates that migration and can
        // disagree with it on commune assignment; the migration's placement
        // wins, so we must not create a duplicate just because the commune
        // column doesn't match. This command never re-links, so if it's
        // filed elsewhere, it just stays there.
        $codeKey = $this->normalizeString($wilaya->intitule) . '|' . $this->normalizeString($commune->intitule) . '|' . $normTarget;
        $codes = $this->codeIndex[$codeKey] ?? null;
        $localite = null;
        if ($codes) {
            $localite = $this->localitesByCode->get($codes['nouveau_code'])
                ?? $this->localitesByAncienCode->get($codes['ancien_code']);
            if ($localite) {
                $existingElsewhere = $localite->commune_id != $commune->id;
            }
        }

        // No ancien/nouveau match (row not covered by that reference, or
        // not matched to anything yet): fall back to a commune-scoped name
        // match. Same wilaya+moughata+commune+name isn't enough on its own
        // to call it the same locality — Mauritania has plenty of same-name
        // places within one commune. When the candidate's code_ansade is
        // still in the source file's own format (untouched by the
        // migration, which overwrites it with a real ANSADE code), compare
        // it against this row's code_ansade: equal codes confirm it's truly
        // the same source row; different codes mean it's a distinct
        // locality that happens to share a name and should still be
        // created. A candidate the migration already recoded is trusted on
        // the name match alone, since a code "mismatch" there is expected,
        // not a sign of a different place.
        if (!$localite) {
            $xlsxCodePattern = '/^\d{2}-\d-\d{2}-\d{3}$/';
            $localite = $this->localites->first(function ($item) use ($normTarget, $commune, $row, $xlsxCodePattern) {
                if ($item->commune_id != $commune->id) {
                    return false;
                }
                $nameMatches = $this->normalizeString($item->intitule) === $normTarget
                    || $this->normalizeString($item->intitule_fr) === $normTarget;
                if (!$nameMatches) {
                    return false;
                }
                if ($item->code_ansade !== null && preg_match($xlsxCodePattern, $item->code_ansade)) {
                    return $item->code_ansade === $row['code_ansade'];
                }
                return true;
            });
        }

        [$lat, $latIssue] = $this->parseCoordinate($row['lat_loc'], 'lat');
        [$long, $longIssue] = $this->parseCoordinate($row['long_loc'], 'long');
        if ($latIssue || $longIssue) {
            $stats['coord_unparseable']++;
            $coordIssues[] = [
                'wilaya' => $wilaya->intitule,
                'commune' => $commune->intitule,
                'localite' => $row['localite'],
                'lat_raw' => $row['lat_loc'],
                'long_raw' => $row['long_loc'],
            ];
        }

        if ($localite) {
            // Only ever touch coordinates on an existing row, and only to
            // replace a non-numeric value with a parsed decimal — never
            // re-link or recode a localite the nomenclature migration
            // already placed correctly.
            $currentLatOk = $localite->lat_loc !== null && $localite->lat_loc !== '' && is_numeric($localite->lat_loc);
            $currentLongOk = $localite->long_loc !== null && $localite->long_loc !== '' && is_numeric($localite->long_loc);

            $update = [];
            if (!$currentLatOk && $lat !== null) {
                $update['lat_loc'] = $lat;
            }
            if (!$currentLongOk && $long !== null) {
                $update['long_loc'] = $long;
            }

            if (!empty($update)) {
                Localite::where('id', $localite->id)->update($update);
                $stats['localite_coords_fixed']++;
            } elseif ($existingElsewhere) {
                $stats['localite_existing_elsewhere']++;
            } else {
                $stats['localite_existing_ok']++;
            }

            return;
        }

        $newLocalite = Localite::create([
            'intitule' => $row['localite'],
            'intitule_fr' => $row['localite'],
            'commune_id' => $commune->id,
            'moughata_id' => $moughata->id,
            'wilaya_id' => $wilaya->id,
            'code' => $row['code_loc'] ?? $this->generateLocaliteCode($wilaya, $moughata, $commune),
            'code_ansade' => $codes['nouveau_code'] ?? $row['code_ansade'],
            'ancien_code_ansade' => $codes['ancien_code'] ?? null,
            'nb_pop_ansade' => is_numeric($row['nb_pop_ansade']) ? (int) $row['nb_pop_ansade'] : 0,
            'lat_loc' => $lat,
            'long_loc' => $long,
            'valider' => false,
        ]);
        $this->localites->push($newLocalite);
        if (!empty($newLocalite->code_ansade)) {
            $this->localitesByCode->put($newLocalite->code_ansade, $newLocalite);
        }
        if (!empty($newLocalite->ancien_code_ansade)) {
            $this->localitesByAncienCode->put($newLocalite->ancien_code_ansade, $newLocalite);
        }
        $stats['localite_created']++;
        $created[] = [
            'wilaya' => $wilaya->intitule,
            'moughataa' => $moughata->intitule,
            'commune' => $commune->intitule,
            'localite' => $row['localite'],
        ];
    }

    /**
     * Accepts a value already in decimal degrees, or a DMS string in one of
     * the formats actually seen in the source file:
     *   - hemisphere prefix:  N16°39'28,0"  /  W12°25'05,0"
     *   - hemisphere suffix:  11°34'106''W  /  16°13'52,4''N  (curly ’ / ''
     *     or "" as the seconds mark are normalized to a straight ' and ")
     *   - degrees + decimal minutes, no seconds, no hemisphere marker at
     *     all: 16°12.283' — since every locality in this dataset sits in
     *     Mauritania (entirely N latitude, W longitude), the sign is inferred
     *     from which column ($column: 'lat' defaults positive, 'long'
     *     negative) rather than guessed from the string.
     *
     * @return array{0: ?float, 1: bool}
     */
    private function parseCoordinate($raw, string $column): array
    {
        if ($raw === null || $raw === '') {
            return [null, false];
        }

        if (is_numeric($raw)) {
            return [(float) $raw, false];
        }

        $raw = trim((string) $raw);
        $raw = str_replace(['’', '‘', '′', '´'], "'", $raw);
        $raw = str_replace(['″', '""'], '"', $raw);
        $raw = preg_replace("/''/", '"', $raw);

        if (preg_match('/^([NSEW])\s*(\d+)[°:\s]+(\d+)(?:[\'\s]+([\d.,]+)"?)?\s*$/u', $raw, $m)) {
            return [$this->dmsToDecimal((float) $m[2], (float) $m[3], isset($m[4]) ? (float) str_replace(',', '.', $m[4]) : 0.0, $m[1]), false];
        }

        if (preg_match('/^(\d+)[°:\s]+(\d+)(?:[\'\s]+([\d.,]+)"?)?\s*([NSEW])\s*$/u', $raw, $m)) {
            return [$this->dmsToDecimal((float) $m[1], (float) $m[2], isset($m[3]) ? (float) str_replace(',', '.', $m[3]) : 0.0, $m[4]), false];
        }

        if (preg_match('/^(\d+)[°:\s]+([\d.,]+)\'\s*$/u', $raw, $m)) {
            $decimal = round((float) $m[1] + ((float) str_replace(',', '.', $m[2])) / 60, 6);
            return [$column === 'long' ? -$decimal : $decimal, false];
        }

        return [null, true];
    }

    private function dmsToDecimal(float $degrees, float $minutes, float $seconds, string $hemisphere): float
    {
        $decimal = round($degrees + $minutes / 60 + $seconds / 3600, 6);

        return in_array($hemisphere, ['S', 'W'], true) ? -$decimal : $decimal;
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

    private function generateLocaliteCode(Wilaya $wilaya, Moughata $moughata, Commune $commune): string
    {
        $prefix = str_pad((string) ($wilaya->code ?? '0'), 2, '0', STR_PAD_LEFT)
            . str_pad((string) ($moughata->code ?? '0'), 2, '0', STR_PAD_LEFT)
            . str_pad((string) ($commune->code ?? '0'), 2, '0', STR_PAD_LEFT);

        $count = Localite::where('code', 'LIKE', $prefix . '%')->count();

        return $prefix . str_pad((string) ($count + 1), 4, '0', STR_PAD_LEFT);
    }

    private function printReport(array $stats): void
    {
        $this->table(['Indicateur', 'Valeur'], [
            ['Wilaya introuvable (ligne ignorée)', $stats['wilaya_not_found']],
            ['Moughataa créées', $stats['moughata_create']],
            ['Communes créées', $stats['commune_create']],
            ['Localités créées', $stats['localite_created']],
            ['Localités déjà présentes (inchangées)', $stats['localite_existing_ok']],
            ['Localités déjà présentes ailleurs (autre commune, via code ANSADE)', $stats['localite_existing_elsewhere']],
            ['Localités existantes — coordonnées corrigées', $stats['localite_coords_fixed']],
            ['Coordonnées illisibles (ni décimal ni DMS reconnu)', $stats['coord_unparseable']],
        ]);
    }

    private function writeReportCsv(string $path, array $created, array $coordIssues): void
    {
        $fh = fopen($path, 'w');
        fputcsv($fh, ['type', 'wilaya', 'moughataa_ou_commune', 'commune_ou_localite', 'localite_ou_lat', 'lat_raw_ou_long', 'long_raw']);
        foreach ($created as $row) {
            fputcsv($fh, ['created', $row['wilaya'], $row['moughataa'], $row['commune'], $row['localite'], '', '']);
        }
        foreach ($coordIssues as $row) {
            fputcsv($fh, ['coord_issue', $row['wilaya'], $row['commune'], $row['localite'], $row['lat_raw'], $row['long_raw'], '']);
        }
        fclose($fh);
    }
}
