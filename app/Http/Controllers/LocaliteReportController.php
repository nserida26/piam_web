<?php

namespace App\Http\Controllers;

use App\Models\LocaliteReport;
use App\Models\Operation;
use App\Models\Projet;
use App\Models\User;
use App\Models\Operateur;

use Illuminate\Http\Request;
use App\Models\Wilaya;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class LocaliteReportController extends Controller
{
    // 
    public function index()
    {
        $wilayas = Wilaya::orderBy('intitule')->get();
        $operations = Operation::all();
        //operateurs_projets
        $operateurs = Operateur::all();
        //wilayas_projets
        $projets = Projet::all();

        return view('admin.reports.localites.index', compact('projets', 'operateurs', 'operations', 'wilayas'));
    }
    public function destroy($id)
    {
        $report = LocaliteReport::find($id);

        if (!is_null($report)) {
            $delete = $report->delete();

            if ($delete) {
                $notification = array(
                    'message' => 'Report has been deleted successfully.',
                    'alert-type' => 'success'
                );

                return redirect()->route('admin.reports.localites')->with($notification);
            } else {

                $notification = array(
                    'message' => 'Oops! Something went wrong',
                    'alert-type' => 'error'
                );

                return redirect()->back()->with($notification);
            }
        }
    }
    public function fechWilayaLocaliteReport(Request $request)
    {
        $wilaya_id = $request->wilaya_id;

        $localite_reports = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
            ->join('communes', 'localite_operations.commune_id', 'communes.id')
            ->join('moughatas', 'localite_operations.moughata_id', 'moughatas.id')
            ->join('wilayas', 'localite_operations.wilaya_id', 'wilayas.id')
            ->join('projets', 'localite_operations.projet_id', 'projets.id')
            ->join('operateurs', 'localite_operations.operateur_id', 'operateurs.id')
            ->join('operations', 'localite_operations.operation_id', 'operations.id')
            ->join('users', 'localite_operations.user_id', 'users.id')
            ->select('localite_operations.id', 'localites.intitule as localite', 'localites.code_ansade as code_ansade', 'projets.intitule_resume as projet', 'communes.intitule as commune', 'moughatas.intitule as moughata', 'wilayas.intitule as wilaya', 'operations.intitule as operation', 'operateurs.intitule as operateur', 'localite_operations.date_op','localite_operations.confirmed')
            ->where('localite_operations.wilaya_id', $wilaya_id)
            ->get();
        return response()->json($localite_reports);
    }


// Dans LocaliteReportController.php
public function getData(Request $request)
{
    $query = LocaliteReport::query()
        ->join('localites', 'localite_operations.localite_id', '=', 'localites.id')
        ->join('communes', 'localite_operations.commune_id', '=', 'communes.id')
        ->join('moughatas', 'localite_operations.moughata_id', '=', 'moughatas.id')
        ->join('wilayas', 'localite_operations.wilaya_id', '=', 'wilayas.id')
        ->join('projets', 'localite_operations.projet_id', '=', 'projets.id')
        ->join('operateurs', 'localite_operations.operateur_id', '=', 'operateurs.id')
        ->join('operations', 'localite_operations.operation_id', '=', 'operations.id')
        ->select([
            'localite_operations.id',
            'localites.intitule as localite',
            'localites.code_ansade',
            'projets.intitule_resume as projet',
            'communes.intitule as commune',
            'moughatas.intitule as moughata',
            'wilayas.intitule as wilaya',
            'operations.intitule as operation',
            'operateurs.intitule as operateur',
            'localite_operations.date_op',
            'localite_operations.confirmed'
        ]);

    // Apply filters
    if ($request->wilaya_id) {
        $query->where('localite_operations.wilaya_id', $request->wilaya_id);
    }
    if ($request->moughata_id) {
        $query->where('localite_operations.moughata_id', $request->moughata_id);
    }
    if ($request->commune_id) {
        $query->where('localite_operations.commune_id', $request->commune_id);
    }
    if ($request->localite_id) {
        $query->where('localite_operations.localite_id', $request->localite_id);
    }
    if ($request->operation_id) {
        $query->where('localite_operations.operation_id', $request->operation_id);
    }
    if ($request->projet_id) {
        $query->where('localite_operations.projet_id', $request->projet_id);
    }
    if ($request->operateur_id) {
        $query->where('localite_operations.operateur_id', $request->operateur_id);
    }
    if ($request->from && $request->to) {
        $query->whereBetween('localite_operations.date_op', [$request->from, $request->to]);
    }

    // Pagination manuelle
    $recordsTotal = $query->count();
    
    // Apply search if any
    if ($request->has('search') && !empty($request->search['value'])) {
        $search = $request->search['value'];
        $query->where(function($q) use ($search) {
            $q->where('localites.intitule', 'like', "%{$search}%")
              ->orWhere('communes.intitule', 'like', "%{$search}%")
              ->orWhere('moughatas.intitule', 'like', "%{$search}%")
              ->orWhere('wilayas.intitule', 'like', "%{$search}%")
              ->orWhere('projets.intitule_resume', 'like', "%{$search}%")
              ->orWhere('operations.intitule', 'like', "%{$search}%")
              ->orWhere('operateurs.intitule', 'like', "%{$search}%");
        });
    }
    
    // Apply ordering
    $columns = ['wilaya', 'moughata', 'commune', 'localite', 'operation', 'projet', 'operateur', 'date_op', 'confirmed'];

    // Apply per-column search (excludes the non-searchable "action" column)
    $columnDbMap = [
        'wilaya' => 'wilayas.intitule',
        'moughata' => 'moughatas.intitule',
        'commune' => 'communes.intitule',
        'localite' => 'localites.intitule',
        'operation' => 'operations.intitule',
        'projet' => 'projets.intitule_resume',
        'operateur' => 'operateurs.intitule',
        'date_op' => 'localite_operations.date_op',
        'confirmed' => 'localite_operations.confirmed',
    ];
    if ($request->has('columns')) {
        foreach ($request->columns as $index => $col) {
            $value = $col['search']['value'] ?? null;
            $columnKey = $columns[$index] ?? null;
            if ($value !== null && $value !== '' && $columnKey && isset($columnDbMap[$columnKey])) {
                $query->where($columnDbMap[$columnKey], 'like', "%{$value}%");
            }
        }
    }

    $recordsFiltered = $query->count();

    $orderColumnIndex = $request->order[0]['column'] ?? 7;
    $orderDir = $request->order[0]['dir'] ?? 'desc';
    
    if (isset($columns[$orderColumnIndex])) {
        $orderColumn = $columns[$orderColumnIndex];
        switch($orderColumn) {
            case 'wilaya':
                $query->orderBy('wilayas.intitule', $orderDir);
                break;
            case 'moughata':
                $query->orderBy('moughatas.intitule', $orderDir);
                break;
            case 'commune':
                $query->orderBy('communes.intitule', $orderDir);
                break;
            case 'localite':
                $query->orderBy('localites.intitule', $orderDir);
                break;
            case 'operation':
                $query->orderBy('operations.intitule', $orderDir);
                break;
            case 'projet':
                $query->orderBy('projets.intitule_resume', $orderDir);
                break;
            case 'operateur':
                $query->orderBy('operateurs.intitule', $orderDir);
                break;
            case 'date_op':
                $query->orderBy('localite_operations.date_op', $orderDir);
                break;
            case 'confirmed':
                $query->orderBy('localite_operations.confirmed', $orderDir);
                break;
        }
    }
    
    // Apply pagination
    $data = $query->skip($request->start ?? 0)
                  ->take($request->length ?? 50)
                  ->get();
    
    // Format data for DataTable
    $formattedData = [];
    foreach ($data as $row) {
        $formattedData[] = [
            'id' => $row->id,
            'wilaya' => $row->wilaya,
            'moughata' => $row->moughata,
            'commune' => $row->commune,
            'localite' => $row->localite,
            'operation' => $row->operation,
            'projet' => $row->projet,
            'operateur' => $row->operateur,
            'date_op' => $row->date_op,
            'confirmed' => $row->confirmed,
            'action' => $this->generateActionButtons($row->id)
        ];
    }
    
    return response()->json([
        'draw' => intval($request->draw),
        'recordsTotal' => $recordsTotal,
        'recordsFiltered' => $recordsFiltered,
        'data' => $formattedData
    ]);
}

private function generateActionButtons($id)
{
    $editUrl = route('admin.reports.localites.edit', $id);
    $deleteUrl = route('admin.reports.localites.destroy', $id);
    
    return '<div class="btn-group" role="group">' .
           '<a href="'.$editUrl.'" class="btn btn-sm btn-info" title="Edit">' .
           '<i class="fas fa-edit"></i>' .
           '</a>' .
           '<form action="'.$deleteUrl.'" method="POST" style="display:inline;" onsubmit="return confirm(\'Are you sure?\');">' .
           '<input type="hidden" name="_method" value="DELETE">' .
           '<input type="hidden" name="_token" value="'.csrf_token().'">' .
           '<button type="submit" class="btn btn-sm btn-danger" title="Delete">' .
           '<i class="fa fa-trash"></i>' .
           '</button>' .
           '</form>' .
           '</div>';
}
    public function fechMoughataLocaliteReport(Request $request)
    {
        $moughata_id = $request->moughata_id;
        $wilaya_id = $request->wilaya_id;
        $localite_reports = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
            ->join('communes', 'localite_operations.commune_id', 'communes.id')
            ->join('moughatas', 'localite_operations.moughata_id', 'moughatas.id')
            ->join('wilayas', 'localite_operations.wilaya_id', 'wilayas.id')
            ->join('projets', 'localite_operations.projet_id', 'projets.id')
            ->join('operateurs', 'localite_operations.operateur_id', 'operateurs.id')
            ->join('operations', 'localite_operations.operation_id', 'operations.id')
            ->join('users', 'localite_operations.user_id', 'users.id')
            ->select('localite_operations.id', 'localites.intitule as localite', 'localites.code_ansade as code_ansade', 'projets.intitule_resume as projet', 'communes.intitule as commune', 'moughatas.intitule as moughata', 'wilayas.intitule as wilaya', 'operations.intitule as operation', 'operateurs.intitule as operateur', 'localite_operations.date_op','localite_operations.confirmed')
            ->where('localite_operations.wilaya_id', $wilaya_id)
            ->where('localite_operations.moughata_id', $moughata_id)
            ->get();
        return response()->json($localite_reports);
    }

    public function fechCommuneLocaliteReport(Request $request)
    {
        $moughata_id = $request->moughata_id;
        $wilaya_id = $request->wilaya_id;
        $commune_id = $request->commune_id;
        $localite_reports = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
            ->join('communes', 'localite_operations.commune_id', 'communes.id')
            ->join('moughatas', 'localite_operations.moughata_id', 'moughatas.id')
            ->join('wilayas', 'localite_operations.wilaya_id', 'wilayas.id')
            ->join('projets', 'localite_operations.projet_id', 'projets.id')
            ->join('operateurs', 'localite_operations.operateur_id', 'operateurs.id')
            ->join('operations', 'localite_operations.operation_id', 'operations.id')
            ->join('users', 'localite_operations.user_id', 'users.id')
            ->select('localite_operations.id', 'localites.intitule as localite', 'localites.code_ansade as code_ansade', 'projets.intitule_resume as projet', 'communes.intitule as commune', 'moughatas.intitule as moughata', 'wilayas.intitule as wilaya', 'operations.intitule as operation', 'operateurs.intitule as operateur', 'localite_operations.date_op','localite_operations.confirmed')
            ->where('localite_operations.wilaya_id', $wilaya_id)
            ->where('localite_operations.moughata_id', $moughata_id)
            ->where('localite_operations.commune_id', $commune_id)
            ->get();
        return response()->json($localite_reports);
    }
    public function fechLocaliteLocaliteReport(Request $request)
    {
        $moughata_id = $request->moughata_id;
        $wilaya_id = $request->wilaya_id;
        $commune_id = $request->commune_id;
        $localite_id = $request->localite_id;
        $localite_reports = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
            ->join('communes', 'localite_operations.commune_id', 'communes.id')
            ->join('moughatas', 'localite_operations.moughata_id', 'moughatas.id')
            ->join('wilayas', 'localite_operations.wilaya_id', 'wilayas.id')
            ->join('projets', 'localite_operations.projet_id', 'projets.id')
            ->join('operateurs', 'localite_operations.operateur_id', 'operateurs.id')
            ->join('operations', 'localite_operations.operation_id', 'operations.id')
            ->join('users', 'localite_operations.user_id', 'users.id')
            ->select('localite_operations.id', 'localites.intitule as localite', 'localites.code_ansade as code_ansade', 'projets.intitule_resume as projet', 'communes.intitule as commune', 'moughatas.intitule as moughata', 'wilayas.intitule as wilaya', 'operations.intitule as operation', 'operateurs.intitule as operateur', 'localite_operations.date_op','localite_operations.confirmed')
            ->where('localite_operations.wilaya_id', $wilaya_id)
            ->where('localite_operations.moughata_id', $moughata_id)
            ->where('localite_operations.commune_id', $commune_id)
            ->where('localite_operations.localite_id', $localite_id)
            ->get();
        return response()->json($localite_reports);
    }

    public function fechDateLocaliteReport(Request $request)
    {
        $from = $request->from;
        $to = $request->to;

        $wilaya_id = $request->wilaya_id;


        $localite_reports = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
            ->join('communes', 'localite_operations.commune_id', 'communes.id')
            ->join('moughatas', 'localite_operations.moughata_id', 'moughatas.id')
            ->join('wilayas', 'localite_operations.wilaya_id', 'wilayas.id')
            ->join('projets', 'localite_operations.projet_id', 'projets.id')
            ->join('operateurs', 'localite_operations.operateur_id', 'operateurs.id')
            ->join('operations', 'localite_operations.operation_id', 'operations.id')
            ->join('users', 'localite_operations.user_id', 'users.id')
            ->select('localite_operations.id', 'localites.intitule as localite', 'localites.code_ansade as code_ansade', 'projets.intitule_resume as projet', 'communes.intitule as commune', 'moughatas.intitule as moughata', 'wilayas.intitule as wilaya', 'operations.intitule as operation', 'operateurs.intitule as operateur', 'localite_operations.date_op','localite_operations.confirmed')
            ->where('localite_operations.wilaya_id', $wilaya_id)
            ->whereBetween('localite_operations.date_op', [$from, $to])
            ->get();
        return response()->json($localite_reports);
    }

    public function fechOperationLocaliteReport(Request $request)
    {
        $operation_id = $request->operation_id;
        $wilaya_id = $request->wilaya_id;

        $localite_reports = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
            ->join('communes', 'localite_operations.commune_id', 'communes.id')
            ->join('moughatas', 'localite_operations.moughata_id', 'moughatas.id')
            ->join('wilayas', 'localite_operations.wilaya_id', 'wilayas.id')
            ->join('projets', 'localite_operations.projet_id', 'projets.id')
            ->join('operateurs', 'localite_operations.operateur_id', 'operateurs.id')
            ->join('operations', 'localite_operations.operation_id', 'operations.id')
            ->join('users', 'localite_operations.user_id', 'users.id')
            ->select('localite_operations.id', 'localites.intitule as localite', 'localites.code_ansade as code_ansade', 'projets.intitule_resume as projet', 'communes.intitule as commune', 'moughatas.intitule as moughata', 'wilayas.intitule as wilaya', 'operations.intitule as operation', 'operateurs.intitule as operateur', 'localite_operations.date_op','localite_operations.confirmed')
            ->where('localite_operations.operation_id', $operation_id)
            ->where('localite_operations.wilaya_id', $wilaya_id)
            ->get();
        return response()->json($localite_reports);
    }
    public function fechProjetLocaliteReport(Request $request)
    {
        $projet_id = $request->projet_id;
        $wilaya_id = $request->wilaya_id;

        $localite_reports = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
            ->join('communes', 'localite_operations.commune_id', 'communes.id')
            ->join('moughatas', 'localite_operations.moughata_id', 'moughatas.id')
            ->join('wilayas', 'localite_operations.wilaya_id', 'wilayas.id')
            ->join('projets', 'localite_operations.projet_id', 'projets.id')
            ->join('operateurs', 'localite_operations.operateur_id', 'operateurs.id')
            ->join('operations', 'localite_operations.operation_id', 'operations.id')
            ->join('users', 'localite_operations.user_id', 'users.id')
            ->select('localite_operations.id', 'localites.intitule as localite', 'localites.code_ansade as code_ansade', 'projets.intitule_resume as projet', 'communes.intitule as commune', 'moughatas.intitule as moughata', 'wilayas.intitule as wilaya', 'operations.intitule as operation', 'operateurs.intitule as operateur', 'localite_operations.date_op','localite_operations.confirmed')
            ->where('localite_operations.projet_id', $projet_id)
            ->where('localite_operations.wilaya_id', $wilaya_id)
            ->get();
        return response()->json($localite_reports);
    }
    public function fechOperateurLocaliteReport(Request $request)
    {
        $operateur_id = $request->operateur_id;
        $projet_id = $request->projet_id;
        $wilaya_id = $request->wilaya_id;

        $localite_reports = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
            ->join('communes', 'localite_operations.commune_id', 'communes.id')
            ->join('moughatas', 'localite_operations.moughata_id', 'moughatas.id')
            ->join('wilayas', 'localite_operations.wilaya_id', 'wilayas.id')
            ->join('projets', 'localite_operations.projet_id', 'projets.id')
            ->join('operateurs', 'localite_operations.operateur_id', 'operateurs.id')
            ->join('operations', 'localite_operations.operation_id', 'operations.id')
            ->join('users', 'localite_operations.user_id', 'users.id')
            ->select('localite_operations.id', 'localites.intitule as localite', 'localites.code_ansade as code_ansade', 'projets.intitule_resume as projet', 'communes.intitule as commune', 'moughatas.intitule as moughata', 'wilayas.intitule as wilaya', 'operations.intitule as operation', 'operateurs.intitule as operateur', 'localite_operations.date_op','localite_operations.confirmed')
            ->where('localite_operations.operateur_id', $operateur_id)
            ->where('localite_operations.projet_id', $projet_id)
            ->where('localite_operations.wilaya_id', $wilaya_id)
            ->get();
        return response()->json($localite_reports);
    }
    public function update(Request $request)
    {


        $id = $request->id;        
        $report = LocaliteReport::where('id', $id)->update(
            [
                'confirmed' => $request->status,
                'remarque' => $request->remarques
            ]
        );
        $report = LocaliteReport::find($id);

        if ($report && $report->confirmed) {
            return redirect('admin/reports/localites')->with('success', 'Nouveau Ligne valide!');
        } else {
            return redirect('admin/reports/localites')->with('error', 'Nouveau Ligne non valide / Rejete!');
        }
        
    }

    public function edit($id)
    {

        $report = LocaliteReport::join('localites', 'localite_operations.localite_id', 'localites.id')
            ->join('communes', 'localite_operations.commune_id', 'communes.id')
            ->join('moughatas', 'localite_operations.moughata_id', 'moughatas.id')
            ->join('wilayas', 'localite_operations.wilaya_id', 'wilayas.id')
            ->join('projets', 'localite_operations.projet_id', 'projets.id')
            ->join('operateurs', 'localite_operations.operateur_id', 'operateurs.id')
            ->join('operations', 'localite_operations.operation_id', 'operations.id')
            ->select('localite_operations.*', 'localites.intitule as localite', 'localites.code_ansade as code_ansade', 'projets.intitule_resume as projet', 'communes.intitule as commune', 'moughatas.intitule as moughata', 'wilayas.intitule as wilaya', 'operations.intitule as operation', 'operations.code as operation_code', 'operateurs.intitule as operateur')
            ->where('localite_operations.id', $id)
            ->first();
             return view('admin.reports.localites.edit', compact('report'));
        
    }
}
