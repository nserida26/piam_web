<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::USeBootstrap();

        // DRHA : uniquement les données de sa ou ses wilayas.
        foreach ([
            \App\Models\Wilaya::class,
            \App\Models\Moughata::class,
            \App\Models\Commune::class,
            \App\Models\Localite::class,
            \App\Models\Infrastructure::class,
            \App\Models\LocaliteReport::class,
            \App\Models\InfrastructureReport::class,
            \App\Models\ControleTravaux::class,
        ] as $model) {
            $model::addGlobalScope(new \App\Support\WilayaScope());
        }
    }
}
