<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Certains hébergements (Apache en CGI/FastCGI, double .htaccess racine →
 * public) suppriment l'en-tête « Authorization » : Sanctum reçoit alors une
 * requête sans jeton et répond « Unauthenticated » même juste après la
 * connexion. L'app mobile envoie aussi le jeton dans « X-Auth-Token » ; on
 * reconstruit ici l'en-tête manquant à partir de celui-ci ou des variables
 * serveur de secours.
 */
class RecoverBearerToken
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->bearerToken()) {
            $token = $request->header('X-Auth-Token');

            if (!$token) {
                $raw = $request->server('HTTP_AUTHORIZATION')
                    ?: $request->server('REDIRECT_HTTP_AUTHORIZATION');
                if ($raw && stripos($raw, 'Bearer ') === 0) {
                    $token = substr($raw, 7);
                }
            }

            if ($token) {
                $request->headers->set('Authorization', 'Bearer ' . trim($token));
            }
        }

        return $next($request);
    }
}
