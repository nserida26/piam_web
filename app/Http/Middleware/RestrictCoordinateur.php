<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Un coordinateur d'ONG ne voit que les données de son ONG : il n'a accès
 * qu'à son espace (routes « coordinateur.* ») et à quelques routes communes.
 * Toute autre page du panneau admin le renvoie vers son tableau de bord.
 */
class RestrictCoordinateur
{
    private const ALLOWED = [
        'admin.logout',
        'profile',
        'profile.update',
        // Listes en cascade / recherche utilisées par le choix des localités
        'admin.localites.getMoughatas',
        'admin.localites.getCommunes',
        'admin.reorganisation.localites.search',
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->isCoordinateur()) {
            $name = optional($request->route())->getName() ?? '';
            $allowed = Str::startsWith($name, 'coordinateur.') || in_array($name, self::ALLOWED, true);

            if (!$allowed) {
                if ($request->isMethod('get') && !$request->expectsJson()) {
                    return redirect()->route('coordinateur.dashboard');
                }
                abort(403);
            }
        }

        return $next($request);
    }
}
