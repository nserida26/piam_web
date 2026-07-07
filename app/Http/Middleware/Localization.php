<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App;
class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        /** 
         * requests hasHeader is used to check the app-language-x header from the REST API's
         */
        if ($request->hasHeader("app-language-x")) {
            /**
             * If app-language-x header found then set it to the default locale
             */
            App::setLocale($request->header("app-language-x"));
        }
        return $next($request);
    }
}
