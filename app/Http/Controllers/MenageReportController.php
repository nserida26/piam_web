<?php

namespace App\Http\Controllers;

use App\Models\Menage;
use App\Models\Wilaya;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Onglet « Gest. Validations > Ménages » : liste des fiches ménage reçues de
 * l'app mobile, détail d'une fiche, validation / rejet en masse.
 */
class MenageReportController extends Controller
{
    /** Libellés des champs de la fiche (ordre d'affichage du détail). */
    private const FIELDS = [
        'nbr_h_c' => 'Hommes',
        'nbr_f_c' => 'Femmes',
        'nbr_enfant_inf_5_ans' => 'Enfants < 5 ans',
        'latr_fam' => 'Latrine familiale',
        'latr_amel' => 'Latrine améliorée',
        'latr_non_amelior' => 'Latrine non améliorée',
        'latr_amel_vid_adeq' => 'Vidange adéquate',
        'latr_amel_vid_inadeq' => 'Vidange inadéquate',
        'latr_amel_vid_adeq_part' => 'Vidange adéquate (partagée)',
        'latr_amel_vid_inadeq_part' => 'Vidange inadéquate (partagée)',
        'ltr_endommag' => 'Latrine endommagée',
        'latr_amelior_voisin' => 'Latrine améliorée du voisin',
        'men_dal' => 'Ménage en DAL',
        'dlm' => 'DLM',
        'mont_invest_men' => 'Montant investi',
        'invest_men_dispo' => 'Investissement disponible',
        'latr_realise' => 'Latrine réalisée',
        'latr_auto_const' => 'Auto-construite',
        'latr_realise_macon' => 'Réalisée par maçon',
        'latr_autofin' => 'Autofinancée',
        'latr_entraid' => 'Entraide',
        'latr_subv' => 'Subventionnée',
        'latr_en_constr' => 'En construction',
        'latlng' => 'Coordonnées GPS',
    ];

    public function index()
    {
        $wilayas = Wilaya::orderBy('intitule')->get();

        return view('admin.reports.menages.index', compact('wilayas'));
    }

    private function baseQuery()
    {
        return Menage::query()
            ->leftJoin('localites', 'menages.localite_id', '=', 'localites.id')
            ->leftJoin('communes', 'menages.commune_id', '=', 'communes.id')
            ->leftJoin('moughatas', 'menages.moughata_id', '=', 'moughatas.id')
            ->leftJoin('wilayas', 'menages.wilaya_id', '=', 'wilayas.id')
            ->leftJoin('operations', 'menages.operation_id', '=', 'operations.id')
            ->leftJoin('users', 'menages.user_id', '=', 'users.id');
    }

    public function getData(Request $request)
    {
        $query = $this->baseQuery()->select([
            'menages.id',
            'wilayas.intitule as wilaya',
            'moughatas.intitule as moughata',
            'communes.intitule as commune',
            'localites.intitule as localite',
            'operations.intitule as operation',
            'users.username as agent',
            'menages.nbr_h_c',
            'menages.nbr_f_c',
            'menages.latr_amel',
            'menages.dlm',
            'menages.created_at',
            'menages.confirmed',
            'menages.remarque',
        ]);

        foreach (['wilaya_id', 'moughata_id', 'commune_id', 'localite_id', 'operation_id'] as $filter) {
            if ($request->filled($filter)) {
                $query->where('menages.' . $filter, $request->input($filter));
            }
        }
        if ($request->filled('statut')) {
            $query->where('menages.confirmed', $request->statut === 'valide' ? 1 : 0);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('menages.created_at', [$request->from . ' 00:00:00', $request->to . ' 23:59:59']);
        }

        $recordsTotal = (clone $query)->count();

        $search = $request->input('search.value');
        if ($search !== null && $search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('localites.intitule', 'like', "%{$search}%")
                  ->orWhere('communes.intitule', 'like', "%{$search}%")
                  ->orWhere('moughatas.intitule', 'like', "%{$search}%")
                  ->orWhere('wilayas.intitule', 'like', "%{$search}%")
                  ->orWhere('users.username', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $query)->count();

        // Index de colonne DataTables => colonne SQL triable
        $orderable = [
            1 => 'wilayas.intitule',
            2 => 'moughatas.intitule',
            3 => 'communes.intitule',
            4 => 'localites.intitule',
            5 => 'operations.intitule',
            6 => 'users.username',
            9 => 'menages.created_at',
            10 => 'menages.confirmed',
        ];
        $orderIndex = (int) $request->input('order.0.column', 9);
        $orderDir = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($orderable[$orderIndex] ?? 'menages.created_at', $orderDir);

        $length = (int) $request->input('length', 50);
        $query->skip((int) $request->input('start', 0));
        if ($length > 0) {
            $query->take($length);
        }

        $rows = $query->get()->map(function ($row) {
            return [
                'id' => $row->id,
                'checkbox' => '<input type="checkbox" class="row-checkbox" value="' . $row->id . '">',
                'wilaya' => e($row->wilaya),
                'moughata' => e($row->moughata),
                'commune' => e($row->commune),
                'localite' => e($row->localite),
                'operation' => e($row->operation),
                'agent' => e($row->agent),
                'personnes' => (int) $row->nbr_h_c + (int) $row->nbr_f_c,
                'latrine' => $row->latr_amel ? 'Améliorée' : 'Non améliorée',
                'dlm' => e($row->dlm),
                'date' => optional($row->created_at)->format('Y-m-d'),
                'confirmed' => (bool) $row->confirmed,
                'remarque' => e($row->remarque),
                'action' => '<button type="button" class="btn btn-sm btn-info btn-detail" data-id="' . $row->id . '" title="Détail">'
                    . '<i class="fas fa-eye"></i></button>',
            ];
        });

        return response()->json([
            'draw' => (int) $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }

    /** Détail d'une fiche (JSON pour la fenêtre modale). */
    public function show($id)
    {
        $menage = $this->baseQuery()
            ->select('menages.*', 'wilayas.intitule as wilaya', 'moughatas.intitule as moughata',
                'communes.intitule as commune', 'localites.intitule as localite',
                'localites.code_ansade', 'operations.intitule as operation', 'users.username as agent')
            ->where('menages.id', $id)
            ->firstOrFail();

        $fields = [];
        foreach (self::FIELDS as $key => $label) {
            $fields[] = ['label' => $label, 'value' => $menage->{$key}];
        }

        return response()->json([
            'id' => $menage->id,
            'entete' => [
                'Wilaya' => $menage->wilaya,
                'Moughataa' => $menage->moughata,
                'Commune' => $menage->commune,
                'Localité' => trim($menage->localite . ($menage->code_ansade ? ' (' . $menage->code_ansade . ')' : '')),
                'Opération' => $menage->operation,
                'Agent' => $menage->agent,
                'Date' => optional($menage->created_at)->format('Y-m-d'),
            ],
            'fields' => $fields,
            'confirmed' => (bool) $menage->confirmed,
            'remarque' => $menage->remarque,
        ]);
    }

    public function bulkUpdate(Request $request)
    {
        $ids = array_map('intval', (array) $request->input('ids', []));

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Aucune ligne sélectionnée.'], 422);
        }

        $update = [
            'confirmed' => (bool) $request->status,
            'valide_par' => Auth::guard('admin')->id(),
            'valide_le' => now(),
        ];
        if ($request->filled('remarque')) {
            $update['remarque'] = $request->remarque;
        }

        // Le scope DRHA limite la mise à jour aux fiches de ses wilayas.
        $count = Menage::whereIn('id', $ids)->update($update);

        return response()->json(['success' => true, 'count' => $count]);
    }
}
