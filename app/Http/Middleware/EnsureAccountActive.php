<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Compte désactivé pendant qu'il était connecté au web : la session est
 * fermée à la requête suivante. (Côté mobile, la désactivation révoque les
 * jetons Sanctum.)
 */
class EnsureAccountActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('admin')->user();

        if ($user && !$user->isActive()) {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                abort(403, __('auth.disabled'));
            }
            return redirect()->route('admin.showlogin')->with(['error' => __('auth.disabled')]);
        }

        return $next($request);
    }
}
