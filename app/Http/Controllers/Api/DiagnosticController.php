<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * GET/POST /api/diagnostic — SANS authentification, pour déboguer la
 * synchronisation mobile : indique si le jeton arrive jusqu'à PHP (ou s'il
 * est supprimé par l'hébergeur), s'il est valide, et l'état du compte
 * (actif, rôles, localités / projets assignés). Ne renvoie aucun secret.
 */
class DiagnosticController extends Controller
{
    public const VERSION = '2026-11-05b';

    public function __invoke(Request $request)
    {
        $raw = [
            'header_authorization' => (bool) $request->header('Authorization'),
            'header_x_auth_token' => (bool) $request->header('X-Auth-Token'),
            'server_HTTP_AUTHORIZATION' => !empty($request->server('HTTP_AUTHORIZATION')),
            'server_REDIRECT_HTTP_AUTHORIZATION' => !empty($request->server('REDIRECT_HTTP_AUTHORIZATION')),
            'bearer_token_lu_par_laravel' => (bool) $request->bearerToken(),
        ];

        $token = $request->bearerToken()
            ?: $request->header('X-Auth-Token')
            ?: (preg_match('/Bearer\s+(\S+)/i', (string) ($request->server('HTTP_AUTHORIZATION') ?: $request->server('REDIRECT_HTTP_AUTHORIZATION')), $m) ? $m[1] : null);

        $result = [
            'status' => 'ok',
            'diagnostic_version' => self::VERSION,
            'php' => PHP_VERSION,
            'middleware_recover_bearer_installe' => class_exists(\App\Http\Middleware\RecoverBearerToken::class),
            'colonnes_menages' => [
                'confirmed' => \Schema::hasColumn('menages', 'confirmed'),
                'nom_chef_menage' => \Schema::hasColumn('menages', 'nom_chef_menage'),
                'projet_id' => \Schema::hasColumn('menages', 'projet_id'),
            ],
            'entetes_recus' => $raw,
            'jeton_recu' => (bool) $token,
            'jeton_valide' => false,
        ];

        if (!$token) {
            $result['conclusion'] = $raw['header_authorization'] || $raw['server_HTTP_AUTHORIZATION']
                ? 'En-tête reçu mais jeton illisible.'
                : "Aucun jeton reçu : l'hébergeur supprime l'en-tête Authorization (et X-Auth-Token), ou l'app n'est pas connectée.";
            return response()->json($result);
        }

        $access = PersonalAccessToken::findToken($token);
        if (!$access || !$access->tokenable) {
            $result['conclusion'] = 'Jeton reçu mais inconnu du serveur : il a été supprimé (déconnexion sur un autre appareil, compte désactivé/modifié, base réinitialisée). Se reconnecter.';
            return response()->json($result);
        }

        $user = $access->tokenable;
        $localites = $user->allowedLocaliteIds();

        $result['jeton_valide'] = true;
        $result['compte'] = [
            'id' => $user->id,
            'username' => $user->username,
            'actif' => method_exists($user, 'isActive') ? $user->isActive() : null,
            'roles' => $user->roles->pluck('name'),
            'operateur_id' => $user->operateur_id,
            'nb_localites_assignees' => $localites === null ? 'aucune restriction' : count($localites),
            'localites_assignees' => $localites === null ? [] : array_slice($localites, 0, 50),
            'projets' => DB::table('user_projets')->where('user_id', $user->id)->pluck('projet_id'),
            'nb_jetons_actifs' => $user->tokens()->count(),
        ];

        if ($request->filled('localite_id')) {
            $result['test_localite'] = [
                'localite_id' => (int) $request->localite_id,
                'autorisee' => $user->canWorkOnLocalite($request->localite_id),
            ];
        }

        $result['conclusion'] = 'Jeton OK : l\'authentification fonctionne. Si un envoi échoue, voir le message renvoyé pour ce formulaire.';
        return response()->json($result);
    }
}
