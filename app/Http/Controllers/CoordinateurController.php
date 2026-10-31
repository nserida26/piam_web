<?php

namespace App\Http\Controllers;

use App\Models\Localite;
use App\Models\Role;
use App\Models\User;
use App\Models\Wilaya;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Espace du coordinateur d'ONG : il gère les facilitateurs de son ONG
 * (création, localités, projets) et valide les données qu'ils collectent.
 * Une donnée validée par le coordinateur doit ensuite être validée par
 * l'admin (colonne `confirmed`). Le coordinateur ne voit que son ONG.
 */
class CoordinateurController extends Controller
{
    /** type d'URL => table des rapports */
    private const TABLES = [
        'localite' => 'localite_operations',
        'infrastructure' => 'infrastructure_operations',
    ];

    private function coordinateur(): User
    {
        $user = Auth::user();
        abort_unless($user && $user->operateur_id, 403, "Aucune ONG n'est associée à votre compte. Contactez l'administrateur.");
        return $user;
    }

    // ------------------------------------------------------------------
    // Tableau de bord
    // ------------------------------------------------------------------

    public function dashboard()
    {
        $coord = $this->coordinateur();

        $stats = [];
        foreach (self::TABLES as $type => $table) {
            $base = fn () => $coord->restrictReportsToOng(DB::table($table), $table);
            $stats[$type] = [
                'attente' => $base()->whereNull('statut_coordinateur')->where('confirmed', 0)->count(),
                'valide_coord' => $base()->where('statut_coordinateur', 'valide')->where('confirmed', 0)->count(),
                'rejete' => $base()->where('statut_coordinateur', 'rejete')->where('confirmed', 0)->count(),
                'valide_admin' => $base()->where('confirmed', 1)->count(),
            ];
        }

        $nbFacilitateurs = $this->facilitateursQuery($coord)->count();
        $ong = $coord->operateur;

        return view('admin.coordinateur.dashboard', compact('stats', 'nbFacilitateurs', 'ong'));
    }

    // ------------------------------------------------------------------
    // Facilitateurs
    // ------------------------------------------------------------------

    private function facilitateurRole(): Role
    {
        $role = Role::where('name', 'like', '%facil%')->first();
        abort_unless($role, 500, 'Le rôle Facilitateur est introuvable.');
        return $role;
    }

    private function facilitateursQuery(User $coord)
    {
        $roleId = $this->facilitateurRole()->id;
        return User::where('operateur_id', $coord->operateur_id)
            ->whereIn('id', DB::table('role_user')->where('role_id', $roleId)->pluck('user_id'));
    }

    private function findFacilitateur(User $coord, $id): User
    {
        return $this->facilitateursQuery($coord)->where('id', $id)->firstOrFail();
    }

    public function facilitateurs()
    {
        $coord = $this->coordinateur();
        $facilitateurs = $this->facilitateursQuery($coord)
            ->with(['localites:id,intitule', 'projets:id,intitule_resume'])
            ->orderBy('username')
            ->get();

        return view('admin.coordinateur.facilitateurs.index', compact('facilitateurs'));
    }

    public function createFacilitateur()
    {
        $coord = $this->coordinateur();
        return view('admin.coordinateur.facilitateurs.form', $this->formData($coord, new User));
    }

    public function editFacilitateur($id)
    {
        $coord = $this->coordinateur();
        return view('admin.coordinateur.facilitateurs.form', $this->formData($coord, $this->findFacilitateur($coord, $id)));
    }

    private function formData(User $coord, User $facilitateur): array
    {
        $wilayas = Wilaya::orderBy('intitule')->get();
        $projets = $coord->ongProjets()->orderBy('intitule_resume')->get();

        if (old('localite_ids') !== null) {
            $assignedLocalites = Localite::whereIn('id', (array) old('localite_ids'))->get();
        } else {
            $assignedLocalites = $facilitateur->exists ? $facilitateur->localites()->get() : collect();
        }
        $assignedProjets = old('projet_ids') !== null
            ? array_map('intval', (array) old('projet_ids'))
            : ($facilitateur->exists ? $facilitateur->projets()->pluck('projets.id')->all() : []);

        $alwaysShow = true; // pas de cases de rôle ici : la section est toujours visible

        return compact('facilitateur', 'wilayas', 'projets', 'assignedLocalites', 'assignedProjets', 'alwaysShow');
    }

    private function validateFacilitateur(Request $request, User $coord, ?User $facilitateur = null): array
    {
        $projetIds = $coord->ongProjets()->pluck('id')->all();

        return $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'string', 'max:255', Rule::unique('users', 'email')->ignore($facilitateur->id ?? null)],
            'password' => ($facilitateur ? 'nullable' : 'required') . '|string|min:8|confirmed',
            'localite_ids' => 'required|array|min:1',
            'localite_ids.*' => 'integer|exists:localites,id',
            'projet_ids' => 'nullable|array',
            'projet_ids.*' => ['integer', Rule::in($projetIds)],
        ], [
            'localite_ids.required' => __('user.localites_required'),
            'localite_ids.min' => __('user.localites_required'),
        ]);
    }

    public function storeFacilitateur(Request $request)
    {
        $coord = $this->coordinateur();
        $data = $this->validateFacilitateur($request, $coord);

        DB::transaction(function () use ($data, $coord) {
            $user = new User;
            $user->username = $data['name'];
            $user->email = $data['email'];
            $user->password = bcrypt($data['password']);
            $user->photo = 'default.png';
            $user->operateur_id = $coord->operateur_id;
            $user->created_by = $coord->id;
            $user->save();

            DB::table('role_user')->insert([
                'user_id' => $user->id,
                'role_id' => $this->facilitateurRole()->id,
            ]);
            $user->localites()->sync(array_map('intval', $data['localite_ids']));
            $user->projets()->sync(array_map('intval', $data['projet_ids'] ?? []));
        });

        return redirect()->route('coordinateur.facilitateurs.index')
            ->with('success', 'Facilitateur créé.');
    }

    public function updateFacilitateur(Request $request, $id)
    {
        $coord = $this->coordinateur();
        $user = $this->findFacilitateur($coord, $id);
        $data = $this->validateFacilitateur($request, $coord, $user);

        DB::transaction(function () use ($data, $user) {
            $user->username = $data['name'];
            $user->email = $data['email'];
            if (!empty($data['password'])) {
                $user->password = bcrypt($data['password']);
            }
            $user->save();

            $user->localites()->sync(array_map('intval', $data['localite_ids']));
            $user->projets()->sync(array_map('intval', $data['projet_ids'] ?? []));
        });

        return redirect()->route('coordinateur.facilitateurs.index')
            ->with('success', 'Facilitateur mis à jour.');
    }

    // ------------------------------------------------------------------
    // Validation des données
    // ------------------------------------------------------------------

    private function reportsQuery(User $coord, string $type)
    {
        abort_unless(isset(self::TABLES[$type]), 404);
        $t = self::TABLES[$type];

        $query = DB::table($t)
            ->leftJoin('localites', "$t.localite_id", 'localites.id')
            ->leftJoin('communes', "$t.commune_id", 'communes.id')
            ->leftJoin('operations', "$t.operation_id", 'operations.id')
            ->leftJoin('projets', "$t.projet_id", 'projets.id')
            ->leftJoin('users', "$t.user_id", 'users.id');

        if ($type === 'infrastructure') {
            $query->leftJoin('infrastructures', "$t.infrastructure_id", 'infrastructures.id');
        }

        return $coord->restrictReportsToOng($query, $t);
    }

    public function validation(Request $request)
    {
        $coord = $this->coordinateur();
        $type = $request->get('type', 'localite');
        $statut = $request->get('statut', 'attente');
        $t = self::TABLES[$type] ?? abort(404);

        $query = $this->reportsQuery($coord, $type);

        switch ($statut) {
            case 'attente':
                $query->whereNull("$t.statut_coordinateur")->where("$t.confirmed", 0);
                break;
            case 'valide_coord':
                $query->where("$t.statut_coordinateur", 'valide')->where("$t.confirmed", 0);
                break;
            case 'rejete':
                $query->where("$t.statut_coordinateur", 'rejete')->where("$t.confirmed", 0);
                break;
            case 'valide_admin':
                $query->where("$t.confirmed", 1);
                break;
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('localites.intitule', 'like', "%$q%")
                  ->orWhere('localites.code_ansade', 'like', "%$q%")
                  ->orWhere('users.username', 'like', "%$q%");
            });
        }

        $select = [
            "$t.id", "$t.date_op", "$t.confirmed", "$t.statut_coordinateur", "$t.coordinateur_remarque",
            'localites.intitule as localite', 'localites.code_ansade', 'communes.intitule as commune',
            'operations.intitule as operation', 'projets.intitule_resume as projet', 'users.username as facilitateur',
        ];
        if ($type === 'infrastructure') {
            $select[] = 'infrastructures.intitule_infra_publ as infrastructure';
        }

        $reports = $query->select($select)
            ->orderByDesc("$t.date_op")
            ->orderByDesc("$t.id")
            ->paginate(50)
            ->withQueryString();

        return view('admin.coordinateur.validation', compact('reports', 'type', 'statut'));
    }

    public function reportDetail($type, $id)
    {
        $coord = $this->coordinateur();
        $t = self::TABLES[$type] ?? abort(404);

        $report = $this->reportsQuery($coord, $type)
            ->where("$t.id", $id)
            ->select("$t.*", 'localites.intitule as localite', 'operations.intitule as operation',
                'projets.intitule_resume as projet', 'users.username as facilitateur')
            ->first();
        abort_unless($report, 404);

        // Champs techniques masqués ; on n'affiche que les valeurs saisies.
        $hidden = ['id', 'localite_id', 'wilaya_id', 'moughata_id', 'commune_id', 'operation_id', 'user_id',
            'projet_id', 'operateur_id', 'infrastructure_id', 'mobile_ref', 'coordinateur_id', 'created_at', 'updated_at'];
        $fields = collect((array) $report)
            ->except($hidden)
            ->reject(fn ($v) => $v === null || $v === '')
            ->all();

        return response()->json(['fields' => $fields]);
    }

    public function validateReports(Request $request)
    {
        $coord = $this->coordinateur();
        $data = $request->validate([
            'type' => ['required', Rule::in(array_keys(self::TABLES))],
            'action' => ['required', Rule::in(['valide', 'rejete'])],
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
            'remarque' => ($request->action === 'rejete' ? 'required' : 'nullable') . '|string|max:2000',
        ], [
            'remarque.required' => 'Indiquez le motif du rejet pour le facilitateur.',
            'ids.required' => 'Sélectionnez au moins une ligne.',
        ]);
        $t = self::TABLES[$data['type']];

        // Seulement les rapports de son ONG, pas encore validés par l'admin.
        $ids = $coord->restrictReportsToOng(DB::table($t), $t)
            ->whereIn("$t.id", $data['ids'])
            ->where("$t.confirmed", 0)
            ->pluck("$t.id");

        $count = DB::table($t)->whereIn('id', $ids)->update([
            'statut_coordinateur' => $data['action'],
            'coordinateur_id' => $coord->id,
            'coordinateur_valide_le' => now(),
            'coordinateur_remarque' => $data['remarque'] ?? null,
        ]);

        $msg = $data['action'] === 'valide'
            ? "$count ligne(s) validée(s), en attente de validation par l'administrateur."
            : "$count ligne(s) rejetée(s).";

        return back()->with('success', $msg);
    }
}
