<?php

namespace App\Console\Commands;

use App\Imports\CertificationImport;
use App\Imports\DeclenchementImport;
use App\Imports\DernierSuiviImport;
use App\Imports\EtatLieuxImport;
use App\Models\LocaliteReport;
use App\Models\Wilaya;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

/**
 * Imports the 9 per-wilaya "Transfert données {Wilaya} dans BADAM_xxxxxx.xlsx"
 * files BEFORE the wilaya/moughataa/commune/localite hierarchy has been
 * reorganized against "Ancien et nouveau codes.xlsx" — i.e. while the DB
 * hierarchy still carries the same old naming/dashed-code_ansade nomenclature
 * as these transfer files. That lets every row be resolved the natural way
 * (wilaya -> moughataa -> commune -> localite, matched top-down by name/old
 * code, exactly what app/Imports/BaseReportImport.php + its 4 operation
 * subclasses already do), instead of the ancien_code_ansade-based reverse
 * lookup ImportLegacyLocaliteOperationsCommand needs post-reorg.
 *
 * Reuses those exact import classes (DeclenchementImport, EtatLieuxImport,
 * CertificationImport, DernierSuiviImport) by feeding them rows read directly
 * off each sheet via PhpSpreadsheet — sidesteps Laravel-Excel's single-sheet
 * assumption so every relevant sheet in the workbook gets processed.
 *
 * Dry-run by default (rolled back transaction); --apply commits.
 */
class ImportLegacyPreReorgCommand extends Command
{
    protected $signature = 'operations:import-legacy-prereorg
                            {--wilaya= : Nom de wilaya (ex: ADRAR) ; toutes les 9 si omis}
                            {--apply : Applique réellement les changements (sans cette option : dry-run)}
                            {--report= : Chemin d\'un CSV où écrire le détail des lignes ignorées/en erreur}
                            {--user-id=1 : user_id attribué aux lignes importées (BaseReportImport lit Auth::id(), vide en contexte console)}
                            {--moughataa= : Ne traiter que les lignes dont la colonne "moughataa" du fichier correspond (ex: OUALATA)}';

    protected $description = "Importe les opérations localité historiques des fichiers BADAM en matchant contre la hiérarchie AVANT réorganisation";

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

    private const IMPORT_CLASSES = [
        1 => DeclenchementImport::class,
        2 => EtatLieuxImport::class,
        3 => CertificationImport::class,
        4 => DernierSuiviImport::class,
    ];

    private array $stats = [
        'read' => 0,
        'inserted' => 0,
        'updated' => 0,
        'skipped' => 0,
        'errored' => 0,
    ];

    /** @var array<int,array<string,mixed>> */
    private array $reportRows = [];

    /** @var array<int,array<string,int>> per-wilaya per-operation inserted+updated count */
    private array $perOperationCounts = [];

    /** @var string|null uppercased moughataa filter, or null for no filter */
    private ?string $moughataaFilter = null;

    /** @var array<string,true> keys of newly-created rows in THIS run, to catch duplicate source rows findExistingReport() (DB-only) can't see */
    private array $createdKeys = [];

    public function handle(): int
    {
        $wilayaFilter = $this->option('wilaya') ? mb_strtoupper(trim($this->option('wilaya'))) : null;
        $apply = (bool) $this->option('apply');
        $this->moughataaFilter = $this->option('moughataa') ? mb_strtoupper(trim($this->option('moughataa'))) : null;
        Auth::loginUsingId((int) $this->option('user-id'));

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

        // One transaction PER WILAYA rather than one for the whole batch: a
        // bad row in wilaya #5 must not roll back the 4 wilayas already
        // successfully processed before it.
        $failedWilayas = [];
        foreach ($wilayaNames as $wilayaName) {
            DB::beginTransaction();
            try {
                $this->processWilaya($wilayaName, $projectRoot . DIRECTORY_SEPARATOR . self::WILAYA_FILES[$wilayaName]);

                if ($apply) {
                    DB::commit();
                } else {
                    DB::rollBack();
                }
            } catch (\Throwable $e) {
                DB::rollBack();
                $this->error("Erreur sur {$wilayaName} — rollback de cette wilaya seulement : " . $e->getMessage());
                $failedWilayas[] = $wilayaName;
            }
        }

        $this->printStats();
        if ($apply) {
            $this->info('Changements appliqués' . ($failedWilayas ? ' (sauf : ' . implode(', ', $failedWilayas) . ')' : '') . '.');
        } else {
            $this->comment('Dry-run : aucun changement écrit. Relancez avec --apply pour appliquer.');
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

    private function processWilaya(string $wilayaName, string $path): void
    {
        $wilaya = Wilaya::where('intitule', $wilayaName)->first();
        if (!$wilaya) {
            $this->error("Wilaya introuvable en base: {$wilayaName}");
            return;
        }

        $this->info("=== {$wilayaName} ({$path}) ===");
        $this->perOperationCounts[$wilayaName] = [1 => 0, 2 => 0, 3 => 0, 4 => 0];

        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);

        // Group sheets by operation_id so e.g. Op2-EDL1 and Op2-EDL2 share
        // ONE import instance (one set of reference collections + one
        // existingReports snapshot) instead of reloading everything twice.
        $sheetsByOperation = [];
        foreach ($spreadsheet->getSheetNames() as $sheetName) {
            $operationId = $this->operationIdForSheet($sheetName);
            if ($operationId !== null) {
                $sheetsByOperation[$operationId][] = $sheetName;
            }
        }

        foreach ($sheetsByOperation as $operationId => $sheetNames) {
            $importClass = self::IMPORT_CLASSES[$operationId];
            $import = new $importClass($operationId, $wilaya->id);

            foreach ($sheetNames as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                $rows = $this->readSheetByHeader($sheet);

                if ($this->moughataaFilter !== null) {
                    $rows = array_values(array_filter($rows, function ($row) {
                        return mb_strtoupper(trim((string) ($row['moughataa'] ?? ''))) === $this->moughataaFilter;
                    }));
                }

                $this->info("  - {$sheetName}: " . count($rows) . ' lignes');

                foreach ($rows as $row) {
                    $this->stats['read']++;
                    $this->processRow($row, $import, $operationId, $wilayaName, $sheetName);
                }
            }
        }
    }

    private function processRow(array $row, $import, int $operationId, string $wilayaName, string $sheetName): void
    {
        $skippedBefore = count($import->getSkippedRows());
        $errorBefore = count($import->getErrorRows());

        $model = $import->model($row);

        if ($model !== null) {
            // BaseReportImport::findExistingReport() only guards against rows
            // already committed to the DB — it never sees a sibling row
            // created earlier in THIS SAME run, so an identical row repeated
            // across sheets (or duplicated in the source file itself) would
            // otherwise be inserted once per occurrence. Guard on the same
            // key here, where every row is saved one at a time under our
            // control.
            $dedupKey = implode('|', [
                $model->localite_id, $model->operation_id, $model->projet_id, $model->operateur_id, $model->date_op,
            ]);
            if (isset($this->createdKeys[$dedupKey])) {
                $this->stats['skipped']++;
                $this->reportRows[] = $this->reportRow($wilayaName, $sheetName, $row, 'doublon_intra_execution');
                return;
            }
            $this->createdKeys[$dedupKey] = true;

            $model->save();
            $this->stats['inserted']++;
            $this->perOperationCounts[$wilayaName][$operationId]++;
            return;
        }

        $errorRows = $import->getErrorRows();
        if (count($errorRows) > $errorBefore) {
            $this->stats['errored']++;
            $errorRow = end($errorRows);
            $this->reportRows[] = $this->reportRow($wilayaName, $sheetName, $row, 'erreur: ' . ($errorRow['error'] ?? ''));
            return;
        }

        $skippedRows = $import->getSkippedRows();
        if (count($skippedRows) > $skippedBefore) {
            $this->stats['skipped']++;
            $skippedRow = end($skippedRows);
            $this->reportRows[] = $this->reportRow($wilayaName, $sheetName, $row, $skippedRow['reason'] ?? 'ignoree');
            return;
        }

        // model() returned null without a new skip/error entry: an existing
        // report was matched and updated in place (see findExistingReport()
        // in BaseReportImport).
        $this->stats['updated']++;
        $this->perOperationCounts[$wilayaName][$operationId]++;
    }

    private function operationIdForSheet(string $sheetName): ?int
    {
        $key = str_replace(' ', '', mb_strtolower(trim($sheetName)));
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
     *   matching the keys app/Imports/*.php's WithHeadingRow already relies on.
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

    private function reportRow(string $wilaya, string $sheet, array $row, string $reason): array
    {
        return [
            'wilaya' => $wilaya,
            'sheet' => $sheet,
            'localite' => $row['localite'] ?? '',
            'code_ansade' => $row['code_ansade'] ?? '',
            'commune' => $row['commune'] ?? '',
            'moughataa' => $row['moughataa'] ?? '',
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
        fputcsv($handle, ['wilaya', 'sheet', 'localite', 'code_ansade', 'commune', 'moughataa', 'int_projet_resum', 'intitule_se', 'reason']);
        foreach ($this->reportRows as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
    }

    private function printStats(): void
    {
        $this->newLine();
        $this->info('--- Résumé global ---');
        foreach ($this->stats as $key => $value) {
            $this->line("  {$key}: {$value}");
        }

        $this->newLine();
        $this->info('--- Par wilaya / opération (1=Décl, 2=EDL, 3=FDAL, 4=Dernier suivi) ---');
        $rows = [];
        foreach ($this->perOperationCounts as $wilaya => $counts) {
            $rows[] = [$wilaya, $counts[1], $counts[2], $counts[3], $counts[4], array_sum($counts)];
        }
        $this->table(['Wilaya', '1-Décl', '2-EDL', '3-FDAL', '4-Dernier suivi', 'Total'], $rows);
    }
}
