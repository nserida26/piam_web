<?php

namespace App\Console\Commands;

use App\Models\Commune;
use App\Models\Localite;
use App\Models\Moughata;
use App\Models\Wilaya;
use App\Support\Concerns\NormalizesAdministrativeNames;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

/**
 * Full ground-up import of the wilaya -> moughata -> commune -> localite
 * hierarchy from all_localites.xlsx, meant to run against EMPTY hierarchy
 * tables (unlike localites:import-missing, which only fills gaps in an
 * already-populated DB). Also backfills ancien_code_ansade from
 * "Ancien et nouveau codes.xlsx" by name-matching, since that file uses a
 * different code system (flat ANSADE numeric codes) than all_localites.xlsx's
 * own code_ansade (dashed "01-1-01-001" format) and the two can't be joined
 * by code.
 *
 * all_localites.xlsx only covers 9 inland wilayas (Hodh Chargui, Hodh
 * Gharbi, Assaba, Gorgol, Brakna, Trarza, Adrar, Tagant, Guidimakha) — the
 * 6 coastal/capital wilayas (Inchiri, Tiris Zemmour, Dakhlet Nouadhibou,
 * Nouakchott-Nord/Ouest/Sud) are NOT in this source and will not be created.
 *
 * Dry-run by default (rolled back transaction); --apply commits.
 */
class ImportAllLocalitesCommand extends Command
{
    use NormalizesAdministrativeNames;

    protected $signature = 'localites:import-all
                            {--apply : Applique réellement les changements (sans cette option : dry-run)}
                            {--file= : Chemin de all_localites.xlsx (défaut : à la racine du projet)}
                            {--codes-file= : Chemin de "Ancien et nouveau codes.xlsx" (défaut : à la racine du projet)}
                            {--report= : Chemin d\'un CSV où écrire les lignes sans correspondance ancien_code / coordonnées illisibles / doublons fusionnés}';

    protected $description = "Import complet wilaya->moughata->commune->localite depuis all_localites.xlsx (table rase), avec backfill de ancien_code_ansade depuis 'Ancien et nouveau codes.xlsx'";

    /** @var array<string, Wilaya> keyed by normalized name */
    private array $wilayaCache = [];
    /** @var array<string, Moughata> keyed by wilaya_id|normalized name */
    private array $moughataCache = [];
    /** @var array<string, Commune> keyed by moughata_id|normalized name */
    private array $communeCache = [];
    /** @var array<string, Localite> keyed by commune_id|normalized name, for in-run dedupe */
    private array $localiteCache = [];
    /** @var array<string, int> next free localite-code sequence per wilayaCode+moughataCode+communeCode prefix */
    private array $codeSeq = [];
    /** @var array<string, array{ancien_code:string, nouveau_code:string}> keyed by normalized wilaya|commune|localite (moughataa dropped — see buildCodeIndex) */
    private array $codeIndex = [];

    public function handle(): int
    {
        $filePath = $this->option('file') ?: dirname(base_path()) . DIRECTORY_SEPARATOR . 'all_localites.xlsx';
        $codesPath = $this->option('codes-file') ?: dirname(base_path()) . DIRECTORY_SEPARATOR . 'Ancien et nouveau codes.xlsx';

        if (!is_file($filePath)) {
            $this->error("Fichier introuvable: {$filePath}");
            return self::FAILURE;
        }

        $this->buildCodeIndex($codesPath);

        $rows = $this->readSourceXlsx($filePath);
        $this->info(count($rows) . ' lignes lues dans ' . basename($filePath) . '.');

        $stats = [
            'wilaya_created' => 0,
            'moughata_created' => 0,
            'commune_created' => 0,
            'localite_created' => 0,
            'localite_merged_duplicate' => 0,
            'ancien_code_matched' => 0,
            'ancien_code_unmatched' => 0,
            'coord_missing_or_unparseable' => 0,
            'population_unparseable' => 0,
        ];
        $duplicates = [];
        $ancienUnmatched = [];
        $coordIssues = [];

        $apply = (bool) $this->option('apply');

        DB::beginTransaction();
        try {
            $bar = $this->output->createProgressBar(count($rows));
            $bar->start();

            foreach ($rows as $row) {
                if (empty($row['wilaya']) || empty($row['moughataa']) || empty($row['commune']) || empty($row['localite'])) {
                    $bar->advance();
                    continue;
                }

                $wilaya = $this->findOrCreateWilaya($row, $stats);
                $moughata = $this->findOrCreateMoughata($row, $wilaya, $stats);
                $commune = $this->findOrCreateCommune($row, $moughata, $wilaya, $stats);
                $this->createOrMergeLocalite($row, $commune, $moughata, $wilaya, $stats, $duplicates, $ancienUnmatched, $coordIssues);

                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);

            $this->printReport($stats, count($this->wilayaCache));

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
            $this->writeReportCsv($absolutePath, $duplicates, $ancienUnmatched, $coordIssues);
            $this->info("Rapport écrit dans {$absolutePath}");
        }

        return self::SUCCESS;
    }

    /**
     * @return array<int, array{wilaya:string,code_w:mixed,moughataa:string,code_mgt:mixed,commune:string,code_com:mixed,localite:string,code_ansade:?string,long_loc:mixed,lat_loc:mixed,nb_pop_ansade:mixed}>
     */
    private function readSourceXlsx(string $path): array
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

            $rows[] = [
                'wilaya' => $wilaya,
                'code_w' => $sheet->getCell('B' . $r)->getValue(),
                'moughataa' => trim((string) $sheet->getCell('C' . $r)->getValue()),
                'code_mgt' => $sheet->getCell('D' . $r)->getValue(),
                'commune' => trim((string) $sheet->getCell('E' . $r)->getValue()),
                'code_com' => $sheet->getCell('F' . $r)->getValue(),
                'localite' => trim((string) $sheet->getCell('G' . $r)->getValue()),
                'code_ansade' => trim((string) $sheet->getCell('I' . $r)->getValue()) ?: null,
                'long_loc' => $sheet->getCell('J' . $r)->getValue(),
                'lat_loc' => $sheet->getCell('K' . $r)->getValue(),
                'nb_pop_ansade' => $sheet->getCell('L' . $r)->getValue(),
            ];
        }

        return $rows;
    }

    /**
     * Builds a wilaya|commune|localite -> {ancien_code, nouveau_code} index
     * from "Ancien et nouveau codes.xlsx". Moughataa is deliberately left out
     * of the key: the two source files don't always agree on moughataa
     * naming for the same commune, and commune name is already specific
     * enough within a wilaya to avoid collisions.
     */
    private function buildCodeIndex(string $path): void
    {
        if (!is_file($path)) {
            $this->warn("Fichier de codes introuvable ({$path}) — ancien_code_ansade ne sera pas renseigné.");
            return;
        }

        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $sheet = $reader->load($path)->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        for ($r = 2; $r <= $highestRow; $r++) {
            $wilaya = trim((string) $sheet->getCell('A' . $r)->getValue());
            if ($wilaya === '') {
                continue;
            }
            $commune = trim((string) $sheet->getCell('C' . $r)->getValue());
            $ancien = trim((string) $sheet->getCell('D' . $r)->getValue());
            $nouveau = trim((string) $sheet->getCell('E' . $r)->getValue());
            $nom = trim((string) $sheet->getCell('F' . $r)->getValue());

            $key = $this->normalizeString($wilaya) . '|' . $this->normalizeString($commune) . '|' . $this->normalizeString($nom);
            $this->codeIndex[$key] = ['ancien_code' => $ancien, 'nouveau_code' => $nouveau];
        }

        $this->info(count($this->codeIndex) . ' entrées indexées depuis ' . basename($path) . '.');
    }

    private function findOrCreateWilaya(array $row, array &$stats): Wilaya
    {
        $key = $this->normalizeString($row['wilaya']);
        if (isset($this->wilayaCache[$key])) {
            return $this->wilayaCache[$key];
        }

        $wilaya = Wilaya::create([
            'intitule' => $row['wilaya'],
            'intitule_fr' => $row['wilaya'],
            'code' => is_numeric($row['code_w']) ? (int) $row['code_w'] : (count($this->wilayaCache) + 1),
        ]);
        $stats['wilaya_created']++;

        return $this->wilayaCache[$key] = $wilaya;
    }

    private function findOrCreateMoughata(array $row, Wilaya $wilaya, array &$stats): Moughata
    {
        $key = $wilaya->id . '|' . $this->normalizeString($row['moughataa']);
        if (isset($this->moughataCache[$key])) {
            return $this->moughataCache[$key];
        }

        $moughata = Moughata::create([
            'intitule' => $row['moughataa'],
            'intitule_fr' => $row['moughataa'],
            'code' => is_numeric($row['code_mgt']) ? (int) $row['code_mgt'] : 1,
            'wilaya_id' => $wilaya->id,
        ]);
        $stats['moughata_created']++;

        return $this->moughataCache[$key] = $moughata;
    }

    private function findOrCreateCommune(array $row, Moughata $moughata, Wilaya $wilaya, array &$stats): Commune
    {
        $key = $moughata->id . '|' . $this->normalizeString($row['commune']);
        if (isset($this->communeCache[$key])) {
            return $this->communeCache[$key];
        }

        $commune = Commune::create([
            'intitule' => $row['commune'],
            'intitule_fr' => $row['commune'],
            'code' => is_numeric($row['code_com']) ? (int) $row['code_com'] : 1,
            'moughata_id' => $moughata->id,
            'wilaya_id' => $wilaya->id,
        ]);
        $stats['commune_created']++;

        return $this->communeCache[$key] = $commune;
    }

    private function createOrMergeLocalite(
        array $row,
        Commune $commune,
        Moughata $moughata,
        Wilaya $wilaya,
        array &$stats,
        array &$duplicates,
        array &$ancienUnmatched,
        array &$coordIssues
    ): void {
        $normName = $this->normalizeString($row['localite']);

        [$lat, $latIssue] = $this->parseCoordinate($row['lat_loc'], 'lat');
        [$long, $longIssue] = $this->parseCoordinate($row['long_loc'], 'long');
        if ($latIssue || $longIssue || $lat === null || $long === null) {
            $stats['coord_missing_or_unparseable']++;
            $coordIssues[] = [
                'wilaya' => $wilaya->intitule, 'commune' => $commune->intitule, 'localite' => $row['localite'],
                'lat_raw' => $row['lat_loc'], 'long_raw' => $row['long_loc'],
            ];
        }

        $population = $this->parsePopulation($row['nb_pop_ansade']);
        if ($population === null && !empty(trim((string) $row['nb_pop_ansade']))) {
            $stats['population_unparseable']++;
        }

        $codeKey = $this->normalizeString($wilaya->intitule) . '|' . $this->normalizeString($commune->intitule) . '|' . $normName;
        $codes = $this->codeIndex[$codeKey] ?? null;
        if ($codes) {
            $stats['ancien_code_matched']++;
        } else {
            $stats['ancien_code_unmatched']++;
            $ancienUnmatched[] = ['wilaya' => $wilaya->intitule, 'commune' => $commune->intitule, 'localite' => $row['localite']];
        }

        // Two rows with the same (commune, name) are only the SAME locality
        // when they also agree on code_ansade (when both have one) or, when
        // both are missing a code_ansade, on coordinates (including both
        // being blank). Same name alone is common and not sufficient —
        // checked against the source file: 54 of the first pass's 57
        // same-name groups turned out to be genuinely distinct places with
        // different codes and/or coordinates, wrongly collapsed into one row.
        $identityPart = !empty($row['code_ansade'])
            ? 'code:' . $row['code_ansade']
            : (($lat !== null || $long !== null) ? "coord:{$lat},{$long}" : 'blank:' . $normName);
        $dedupeKey = $commune->id . '|' . $normName . '|' . $identityPart;

        if (isset($this->localiteCache[$dedupeKey])) {
            // Same (commune, name) already created earlier in this run — merge
            // rather than creating a second identical-looking row.
            $existing = $this->localiteCache[$dedupeKey];
            $existing->update([
                'nb_pop_ansade' => $population ?? $existing->nb_pop_ansade,
                'lat_loc' => $lat ?? $existing->lat_loc,
                'long_loc' => $long ?? $existing->long_loc,
                'ancien_code_ansade' => $existing->ancien_code_ansade ?: ($codes['ancien_code'] ?? null),
            ]);
            $stats['localite_merged_duplicate']++;
            $duplicates[] = ['wilaya' => $wilaya->intitule, 'commune' => $commune->intitule, 'localite' => $row['localite']];
            return;
        }

        $localite = Localite::create([
            'intitule' => $row['localite'],
            'intitule_fr' => $row['localite'],
            'commune_id' => $commune->id,
            'moughata_id' => $moughata->id,
            'wilaya_id' => $wilaya->id,
            'code' => $this->generateLocaliteCode($wilaya, $moughata, $commune),
            'code_ansade' => $row['code_ansade'],
            'ancien_code_ansade' => $codes['ancien_code'] ?? null,
            'nb_pop_ansade' => $population,
            'lat_loc' => $lat,
            'long_loc' => $long,
            'valider' => true,
        ]);
        $this->localiteCache[$dedupeKey] = $localite;
        $stats['localite_created']++;
    }

    /**
     * "  4,458 " -> 4458. Returns null when there's nothing parseable.
     */
    private function parsePopulation($raw): ?int
    {
        if ($raw === null || $raw === '') {
            return null;
        }
        if (is_numeric($raw)) {
            return (int) round((float) $raw);
        }
        $cleaned = str_replace([',', ' ', "\xc2\xa0"], '', trim((string) $raw));
        return is_numeric($cleaned) ? (int) $cleaned : null;
    }

    /**
     * Same tolerant decimal/DMS coordinate parser as localites:import-missing
     * (all_localites.xlsx coordinates are already decimal at the time this
     * command was written, but kept defensive against DMS strings).
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

    private function generateLocaliteCode(Wilaya $wilaya, Moughata $moughata, Commune $commune): string
    {
        $prefix = str_pad((string) ($wilaya->code ?? '0'), 2, '0', STR_PAD_LEFT)
            . str_pad((string) ($moughata->code ?? '0'), 2, '0', STR_PAD_LEFT)
            . str_pad((string) ($commune->code ?? '0'), 2, '0', STR_PAD_LEFT);

        $seq = ($this->codeSeq[$prefix] ?? 0) + 1;
        $this->codeSeq[$prefix] = $seq;

        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    private function printReport(array $stats, int $wilayaCount): void
    {
        $this->table(['Indicateur', 'Valeur'], [
            ['Wilayas créées', $stats['wilaya_created']],
            ['Moughataas créées', $stats['moughata_created']],
            ['Communes créées', $stats['commune_created']],
            ['Localités créées', $stats['localite_created']],
            ['Doublons (même commune+nom) fusionnés au lieu de dupliqués', $stats['localite_merged_duplicate']],
            ['ancien_code_ansade trouvé (via fichier de codes)', $stats['ancien_code_matched']],
            ['ancien_code_ansade non trouvé', $stats['ancien_code_unmatched']],
            ['Coordonnées manquantes/illisibles', $stats['coord_missing_or_unparseable']],
            ['Population illisible', $stats['population_unparseable']],
        ]);

        if ($wilayaCount < 15) {
            $this->warn("Seulement {$wilayaCount} wilaya(s) importée(s) — all_localites.xlsx ne couvre que les wilayas de l'intérieur. Inchiri, Tiris Zemmour, Dakhlet Nouadhibou et les 3 wilayas de Nouakchott ne sont PAS dans ce fichier et devront être ajoutées séparément.");
        }
    }

    private function writeReportCsv(string $path, array $duplicates, array $ancienUnmatched, array $coordIssues): void
    {
        $fh = fopen($path, 'w');
        fputcsv($fh, ['type', 'wilaya', 'commune', 'localite', 'lat_raw', 'long_raw']);
        foreach ($duplicates as $row) {
            fputcsv($fh, ['duplicate_merged', $row['wilaya'], $row['commune'], $row['localite'], '', '']);
        }
        foreach ($ancienUnmatched as $row) {
            fputcsv($fh, ['ancien_code_unmatched', $row['wilaya'], $row['commune'], $row['localite'], '', '']);
        }
        foreach ($coordIssues as $row) {
            fputcsv($fh, ['coord_issue', $row['wilaya'], $row['commune'], $row['localite'], $row['lat_raw'], $row['long_raw']]);
        }
        fclose($fh);
    }
}
