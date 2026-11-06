<?php

namespace App\Http\Controllers;

use App\Models\Menage;
use App\Models\Operateur;
use App\Models\Projet;
use App\Models\Wilaya;
use App\Services\MenageAggregator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Onglet « Gest. Validations > Ménages » : liste des fiches ménage reçues de
 * l'app mobile, détail d'une fiche, validation / rejet en masse, saisie
 * web d'un ménage. Chaque ajout / suppression recalcule la fiche localité
 * fusionnée (MenageAggregator).
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
        $projets = Projet::orderBy('intitule_resume_fr')->get();
        $operateurs = Operateur::orderBy('intitule_fr')->get();

        return view('admin.reports.menages.index', compact('wilayas', 'projets', 'operateurs'));
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
        $hasChef = Schema::hasColumn('menages', 'nom_chef_menage');
        if ($hasChef) {
            $query->addSelect('menages.nom_chef_menage');
        }

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
            $query->where(function ($q) use ($search, $hasChef) {
                $q->where('localites.intitule', 'like', "%{$search}%")
                  ->orWhere('communes.intitule', 'like', "%{$search}%")
                  ->orWhere('moughatas.intitule', 'like', "%{$search}%")
                  ->orWhere('wilayas.intitule', 'like', "%{$search}%")
                  ->orWhere('users.username', 'like', "%{$search}%");
                if ($hasChef) {
                    $q->orWhere('menages.nom_chef_menage', 'like', "%{$search}%");
                }
            });
        }

        $recordsFiltered = (clone $query)->count();

        // Index de colonne DataTables => colonne SQL triable
        $orderable = [
            1 => 'wilayas.intitule',
            2 => 'moughatas.intitule',
            3 => 'communes.intitule',
            4 => 'localites.intitule',
            5 => $hasChef ? 'menages.nom_chef_menage' : 'menages.id',
            6 => 'operations.intitule',
            7 => 'users.username',
            10 => 'menages.created_at',
            11 => 'menages.confirmed',
        ];
        $orderIndex = (int) $request->input('order.0.column', 10);
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
                'chef' => e($row->nom_chef_menage ?? ''),
                'operation' => e($row->operation),
                'agent' => e($row->agent),
                'personnes' => (int) $row->nbr_h_c + (int) $row->nbr_f_c,
                'latrine' => $row->latr_amel ? 'Améliorée' : 'Non améliorée',
                'dlm' => e($row->dlm),
                'date' => optional($row->created_at)->format('Y-m-d'),
                'confirmed' => (bool) $row->confirmed,
                'remarque' => e($row->remarque),
                'action' => '<button type="button" class="btn btn-sm btn-info btn-detail" data-id="' . $row->id . '" title="Détail">'
                    . '<i class="fas fa-eye"></i></button> '
                    . '<button type="button" class="btn btn-sm btn-danger btn-delete" data-id="' . $row->id . '" title="Supprimer">'
                    . '<i class="fas fa-trash"></i></button>',
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
                'Chef de famille' => $menage->nom_chef_menage ?? '',
                'Localité' => trim($menage->localite . ($menage->code_ansade ? ' (' . $menage->code_ansade . ')' : '')),
                'Opération' => $menage->operation,
                'Agent' => $menage->agent,
                'Date' => optional($menage->created_at)->format('Y-m-d'),
            ],
            'fields' => $fields,
            'confirmed' => (bool) $menage->confirmed,
            'remarque' => $menage->remarque,
            'photo' => \App\Services\MenagePhoto::url($menage->photo ?? null),
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

    /**
     * Saisie web d'un ménage (même structure que l'app mobile). La fiche
     * localité de la localité / opération / projet est recalculée.
     */
    public function store(Request $request)
    {
        if (!Schema::hasColumn('menages', 'nom_chef_menage')) {
            return response()->json(['success' => false, 'message' => 'Migration 2026_11_06_000001 non lancée sur le serveur.'], 422);
        }

        $data = $request->validate([
            'localite_id' => 'required|integer|exists:localites,id',
            'operation_id' => 'required|in:2,4',
            'projet_id' => 'required|integer|exists:projets,id',
            'operateur_id' => 'required|integer|exists:operateurs,id',
            'nom_chef_menage' => 'nullable|string|max:191',
            'nbr_h_c' => 'nullable|integer|min:0',
            'nbr_f_c' => 'nullable|integer|min:0',
            'nbr_enfant_inf_5_ans' => 'nullable|integer|min:0',
            'latr_fam' => 'nullable|in:0,1',
            'latr_amel' => 'nullable|in:0,1',
            'vidange' => 'nullable|in:adeq,inadeq',
            'menages_partage' => 'nullable|integer|min:0',
            'ltr_endommag' => 'nullable|in:0,1',
            'latr_amelior_voisin' => 'nullable|in:0,1',
            'men_dal' => 'nullable|in:0,1',
            'dlm' => 'nullable|in:Sans DLM,DLM Incomplet,DLM Complet',
            'date_op' => 'nullable|date',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:8192',
        ], [], [
            'nom_chef_menage' => 'nom du père de famille',
            'localite_id' => 'localité',
            'projet_id' => 'projet',
            'operateur_id' => 'opérateur',
            'latr_fam' => 'existence de latrine',
            'dlm' => 'dispositif de lavage des mains',
        ]);

        $user = Auth::guard('admin')->user();
        if ($user && !$user->canWorkOnLocalite($data['localite_id'])) {
            return response()->json(['success' => false, 'message' => 'Localité hors de votre périmètre.'], 403);
        }

        $localite = DB::table('localites')->where('id', $data['localite_id'])->first();
        $hasLatrine = (int) ($data['latr_fam'] ?? 0) === 1;
        $amel = $hasLatrine && (int) ($data['latr_amel'] ?? 0) === 1;
        $vidange = $amel ? ($data['vidange'] ?? '') : '';
        $partage = $amel ? (int) ($data['menages_partage'] ?? 0) : 0;
        $now = now()->format('Y-m-d H:i:s');

        $id = DB::table('menages')->insertGetId([
            'nom_chef_menage' => trim((string) ($data['nom_chef_menage'] ?? '')) ?: null,
            'nbr_h_c' => (int) ($data['nbr_h_c'] ?? 0),
            'nbr_f_c' => (int) ($data['nbr_f_c'] ?? 0),
            'nbr_enfant_inf_5_ans' => (int) ($data['nbr_enfant_inf_5_ans'] ?? 0),
            'latr_fam' => $hasLatrine ? 1 : 0,
            'latr_amel' => $amel ? 1 : 0,
            'latr_non_amelior' => $hasLatrine && !$amel ? 1 : 0,
            'latr_amel_vid_adeq' => $vidange === 'adeq' ? 1 : 0,
            'latr_amel_vid_inadeq' => $vidange === 'inadeq' ? 1 : 0,
            'latr_amel_vid_adeq_part' => $vidange === 'adeq' ? $partage : 0,
            'latr_amel_vid_inadeq_part' => $vidange === 'inadeq' ? $partage : 0,
            'ltr_endommag' => $hasLatrine ? (int) ($data['ltr_endommag'] ?? 0) : 0,
            'latr_amelior_voisin' => $hasLatrine ? 0 : (int) ($data['latr_amelior_voisin'] ?? 0),
            'men_dal' => $hasLatrine ? 0 : (int) ($data['men_dal'] ?? 0),
            'dlm' => $data['dlm'] ?? 'Sans DLM',
            'latlng' => '',
            'wilaya_id' => $localite->wilaya_id,
            'moughata_id' => $localite->moughata_id,
            'commune_id' => $localite->commune_id,
            'localite_id' => $localite->id,
            'operation_id' => $data['operation_id'],
            'projet_id' => $data['projet_id'],
            'operateur_id' => $data['operateur_id'],
            'date_op' => $data['date_op'] ?? now()->toDateString(),
            'user_id' => optional($user)->id,
            'created_at' => $now,
            'updated_at' => $now,
        ] + (Schema::hasColumn('menages', 'photo')
            ? ['photo' => \App\Services\MenagePhoto::fromRequest($request, optional($user)->id)]
            : []));

        $row = DB::table('menages')->where('id', $id)->first();
        MenageAggregator::syncForMenages([$row], optional($user)->id);

        return response()->json(['success' => true, 'id' => $id]);
    }

    /** Supprime une fiche ménage (doublon, erreur) et recalcule la fiche localité. */
    public function destroy($id)
    {
        // Passe par le modèle : le scope DRHA limite aux wilayas autorisées.
        $menage = Menage::findOrFail($id);
        $row = DB::table('menages')->where('id', $menage->id)->first();
        $menage->delete();
        \App\Services\MenagePhoto::delete($row->photo ?? null);

        MenageAggregator::syncForMenages([$row], Auth::guard('admin')->id());

        return response()->json(['success' => true]);
    }
}
