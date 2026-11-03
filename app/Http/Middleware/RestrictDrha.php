<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Un compte DRHA ne voit que les données de sa wilaya (voir WilayaScope).
 * Les outils d'administration nationale lui sont fermés : gestion des
 * comptes (il pourrait s'attribuer d'autres wilayas), imports et
 * réorganisation du découpage territorial.
 */
class RestrictDrha
{
    private const BLOCKED = [
        'admin.users.',
        'admin.roles.',
        'wilaya-user.',
        'user-operateur.',
        'admin.imports.',
        'admin.reorganisation.',
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->isDrha()) {
            $name = optional($request->route())->getName() ?? '';
            if (Str::startsWith($name, self::BLOCKED)) {
                if ($request->isMethod('get') && !$request->expectsJson()) {
                    return redirect()->route('admin.dashboard');
                }
                abort(403);
            }
        }

        return $next($request);
    }
}
