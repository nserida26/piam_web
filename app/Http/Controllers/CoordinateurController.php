<?php

namespace App\Http\Controllers;

use App\Models\Localite;
use App\Models\Operateur;
use App\Models\Projet;
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

    /** Coordinateur supervisé par l'admin (null = le coordinateur connecté). */
    private ?User $supervise = null;

    /**
     * Le coordinateur dont on affiche l'espace : celui qui est connecté, ou
     * — routes « supervision.coordinateurs.* » avec {coord} — le coordinateur
     * que l'admin vérifie / à la place duquel il valide.
     */
    private function coordinateur(): User
    {
        $user = Auth::user();
        $coordId = request()->route('coord');
        if ($coordId !== null) {
            abort_unless($user->canSuperviseCoordinateurs(), 403);
            $coord = User::findOrFail($coordId);
            abort_unless($coord->isCoordinateur() && $coord->operateur_id, 404);
            return $this->supervise = $coord;
        }
        if (!$user->operateur_id) {
            // Compte coordinateur sans ONG (ex. rôle ajouté à un compte existant) :
            // page explicative plutôt qu'une erreur 403.
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->view('admin.coordinateur.no_ong')
            );
        }
        return $user;
    }

    /** Préfixe + paramètres des routes des vues (espace coordinateur ou supervision admin). */
    private function routes(): array
    {
        return $this->supervise
            ? ['prefix' => 'supervision.coordinateurs.', 'params' => ['coord' => $this->supervise->id], 'supervise' => $this->supervise]
            : ['prefix' => 'coordinateur.', 'params' => [], 'supervise' => null];
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
        $sansWilaya = $coord->allowedWilayaIds() === null;

        $routes = $this->routes();
        return view('admin.coordinateur.dashboard', compact('stats', 'nbFacilitateurs', 'ong', 'sansWilaya', 'routes'));
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
            ->with(['localites:id,intitule', 'projets:id,intitule_resume', 'operateursAutorises:id,intitule'])
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
        $restrictWilayaIds = $coord->allowedWilayaIds();
        $wilayas = Wilaya::when($restrictWilayaIds !== null, fn ($q) => $q->whereIn('id', $restrictWilayaIds))
            ->orderBy('intitule')->get();
        // Tous les projets et opérateurs sont proposés à la création.
        $projets = Projet::orderBy('intitule_resume')->get();
        $operateurs = Operateur::orderBy('intitule')->get();

        if (old('localite_ids') !== null) {
            $assignedLocalites = Localite::whereIn('id', (array) old('localite_ids'))->get();
        } else {
            $assignedLocalites = $facilitateur->exists ? $facilitateur->localites()->get() : collect();
        }
        $assignedProjets = old('projet_ids') !== null
            ? array_map('intval', (array) old('projet_ids'))
            : ($facilitateur->exists ? $facilitateur->projets()->pluck('projets.id')->all() : []);
        $assignedOperateurs = old('operateur_ids') !== null
            ? array_map('intval', (array) old('operateur_ids'))
            : ($facilitateur->exists ? $facilitateur->operateursAutorises()->pluck('operateurs.id')->all() : []);

        $alwaysShow = true; // pas de cases de rôle ici : la section est toujours visible

        return compact('facilitateur', 'wilayas', 'projets', 'operateurs', 'assignedLocalites', 'assignedProjets', 'assignedOperateurs', 'alwaysShow', 'restrictWilayaIds');
    }

    private function validateFacilitateur(Request $request, User $coord, ?User $facilitateur = null): array
    {
        $wilayaIds = $coord->allowedWilayaIds();
        $localiteRule = $wilayaIds === null
            ? 'integer|exists:localites,id'
            : ['integer', Rule::exists('localites', 'id')->where(fn ($q) => $q->whereIn('wilaya_id', $wilayaIds))];

        return $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'string', 'max:255', Rule::unique('users', 'email')->ignore($facilitateur->id ?? null)],
            'password' => ($facilitateur ? 'nullable' : 'required') . '|string|min:8|confirmed',
            'localite_ids' => 'required|array|min:1',
            'localite_ids.*' => $localiteRule,
            'projet_ids' => 'nullable|array',
            'projet_ids.*' => 'integer|exists:projets,id',
            'operateur_ids' => 'nullable|array',
            'operateur_ids.*' => 'integer|exists:operateurs,id',
        ], [
            'localite_ids.required' => __('user.localites_required'),
            'localite_ids.min' => __('user.localites_required'),
            'localite_ids.*.exists' => 'Une des localités choisies est hors de votre wilaya.',
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
            $user->operateursAutorises()->sync(array_map('intval', $data['operateur_ids'] ?? []));
            $user->wilayas()->sync($coord->allowedWilayaIds() ?? []);
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
            $user->operateursAutorises()->sync(array_map('intval', $data['operateur_ids'] ?? []));
        });

        return redirect()->route('coordinateur.facilitateurs.index')
            ->with('success', 'Facilitateur mis à jour.');
    }

    /** Désactive / réactive un facilitateur de son ONG (données conservées). */
    public function toggleFacilitateurStatus($id)
    {
        $user = $this->findFacilitateur($this->coordinateur(), $id);
        $user->setActive(!$user->isActive());

        return redirect()->route('coordinateur.facilitateurs.index')->with('success', __(
            $user->isActive() ? 'user.enabled' : 'user.disabled',
            ['name' => $user->username]
        ));
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

        $routes = $this->routes();
        return view('admin.coordinateur.validation', compact('reports', 'type', 'statut', 'routes'));
    }

    public function reportDetail(Request $request)
    {
        // Paramètres lus par nom : la route de supervision a {coord} en plus.
        $type = $request->route('type');
        $id = (int) $request->route('id');
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
            'projet_id', 'operateur_id', 'infrastructure_id', 'mobile_ref', 'coordinateur_id', 'created_at', 'updated_at',
            'latlng'];
        $fields = collect((array) $report)
            ->except($hidden)
            ->reject(fn ($v) => $v === null || $v === '')
            ->all();

        return response()->json(['fields' => $fields] + \App\Services\ReportMedia::forReport($t, $report));
    }

    /**
     * Suppression (sélection multiple) de données envoyées par les
     * facilitateurs de l'ONG : seulement celles pas encore validées par
     * l'admin. Une fiche localité fusionnée emporte ses ménages (sinon elle
     * serait recréée au prochain ménage). Photos supprimées, facilitateur
     * prévenu.
     */
    public function deleteReports(Request $request)
    {
        $coord = $this->coordinateur();
        $data = $request->validate([
            'type' => ['required', Rule::in(array_keys(self::TABLES))],
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
            'remarque' => 'nullable|string|max:2000',
        ], ['ids.required' => 'Sélectionnez au moins une ligne.']);
        $t = self::TABLES[$data['type']];

        $rows = $coord->restrictReportsToOng(DB::table($t), $t)
            ->whereIn("$t.id", $data['ids'])
            ->where("$t.confirmed", 0)
            ->get(["$t.id", "$t.mobile_ref", "$t.localite_id", "$t.operation_id", "$t.projet_id"]);
        if ($rows->isEmpty()) {
            return back()->with('error', 'Aucune ligne supprimable (déjà validée par l\'admin ou hors de votre ONG).');
        }
        $ids = $rows->pluck('id')->map(fn ($id) => (int) $id)->all();

        $acteur = Auth::user();
        \App\Services\Notifier::suppression($t, $ids, $data['remarque'] ?? null, $acteur,
            $this->supervise ? "l'administrateur (à la place du coordinateur)" : 'le coordinateur');

        $nbMenages = 0;
        DB::transaction(function () use ($rows, $t, &$nbMenages) {
            foreach ($rows as $r) {
                if ($t === 'localite_operations' && strpos((string) $r->mobile_ref, 'menages:') === 0) {
                    $menageIds = DB::table('menages')->where('localite_id', $r->localite_id)
                        ->where('operation_id', $r->operation_id)->where('projet_id', $r->projet_id)
                        ->pluck('id');
                    foreach ($menageIds as $mid) {
                        \App\Services\ReportPhotos::deleteAll('menages', (int) $mid);
                    }
                    $nbMenages += DB::table('menages')->whereIn('id', $menageIds)->delete();
                }
                \App\Services\ReportPhotos::deleteAll($t, (int) $r->id);
                DB::table($t)->where('id', $r->id)->delete();
            }
        });

        return back()->with('success', count($ids) . ' ligne(s) supprimée(s)'
            . ($nbMenages ? " (dont $nbMenages ménage(s) des fiches fusionnées)" : '') . '. Les facilitateurs ont été prévenus.');
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

        // Admin qui valide à la place du coordinateur : tracé dans la remarque.
        $acteur = Auth::user();
        $remarque = $data['remarque'] ?? null;
        if ($this->supervise) {
            $remarque = trim('[Admin ' . $acteur->username . '] ' . $remarque);
        }

        $count = DB::table($t)->whereIn('id', $ids)->update([
            'statut_coordinateur' => $data['action'],
            'coordinateur_id' => $acteur->id,
            'coordinateur_valide_le' => now(),
            'coordinateur_remarque' => $remarque,
        ]);

        // Le facilitateur est prévenu du rejet (appli mobile + web).
        if ($data['action'] === 'rejete') {
            \App\Services\Notifier::rejet($t, $ids->all(), $data['remarque'] ?? null, $acteur,
                $this->supervise ? "l'administrateur (à la place du coordinateur)" : 'le coordinateur');
        }

        $msg = $data['action'] === 'valide'
            ? "$count ligne(s) validée(s), en attente de validation par l'administrateur."
            : "$count ligne(s) rejetée(s).";

        return back()->with('success', $msg);
    }
}
