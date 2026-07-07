<?php

namespace App\Http\Controllers;

use App\Models\InfrastructureReport;
use App\Models\Operation;
use App\Models\Projet;
use App\Models\User;
use App\Models\Operateur;

use App\Models\Moughata;
use App\Models\Commune;

use App\Models\Localite;
use App\Models\Infrastructure;
use Illuminate\Http\Request;
use App\Models\Wilaya;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InfrastructureReportController extends Controller
{
    public function index()
    {
        $user = Auth::id();
        $wilayas_id = Wilaya::join('wilaya_users', 'wilaya_users.wilaya_id', 'wilayas.id')
            ->join('users', 'wilaya_users.user_id', 'users.id')
            ->where('users.id', $user)->pluck('wilaya_id');

        $wilayas = Wilaya::whereIn('wilayas.id', $wilayas_id)->get();
        $operations = Operation::all();
        //operateurs_projets
        $operateurs = Operateur::all();
        //wilayas_projets
        $projets = Projet::all();
        return view('admin.reports.infrastructures.index', compact('projets', 'operateurs', 'operations', 'wilayas'));
    }
    //
    public function fechWilayaInfrastructureReport(Request $request)
    {
        $wilaya_id = $request->wilaya_id;

        $localite_reports = InfrastructureReport::join('localites', 'infrastructure_operations.localite_id', 'localites.id')
            ->join('communes', 'infrastructure_operations.commune_id', 'communes.id')
            ->join('moughatas', 'infrastructure_operations.moughata_id', 'moughatas.id')
            ->join('wilayas', 'infrastructure_operations.wilaya_id', 'wilayas.id')
            ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
            ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
            ->join('operations', 'infrastructure_operations.operation_id', 'operations.id')
            ->join('users', 'infrastructure_operations.user_id', 'users.id')
            ->select('infrastructure_operations.id', 'localites.intitule as localite', 'localites.code_ansade as code_ansade', 'projets.intitule_resume as projet', 'communes.intitule as commune', 'moughatas.intitule as moughata', 'wilayas.intitule as wilaya', 'operations.intitule as operation', 'operateurs.intitule as operateur', 'infrastructure_operations.date_op','confirmed')
            ->where('infrastructure_operations.wilaya_id', $wilaya_id)

            ->get();
        return response()->json($localite_reports);
    }



    public function fechMoughataInfrastructureReport(Request $request)
    {
        $moughata_id = $request->moughata_id;
        $wilaya_id = $request->wilaya_id;
        $localite_reports = InfrastructureReport::join('localites', 'infrastructure_operations.localite_id', 'localites.id')
            ->join('communes', 'infrastructure_operations.commune_id', 'communes.id')
            ->join('moughatas', 'infrastructure_operations.moughata_id', 'moughatas.id')
            ->join('wilayas', 'infrastructure_operations.wilaya_id', 'wilayas.id')
            ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
            ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
            ->join('operations', 'infrastructure_operations.operation_id', 'operations.id')
            ->join('users', 'infrastructure_operations.user_id', 'users.id')
            ->select('infrastructure_operations.id', 'localites.intitule as localite', 'localites.code_ansade as code_ansade', 'projets.intitule_resume as projet', 'communes.intitule as commune', 'moughatas.intitule as moughata', 'wilayas.intitule as wilaya', 'operations.intitule as operation', 'operateurs.intitule as operateur', 'infrastructure_operations.date_op','confirmed')
            ->where('infrastructure_operations.wilaya_id', $wilaya_id)
            ->where('infrastructure_operations.moughata_id', $moughata_id)
            ->get();
        return response()->json($localite_reports);
    }

    public function fechCommuneInfrastructureReport(Request $request)
    {
        $moughata_id = $request->moughata_id;
        $wilaya_id = $request->wilaya_id;
        $commune_id = $request->commune_id;
        $localite_reports = InfrastructureReport::join('localites', 'infrastructure_operations.localite_id', 'localites.id')
            ->join('communes', 'infrastructure_operations.commune_id', 'communes.id')
            ->join('moughatas', 'infrastructure_operations.moughata_id', 'moughatas.id')
            ->join('wilayas', 'infrastructure_operations.wilaya_id', 'wilayas.id')
            ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
            ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
            ->join('operations', 'infrastructure_operations.operation_id', 'operations.id')
            ->join('users', 'infrastructure_operations.user_id', 'users.id')
            ->select('infrastructure_operations.id', 'localites.intitule as localite', 'localites.code_ansade as code_ansade', 'projets.intitule_resume as projet', 'communes.intitule as commune', 'moughatas.intitule as moughata', 'wilayas.intitule as wilaya', 'operations.intitule as operation', 'operateurs.intitule as operateur', 'infrastructure_operations.date_op','confirmed')
            ->where('infrastructure_operations.wilaya_id', $wilaya_id)
            ->where('infrastructure_operations.moughata_id', $moughata_id)
            ->where('infrastructure_operations.commune_id', $commune_id)
            ->get();
        return response()->json($localite_reports);
    }
    public function fechLocaliteInfrastructureReport(Request $request)
    {
        $moughata_id = $request->moughata_id;
        $wilaya_id = $request->wilaya_id;
        $commune_id = $request->commune_id;
        $localite_id = $request->localite_id;
        $localite_reports = InfrastructureReport::join('localites', 'infrastructure_operations.localite_id', 'localites.id')
            ->join('communes', 'infrastructure_operations.commune_id', 'communes.id')
            ->join('moughatas', 'infrastructure_operations.moughata_id', 'moughatas.id')
            ->join('wilayas', 'infrastructure_operations.wilaya_id', 'wilayas.id')
            ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
            ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
            ->join('operations', 'infrastructure_operations.operation_id', 'operations.id')
            ->join('users', 'infrastructure_operations.user_id', 'users.id')
            ->select('infrastructure_operations.id', 'localites.intitule as localite', 'localites.code_ansade as code_ansade', 'projets.intitule_resume as projet', 'communes.intitule as commune', 'moughatas.intitule as moughata', 'wilayas.intitule as wilaya', 'operations.intitule as operation', 'operateurs.intitule as operateur', 'infrastructure_operations.date_op','confirmed')
            ->where('infrastructure_operations.wilaya_id', $wilaya_id)
            ->where('infrastructure_operations.moughata_id', $moughata_id)
            ->where('infrastructure_operations.commune_id', $commune_id)
            ->where('infrastructure_operations.localite_id', $localite_id)
            ->get();
        return response()->json($localite_reports);
    }

    public function fechDateInfrastructureReport(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $operateur_id = $request->operateur_id;
        $projet_id = $request->projet_id;
        $moughata_id = $request->moughata_id;
        $wilaya_id = $request->wilaya_id;
        $commune_id = $request->commune_id;
        $localite_id = $request->localite_id;
        $operation_id = $request->operation_id;

        $localite_reports = InfrastructureReport::join('localites', 'infrastructure_operations.localite_id', 'localites.id')
            ->join('communes', 'infrastructure_operations.commune_id', 'communes.id')
            ->join('moughatas', 'infrastructure_operations.moughata_id', 'moughatas.id')
            ->join('wilayas', 'infrastructure_operations.wilaya_id', 'wilayas.id')
            ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
            ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
            ->join('operations', 'infrastructure_operations.operation_id', 'operations.id')
            ->join('users', 'infrastructure_operations.user_id', 'users.id')
            ->select('infrastructure_operations.id', 'localites.intitule as localite', 'localites.code_ansade as code_ansade', 'projets.intitule_resume as projet', 'communes.intitule as commune', 'moughatas.intitule as moughata', 'wilayas.intitule as wilaya', 'operations.intitule as operation', 'operateurs.intitule as operateur', 'infrastructure_operations.date_op','confirmed')
            ->where('infrastructure_operations.operateur_id', $operateur_id)
            ->where('infrastructure_operations.projet_id', $projet_id)
            ->where('infrastructure_operations.operation_id', $operation_id)
            ->where('infrastructure_operations.wilaya_id', $wilaya_id)
            ->where('infrastructure_operations.moughata_id', $moughata_id)
            ->where('infrastructure_operations.commune_id', $commune_id)
            ->where('infrastructure_operations.localite_id', $localite_id)
            ->whereBetween('infrastructure_operations.date_op', [$from, $to])
            ->get();
        return response()->json($localite_reports);
    }

    public function fechOperationInfrastructureReport(Request $request)
    {
        $operation_id = $request->operation_id;
        $moughata_id = $request->moughata_id;
        $wilaya_id = $request->wilaya_id;
        $commune_id = $request->commune_id;
        $localite_id = $request->localite_id;

        $localite_reports = InfrastructureReport::join('localites', 'infrastructure_operations.localite_id', 'localites.id')
            ->join('communes', 'infrastructure_operations.commune_id', 'communes.id')
            ->join('moughatas', 'infrastructure_operations.moughata_id', 'moughatas.id')
            ->join('wilayas', 'infrastructure_operations.wilaya_id', 'wilayas.id')
            ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
            ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
            ->join('operations', 'infrastructure_operations.operation_id', 'operations.id')
            ->join('users', 'infrastructure_operations.user_id', 'users.id')
            ->select('infrastructure_operations.id', 'localites.intitule as localite', 'localites.code_ansade as code_ansade', 'projets.intitule_resume as projet', 'communes.intitule as commune', 'moughatas.intitule as moughata', 'wilayas.intitule as wilaya', 'operations.intitule as operation', 'operateurs.intitule as operateur', 'infrastructure_operations.date_op','confirmed')
            ->where('infrastructure_operations.operation_id', $operation_id)
            ->where('infrastructure_operations.wilaya_id', $wilaya_id)
            ->where('infrastructure_operations.moughata_id', $moughata_id)
            ->where('infrastructure_operations.commune_id', $commune_id)
            ->where('infrastructure_operations.localite_id', $localite_id)
            ->get();
        return response()->json($localite_reports);
    }
    public function fechProjetInfrastructureReport(Request $request)
    {
        $projet_id = $request->projet_id;
        $moughata_id = $request->moughata_id;
        $wilaya_id = $request->wilaya_id;
        $commune_id = $request->commune_id;
        $localite_id = $request->localite_id;
        $operation_id = $request->operation_id;

        $localite_reports = InfrastructureReport::join('localites', 'infrastructure_operations.localite_id', 'localites.id')
            ->join('communes', 'infrastructure_operations.commune_id', 'communes.id')
            ->join('moughatas', 'infrastructure_operations.moughata_id', 'moughatas.id')
            ->join('wilayas', 'infrastructure_operations.wilaya_id', 'wilayas.id')
            ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
            ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
            ->join('operations', 'infrastructure_operations.operation_id', 'operations.id')
            ->join('users', 'infrastructure_operations.user_id', 'users.id')
            ->select('infrastructure_operations.id', 'localites.intitule as localite', 'localites.code_ansade as code_ansade', 'projets.intitule_resume as projet', 'communes.intitule as commune', 'moughatas.intitule as moughata', 'wilayas.intitule as wilaya', 'operations.intitule as operation', 'operateurs.intitule as operateur', 'infrastructure_operations.date_op','confirmed')
            ->where('infrastructure_operations.projet_id', $projet_id)
            ->where('infrastructure_operations.operation_id', $operation_id)
            ->where('infrastructure_operations.wilaya_id', $wilaya_id)
            ->where('infrastructure_operations.moughata_id', $moughata_id)
            ->where('infrastructure_operations.commune_id', $commune_id)
            ->where('infrastructure_operations.localite_id', $localite_id)
            ->get();
        return response()->json($localite_reports);
    }
    public function fechOperateurInfrastructureReport(Request $request)
    {
        $operateur_id = $request->operateur_id;
        $projet_id = $request->projet_id;
        $moughata_id = $request->moughata_id;
        $wilaya_id = $request->wilaya_id;
        $commune_id = $request->commune_id;
        $localite_id = $request->localite_id;

        $operation_id = $request->operation_id;

        $localite_reports = InfrastructureReport::join('localites', 'infrastructure_operations.localite_id', 'localites.id')
            ->join('communes', 'infrastructure_operations.commune_id', 'communes.id')
            ->join('moughatas', 'infrastructure_operations.moughata_id', 'moughatas.id')
            ->join('wilayas', 'infrastructure_operations.wilaya_id', 'wilayas.id')
            ->join('projets', 'infrastructure_operations.projet_id', 'projets.id')
            ->join('operateurs', 'infrastructure_operations.operateur_id', 'operateurs.id')
            ->join('operations', 'infrastructure_operations.operation_id', 'operations.id')
            ->join('users', 'infrastructure_operations.user_id', 'users.id')
            ->select('infrastructure_operations.id', 'localites.intitule as localite', 'localites.code_ansade as code_ansade', 'projets.intitule_resume as projet', 'communes.intitule as commune', 'moughatas.intitule as moughata', 'wilayas.intitule as wilaya', 'operations.intitule as operation', 'operateurs.intitule as operateur', 'infrastructure_operations.date_op','confirmed')
            ->where('infrastructure_operations.operateur_id', $operateur_id)
            ->where('infrastructure_operations.projet_id', $projet_id)
            ->where('infrastructure_operations.operation_id', $operation_id)
            ->where('infrastructure_operations.wilaya_id', $wilaya_id)
            ->where('infrastructure_operations.moughata_id', $moughata_id)
            ->where('infrastructure_operations.commune_id', $commune_id)
            ->where('infrastructure_operations.localite_id', $localite_id)
            ->get();
        return response()->json($localite_reports);
    }


    public function update(Request $request)
    {
        


        $id = $request->id;
        

        $r = InfrastructureReport::findOrFail($id);
        
        $r->update(
            [
                'confirmed' => $request->status,
                'remarques' => $request->remarques ?? $request->remarque
            ]
        );
        
        $report = InfrastructureReport::findOrFail($id);
        if($report->confirmed){
            return redirect('admin/reports/infrastructures')->with('success', 'Nouveau Ligne valide!');
        }else{
            return redirect('admin/reports/infrastructures')->with('errors', 'Nouveau Ligne non valide / Rejete!');
        }
        
    }

    public function destroy($id)
    {
        $report = InfrastructureReport::find($id);

        if (!is_null($report)) {
            $delete = $report->delete();

            if ($delete) {
                $notification = array(
                    'message' => 'Report has been deleted successfully.',
                    'alert-type' => 'success'
                );

                return redirect()->route('admin.reports.infrastructures')->with($notification);
            } else {

                $notification = array(
                    'message' => 'Oops! Something went wrong',
                    'alert-type' => 'error'
                );

                return redirect()->back()->with($notification);
            }
        }
    }

public function edit($id)
{
    $report = InfrastructureReport::with([
        'wilaya', 
        'moughata', 
        'commune', 
        'localite', 
        'operation', 
        'projet', 
        'operateur',
        'infrastructure'
    ])->findOrFail($id);
    
    return view('admin.reports.infrastructures.edit', compact('report'));
}
/*
public function update(Request $request, $id)
{
    $report = InfrastructureReport::findOrFail($id);
    
    $validated = $request->validate([
        'date_op' => 'nullable|date',
        'effectif' => 'nullable|integer|min:0',
        'effectif_garcon' => 'nullable|integer|min:0',
        'effectif_fille' => 'nullable|integer|min:0',
        'approv_eau' => 'nullable|boolean',
        'dist_approv_eau' => 'nullable|numeric|min:0',
        'nbr_bloc' => 'nullable|integer|min:0',
        'nb_dlm' => 'nullable|integer|min:0',
        'nb_dlm_complet' => 'nullable|integer|min:0',
        'nb_dlm_incomplet' => 'nullable|integer|min:0',
        'nb_sans_dlm' => 'nullable|integer|min:0',
        'type_latr_publ' => 'nullable|string|max:50',
        'nbr_cabine_exist' => 'nullable|integer|min:0',
        'nbr_cabine_fonct' => 'nullable|integer|min:0',
        'dist_cab_hom_fem' => 'nullable|string|max:100',
        'nbr_cabine_fem_hyg' => 'nullable|integer|min:0',
        'gest_dechet' => 'nullable|boolean',
        'gest_dechet_biomed' => 'nullable|boolean',
        'nbr_cabine_pmr' => 'nullable|integer|min:0',
        'nbr_cabine_a_rehab' => 'nullable|integer|min:0',
        'nbr_bloc_a_const' => 'nullable|integer|min:0',
        'nbr_cabine_a_const' => 'nullable|integer|min:0',
        'remarques' => 'nullable|string',
        'status' => 'nullable|boolean'
    ]);
    
    $report->update($validated);
    
    return redirect()->route('admin.reports.infrastructures.index')
        ->with('success', __('infrastructure.updated_successfully'));
}
*/
}
