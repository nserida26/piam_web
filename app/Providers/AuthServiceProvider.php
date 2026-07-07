<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */

    public function boot()
    {
        $this->registerPolicies();

        //
                /** Gates for user crud */
                /*Gate::define('read', function () {

                    $userPermissions = Auth::user()->permissions->pluck('name');

                    if($userPermissions->contains('read')) {
                        return true;
                    } else {
                        return false;
                    }

                });

                Gate::define('write', function () {

                    $userPermissions = Auth::user()->permissions->pluck('name');

                    if($userPermissions->contains('write')) {
                        return true;
                    } else {
                        return false;
                    }

                });

                Gate::define('edit', function () {

                    $userPermissions = Auth::user()->permissions->pluck('name');

                    if($userPermissions->contains('edit')) {
                        return true;
                    } else {
                        return false;
                    }

                });

                Gate::define('delete', function () {

                    $userPermissions = Auth::user()->permissions->pluck('name');

                    if($userPermissions->contains('delete')) {
                        return true;
                    } else {
                        return false;
                    }

                });
                 */

    }

}
