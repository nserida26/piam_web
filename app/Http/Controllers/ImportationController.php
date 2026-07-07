<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\ImportRequest;
use App\Models\Wilaya;
use App\Models\Operation;
use Maatwebsite\Excel\Exceptions\LaravelExcelException;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Throwable;
use Exception;
use ErrorException;

use App\Imports\AdministrationImport;
use App\Imports\DernierSuiviImport;
use App\Imports\EtatLieuxImport;
use App\Imports\CertificationImport;
use App\Imports\CommuneImport;
use App\Imports\DeclenchementImport;
use App\Imports\InfrastructureImport;
use App\Imports\InventaireImport;
use App\Imports\LocaliteImport;
use App\Imports\MoughataImport;
use App\Imports\OperateurImport;
use App\Imports\OperationImport;
use App\Imports\ProgrammationTraveauxImport;
use App\Imports\ProjetImport;
use App\Imports\RealisationTraveauxImport;
use App\Imports\WilayaImport;
use App\Models\Projet;
use Illuminate\Support\Facades\Log;
use App\Imports\UnifiedHierarchyImport;
use App\Services\ImportPreviewService;

class ImportationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function administration()
    {
        return view('admin.imports.administrations.index');
    }

    public function report()
    {
        $wilayas = Wilaya::all();
        $operations = Operation::all();
        return view('admin.imports.reports.index', compact(['wilayas', 'operations']));
    }

    /**
     * Gère l'import des données d'administration
     */
    public function storeAdministrations(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        try {
            // Validation du fichier
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv|max:20480',
                'type' => 'required|string|in:wilaya,moughata,commune,localite,infrastructure,operation,projet,operateur,unified'
            ], [
                'file.required' => 'Le fichier est obligatoire',
                'file.mimes' => 'Format invalide (Excel ou CSV requis)',
                'type.required' => 'Le type d\'import est obligatoire'
            ]);

            $file = $request->file('file');
            $type = $request->type;

            // Calculer le hash pour éviter les doublons
            $fileHash = hash('sha256', file_get_contents($file->getPathname()));
            $importSign = DB::table('import_sign')->where('sign', $fileHash)->first();

            if ($importSign) {
                return redirect()->back()->with('warning', 'Ce fichier a déjà été importé le ' . $importSign->date_import);
            }

            $importClassName = $this->getAdministrationImportClass($type);
            if (!$importClassName) {
                return redirect()->back()->with('error', 'Type d\'import non reconnu: ' . $type);
            }

            $import = new $importClassName();

            // Effectuer l'import
            $startTime = microtime(true);
            Excel::import($import, $file);
            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);

            // Enregistrer la signature
            DB::table('import_sign')->insert([
                'date_import' => date('Y-m-d H:i:s'),
                'sign' => $fileHash,
                'type' => $type,
                'file_name' => $file->getClientOriginalName()
            ]);

            // Statistiques détaillées
            $stats = "";
            if (method_exists($import, 'getNewCount')) {
                $stats = sprintf(
                    " (%d nouveaux, %d mis à jour, %d ignorés)",
                    $import->getNewCount(),
                    $import->getUpdateCount(),
                    method_exists($import, 'getSkippedCount') ? $import->getSkippedCount() : (method_exists($import, 'getSkippedRows') ? count($import->getSkippedRows()) : 0)
                );
            }

            $rowCount = method_exists($import, 'getRowCount') ? $import->getRowCount() : 'N/A';
            $message = "Import {$type} réussi ! {$rowCount} lignes traitées en {$duration}s.{$stats}";

            Log::info($message);
            return redirect()->back()->with('success', $message);
        } catch (ValidationException $ve) {
            return redirect()->back()->withErrors($ve->errors())->withInput();
        } catch (Throwable $e) {
            Log::error('Import Error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Erreur lors de l\'import: ' . $e->getMessage());
        }
    }

    /**
     * Gère l'import des rapports
     */
    public function storeReports(ImportRequest $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        try {
            $file = $request->file('file');

            // Calculer le hash pour éviter les doublons
            $fileHash = hash('sha256', file_get_contents($file->getPathname()));
            $importSign = DB::table('import_sign')->where('sign', $fileHash)->first();

            if ($importSign) {
                return redirect()->back()->with('warning', 'Ce fichier a déjà été importé le ' . $importSign->date_import);
            }

            $operation_id = $request->operation_id;
            $wilaya_id = $request->wilaya_id;

            $wilaya = Wilaya::find($wilaya_id);
            $operation = Operation::find($operation_id);

            if (!$wilaya || !$operation) {
                return redirect()->back()->with('error', 'Wilaya ou Opération invalide');
            }

            $importClassName = $this->getReportImportClass($operation_id);
            if (!$importClassName) {
                return redirect()->back()->with('error', 'Type d\'opération non reconnu');
            }

            $import = new $importClassName($operation_id, $wilaya_id);

            $startTime = microtime(true);
            Excel::import($import, $file);
            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);

            DB::table('import_sign')->insert([
                'date_import' => date('Y-m-d H:i:s'),
                'sign' => $fileHash,
                'type' => 'report_' . $operation_id,
                'file_name' => $file->getClientOriginalName(),
                'wilaya_id' => $wilaya_id,
                'operation_id' => $operation_id
            ]);

            $rowCount = method_exists($import, 'getRowCount') ? $import->getRowCount() : 'N/A';
            $message = sprintf(
                'Import du rapport "%s" réussi ! %s lignes traitées en %s secondes.',
                $operation->intitule,
                $rowCount,
                $duration
            );

            Log::info($message);
            return redirect()->back()->with('success', $message);
        } catch (Throwable $e) {
            Log::error('Report Import Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de l\'import: ' . $e->getMessage());
        }
    }

    /**
     * Prévisualise un fichier d'import administration (retourne JSON).
     */
    public function previewAdministrations(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:20480',
            'type' => 'required|string|in:wilaya,moughata,commune,localite,infrastructure,operation,projet,operateur,unified',
        ]);

        try {
            $service  = new ImportPreviewService();
            $tempPath = $service->storeTempFile($request->file('file'));
            $preview  = $service->previewAdministration($request->file('file'), $request->type);
            $preview['temp_path'] = $tempPath;
            return response()->json($preview);
        } catch (\Throwable $e) {
            Log::error('Preview Admin Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Prévisualise un fichier d'import rapport (retourne JSON).
     */
    public function previewReports(Request $request)
    {
        $request->validate([
            'file'         => 'required|file|mimes:xlsx,xls|max:204800',
            'operation_id' => 'required|integer',
            'wilaya_id'    => 'required|integer',
        ]);

        try {
            $service  = new ImportPreviewService();
            $tempPath = $service->storeTempFile($request->file('file'));
            $preview  = $service->previewReport(
                $request->file('file'),
                (int) $request->operation_id,
                (int) $request->wilaya_id
            );
            $preview['temp_path'] = $tempPath;
            return response()->json($preview);
        } catch (\Throwable $e) {
            Log::error('Preview Report Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Confirme et exécute l'import administration depuis le fichier temporaire.
     */
    public function confirmAdministrations(Request $request)
    {
        $request->validate([
            'temp_path' => 'required|string',
            'type'      => 'required|string|in:wilaya,moughata,commune,localite,infrastructure,operation,projet,operateur,unified',
        ]);

        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $service  = new ImportPreviewService();
        $fullPath = $service->getTempFilePath($request->temp_path);

        if (!file_exists($fullPath)) {
            return redirect()->route('admin.imports.administrations.index')
                ->with('error', 'Fichier temporaire expiré. Veuillez re-sélectionner le fichier.');
        }

        try {
            $fileHash  = hash('sha256', file_get_contents($fullPath));
            $importSign = DB::table('import_sign')->where('sign', $fileHash)->first();
            if ($importSign) {
                $service->deleteTempFile($request->temp_path);
                return redirect()->route('admin.imports.administrations.index')
                    ->with('warning', 'Ce fichier a déjà été importé le ' . $importSign->date_import);
            }

            $importClassName = $this->getAdministrationImportClass($request->type);
            $import = new $importClassName();

            $start = microtime(true);
            Excel::import($import, $fullPath);
            $duration = round(microtime(true) - $start, 2);

            DB::table('import_sign')->insert([
                'date_import' => date('Y-m-d H:i:s'),
                'sign'        => $fileHash,
                'type'        => $request->type,
                'file_name'   => basename($fullPath),
            ]);

            $stats = '';
            if (method_exists($import, 'getNewCount')) {
                $stats = sprintf(
                    ' (%d nouveaux, %d mis à jour, %d ignorés)',
                    $import->getNewCount(),
                    $import->getUpdateCount(),
                    method_exists($import, 'getSkippedCount')
                        ? $import->getSkippedCount()
                        : count($import->getSkippedRows())
                );
            }
            $rowCount = method_exists($import, 'getRowCount') ? $import->getRowCount() : 'N/A';
            $message  = "Import {$request->type} réussi ! {$rowCount} lignes traitées en {$duration}s.{$stats}";

            Log::info($message);
            $service->deleteTempFile($request->temp_path);
            return redirect()->route('admin.imports.administrations.index')->with('success', $message);
        } catch (\Throwable $e) {
            Log::error('Confirm Admin Import Error: ' . $e->getMessage());
            $service->deleteTempFile($request->temp_path);
            return redirect()->route('admin.imports.administrations.index')
                ->with('error', 'Erreur lors de l\'import : ' . $e->getMessage());
        }
    }

    /**
     * Confirme et exécute l'import rapport depuis le fichier temporaire.
     */
    public function confirmReports(Request $request)
    {
        $request->validate([
            'temp_path'    => 'required|string',
            'operation_id' => 'required|integer',
            'wilaya_id'    => 'required|integer',
        ]);

        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $service  = new ImportPreviewService();
        $fullPath = $service->getTempFilePath($request->temp_path);

        if (!file_exists($fullPath)) {
            return redirect()->route('admin.imports.reports.index')
                ->with('error', 'Fichier temporaire expiré. Veuillez re-sélectionner le fichier.');
        }

        try {
            $fileHash  = hash('sha256', file_get_contents($fullPath));
            $importSign = DB::table('import_sign')->where('sign', $fileHash)->first();
            if ($importSign) {
                $service->deleteTempFile($request->temp_path);
                return redirect()->route('admin.imports.reports.index')
                    ->with('warning', 'Ce fichier a déjà été importé le ' . $importSign->date_import);
            }

            $wilaya    = Wilaya::find($request->wilaya_id);
            $operation = Operation::find($request->operation_id);
            if (!$wilaya || !$operation) {
                return redirect()->route('admin.imports.reports.index')
                    ->with('error', 'Wilaya ou Opération invalide');
            }

            $importClassName = $this->getReportImportClass($request->operation_id);
            $import = new $importClassName($request->operation_id, $request->wilaya_id);

            $start = microtime(true);
            Excel::import($import, $fullPath);
            $duration = round(microtime(true) - $start, 2);

            DB::table('import_sign')->insert([
                'date_import'  => date('Y-m-d H:i:s'),
                'sign'         => $fileHash,
                'type'         => 'report_' . $request->operation_id,
                'file_name'    => basename($fullPath),
                'wilaya_id'    => $request->wilaya_id,
                'operation_id' => $request->operation_id,
            ]);

            $rowCount = method_exists($import, 'getRowCount') ? $import->getRowCount() : 'N/A';
            $message  = sprintf(
                'Import du rapport "%s" réussi ! %s lignes traitées en %s secondes.',
                $operation->intitule,
                $rowCount,
                $duration
            );

            Log::info($message);
            $service->deleteTempFile($request->temp_path);
            return redirect()->route('admin.imports.reports.index')->with('success', $message);
        } catch (\Throwable $e) {
            Log::error('Confirm Report Import Error: ' . $e->getMessage());
            $service->deleteTempFile($request->temp_path);
            return redirect()->route('admin.imports.reports.index')
                ->with('error', 'Erreur lors de l\'import : ' . $e->getMessage());
        }
    }

    /**
     * Retourne la classe d'import pour l'administration
     */
    private function getAdministrationImportClass($type)
    {
        $classes = [
            'wilaya' => WilayaImport::class,
            'moughata' => MoughataImport::class,
            'commune' => CommuneImport::class,
            'localite' => LocaliteImport::class,
            'infrastructure' => InfrastructureImport::class,
            'operation' => OperationImport::class,
            'projet' => ProjetImport::class,
            'operateur' => OperateurImport::class,
            'unified' => UnifiedHierarchyImport::class,
        ];

        return $classes[$type] ?? null;
    }

    /**
     * Retourne la classe d'import pour les rapports
     */
    private function getReportImportClass($operation_id)
    {
        $classes = [
            1 => DeclenchementImport::class,
            2 => EtatLieuxImport::class,
            3 => CertificationImport::class,
            4 => DernierSuiviImport::class,
            5 => InventaireImport::class,
            6 => RealisationTraveauxImport::class,
            7 => ProgrammationTraveauxImport::class,
        ];

        return $classes[$operation_id] ?? null;
    }

    /**
     * Affiche les logs d'import
     */
    public function showImportLogs()
    {
        $logs = DB::table('import_sign')
            ->orderBy('date_import', 'desc')
            ->paginate(20);

        return view('admin.imports.logs', compact('logs'));
    }

    /**
     * Supprime une signature d'import
     */
    public function deleteImportSign($id)
    {
        try {
            DB::table('import_sign')->where('id', $id)->delete();
            return redirect()->back()->with('success', 'Signature supprimée.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
}
