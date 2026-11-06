<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Supervision des coordinateurs par l'admin : pour chaque coordinateur,
 * l'état de sa file de validation ; un clic ouvre son espace (mêmes écrans
 * que le coordinateur, routes supervision.coordinateurs.*) où l'admin peut
 * valider ou rejeter à sa place.
 */
class SupervisionCoordinateurController extends Controller
{
    const TABLES = ['localite_operations', 'infrastructure_operations'];

    public function index()
    {
        abort_unless(Auth::user()->canSuperviseCoordinateurs(), 403);

        $roleIds = Role::where('name', 'like', '%coordinat%')->pluck('id');
        $coordinateurs = User::whereIn('id', DB::table('role_user')->whereIn('role_id', $roleIds)->pluck('user_id'))
            ->with(['operateur:id,intitule', 'wilayas:id,intitule'])
            ->orderBy('username')
            ->get();

        $facilRoleIds = Role::where('name', 'like', '%facil%')->pluck('id');
        $facilitateurs = DB::table('role_user')->whereIn('role_id', $facilRoleIds)->pluck('user_id');

        foreach ($coordinateurs as $c) {
            $stats = ['attente' => 0, 'valide_coord' => 0, 'rejete' => 0, 'derniere' => null];
            if ($c->operateur_id) {
                foreach (self::TABLES as $t) {
                    $base = fn () => $c->restrictReportsToOng(DB::table($t), $t)->where("$t.confirmed", 0);
                    $stats['attente'] += $base()->whereNull("$t.statut_coordinateur")->count();
                    $stats['valide_coord'] += $base()->where("$t.statut_coordinateur", 'valide')->count();
                    $stats['rejete'] += $base()->where("$t.statut_coordinateur", 'rejete')->count();
                    $last = $c->restrictReportsToOng(DB::table($t), $t)->max("$t.coordinateur_valide_le");
                    if ($last && (!$stats['derniere'] || $last > $stats['derniere'])) {
                        $stats['derniere'] = $last;
                    }
                }
            }
            $c->stats = $stats;
            $c->nb_facilitateurs = $c->operateur_id
                ? User::where('operateur_id', $c->operateur_id)->whereIn('id', $facilitateurs)->count()
                : 0;
        }

        return view('admin.supervision.coordinateurs', compact('coordinateurs'));
    }
}
