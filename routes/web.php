<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\ReportingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WelcomeController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|

*/

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {
        Route::get('/', [WelcomeController::class, 'index']);
        Route::get('/download/apk', function () {
            $path = storage_path('app/downloads/piam.apk');
            abort_unless(file_exists($path), 404);
            return response()->download($path, 'PIAM.apk', [
                'Content-Type' => 'application/vnd.android.package-archive',
            ]);
        })->name('download.apk');
        Route::get('/errors/500', function () {
            return view('errors.500');
        })->name('errors.500');
        Route::group(['prefix' => 'admin', 'middleware' => ['auth:admin', 'account.active', 'restrict.coordinateur', 'restrict.drha']], function () {
            // Espace coordinateur d'ONG (seules pages accessibles à ce rôle)
            Route::group(['prefix' => 'coordinateur', 'middleware' => 'role:Coordinateur'], function () {
                $c = App\Http\Controllers\CoordinateurController::class;
                Route::get('/', [$c, 'dashboard'])->name('coordinateur.dashboard');
                Route::get('/facilitateurs', [$c, 'facilitateurs'])->name('coordinateur.facilitateurs.index');
                Route::get('/facilitateurs/create', [$c, 'createFacilitateur'])->name('coordinateur.facilitateurs.create');
                Route::post('/facilitateurs', [$c, 'storeFacilitateur'])->name('coordinateur.facilitateurs.store');
                Route::get('/facilitateurs/{id}/edit', [$c, 'editFacilitateur'])->name('coordinateur.facilitateurs.edit');
                Route::put('/facilitateurs/{id}', [$c, 'updateFacilitateur'])->name('coordinateur.facilitateurs.update');
                Route::post('/facilitateurs/{id}/toggle-status', [$c, 'toggleFacilitateurStatus'])->name('coordinateur.facilitateurs.toggle_status');
                Route::get('/validation', [$c, 'validation'])->name('coordinateur.validation');
                Route::get('/validation/{type}/{id}', [$c, 'reportDetail'])->name('coordinateur.validation.detail');
                Route::post('/validation', [$c, 'validateReports'])->name('coordinateur.validation.store');
            });

            Route::get('/localites/get-moughatas/{wilaya}', [App\Http\Controllers\LocalitesController::class, 'getMoughatasByWilaya'])
                ->name('admin.localites.getMoughatas');

            Route::get('/localites/get-communes/{moughata}', [App\Http\Controllers\LocalitesController::class, 'getCommunesByMoughata'])
                ->name('admin.localites.getCommunes');
            Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
            Route::post('/fetch_general_wilaya', [DashboardController::class, 'fetch_general_wilaya'])->name('admin.fetch_general_wilaya');

            Route::get('/fech_infrastructures', [DashboardController::class, 'fech_infrastructures'])->name('admin.fech_infrastructures');



            Route::post('api/fetch-moughatas', [ApiController::class, 'fetchMoughata']);
            Route::post('api/fetch-moughatas2', [ApiController::class, 'fetchMoughata2']);
            Route::post('api/fetch-communes', [ApiController::class, 'fetchCommune']);
            Route::post('api/fetch-communes2', [ApiController::class, 'fetchCommune2']);
            Route::post('api/fetch-localites', [ApiController::class, 'fetchLocalite']);
            Route::post('api/fetch-localites2', [ApiController::class, 'fetchLocalite2']);
            Route::post('api/fetch-operations', [ApiController::class, 'fetchOperation']);
            Route::post('api/fetch-operations2', [ApiController::class, 'fetchOperation2']);
            Route::post('api/fetch-projets', [ApiController::class, 'fetchProjet']);
            Route::post('api/fetch-projets2', [ApiController::class, 'fetchProjet2']);
            Route::post('api/fetch-operateurs', [ApiController::class, 'fetchOperateur']);
            Route::post('api/fetch-operateurs2', [ApiController::class, 'fetchOperateur2']);
            Route::post('api/fetch-operateurs3', [ApiController::class, 'fetchOperateur3']);
            Route::get('logout', [LoginController::class, 'logout'])->name('admin.logout');

            Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
            Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
            Route::resource('/marches', App\Http\Controllers\MarcheController::class);
            Route::resource('/projets', App\Http\Controllers\ProjetsController::class);
            Route::resource('/wilayas', App\Http\Controllers\WilayasController::class);
            Route::resource('/operations', App\Http\Controllers\OperationsController::class);
            Route::resource('/moughatas', App\Http\Controllers\MoughatasController::class);
            Route::resource('/communes', App\Http\Controllers\CommunesController::class);
            Route::get('/localites/datatable', [App\Http\Controllers\LocalitesController::class, 'datatable'])->name('admin.localites.datatable');
            Route::resource('/localites', App\Http\Controllers\LocalitesController::class);

            Route::group(['prefix' => 'reorganisation'], function () {
                Route::get('/localites', [App\Http\Controllers\ReorganisationController::class, 'localitesIndex'])->name('admin.reorganisation.localites');
                Route::get('/localites/search', [App\Http\Controllers\ReorganisationController::class, 'searchLocalites'])->name('admin.reorganisation.localites.search');
                Route::post('/localites/assign', [App\Http\Controllers\ReorganisationController::class, 'assignLocalites'])->name('admin.reorganisation.localites.assign');

                Route::get('/communes', [App\Http\Controllers\ReorganisationController::class, 'communesIndex'])->name('admin.reorganisation.communes');
                Route::get('/communes/search', [App\Http\Controllers\ReorganisationController::class, 'searchCommunes'])->name('admin.reorganisation.communes.search');
                Route::post('/communes/assign', [App\Http\Controllers\ReorganisationController::class, 'assignCommunes'])->name('admin.reorganisation.communes.assign');

                Route::get('/moughatas', [App\Http\Controllers\ReorganisationController::class, 'moughatasIndex'])->name('admin.reorganisation.moughatas');
                Route::get('/moughatas/search', [App\Http\Controllers\ReorganisationController::class, 'searchMoughatas'])->name('admin.reorganisation.moughatas.search');
                Route::post('/moughatas/assign', [App\Http\Controllers\ReorganisationController::class, 'assignMoughatas'])->name('admin.reorganisation.moughatas.assign');
            });
            Route::resource('/infrastructures', App\Http\Controllers\InfrastructuresController::class);
            Route::resource('/operateurs', App\Http\Controllers\OperateursController::class);
            //Modifier
            Route::get('/operateurs/{id}/{projet_id?}', [App\Http\Controllers\OperateursController::class, 'show'])->name('admin.operateurs.project.show');
            Route::resource('/projet-wilaya', App\Http\Controllers\ProjetWilayaController::class);

            Route::resource('/wilaya-user', App\Http\Controllers\WilayaUserController::class);
            Route::resource('/user-operateur', App\Http\Controllers\UserOperateurController::class);

            Route::group(['prefix' => 'imports'], function () {
                Route::get('/administrations', [App\Http\Controllers\ImportationController::class, 'administration'])->name('admin.imports.administrations.index');
                Route::get('/reports', [App\Http\Controllers\ImportationController::class, 'report'])->name('admin.imports.reports.index');
                Route::post('/storeAdministrations', [App\Http\Controllers\ImportationController::class, 'storeAdministrations'])->name('admin.imports.administrations.store');
                Route::post('/storeReports', [App\Http\Controllers\ImportationController::class, 'storeReports'])->name('admin.imports.reports.store');
                Route::post('/previewAdministrations', [App\Http\Controllers\ImportationController::class, 'previewAdministrations'])->name('admin.imports.administrations.preview');
                Route::post('/previewReports', [App\Http\Controllers\ImportationController::class, 'previewReports'])->name('admin.imports.reports.preview');
                Route::post('/confirmAdministrations', [App\Http\Controllers\ImportationController::class, 'confirmAdministrations'])->name('admin.imports.administrations.confirm');
                Route::post('/confirmReports', [App\Http\Controllers\ImportationController::class, 'confirmReports'])->name('admin.imports.reports.confirm');
            });

            Route::group(['prefix' => 'maps'], function () {
                Route::get('/', [App\Http\Controllers\MapController::class, 'index'])->name('admin.maps.index');
                Route::post('/fetch-maps', [App\Http\Controllers\MapController::class, 'fech_map'])->name('admin.maps.fech_map');
            });
            Route::group(['prefix' => 'users'], function () {
                Route::get('/', [App\Http\Controllers\UsersController::class, 'index'])->name('admin.users.index');
                Route::get('/create', [App\Http\Controllers\UsersController::class, 'create'])->name('admin.users.create');
                Route::post('/store', [App\Http\Controllers\UsersController::class, 'store'])->name('admin.users.store');
                Route::get('/edit/{id}', [App\Http\Controllers\UsersController::class, 'edit'])->name('admin.users.edit');
                Route::get('/show/{id}', [App\Http\Controllers\UsersController::class, 'show'])->name('admin.users.show');
                Route::put('/update/{id}', [App\Http\Controllers\UsersController::class, 'update'])->name('admin.users.update');
                Route::delete('/destroy/{id}', [App\Http\Controllers\UsersController::class, 'destroy'])->name('admin.users.destroy');
                Route::post('/toggle-status/{id}', [App\Http\Controllers\UsersController::class, 'toggleStatus'])->name('admin.users.toggle_status');

                Route::get('/roles/', [App\Http\Controllers\UsersController::class, 'indexRole'])->name('admin.users.roles');
                Route::get('/roles/create', [App\Http\Controllers\UsersController::class, 'createRole'])->name('admin.users.roles.create');
                Route::post('/roles/store', [App\Http\Controllers\UsersController::class, 'storeRole'])->name('admin.users.roles.store');
                Route::get('/roles/{id}/edit', [App\Http\Controllers\UsersController::class, 'editRole'])->name('admin.users.roles.edit');
                Route::post('/roles/{id}/update', [App\Http\Controllers\UsersController::class, 'updateRole'])->name('admin.users.roles.update');
                //Modifier
                Route::delete('/roles/{id}/destroy', [App\Http\Controllers\UsersController::class, 'destroyRole'])->name('admin.roles.users.destroy');
            });
            Route::group(['prefix' => 'reports'], function () {
                Route::get('/localites', [App\Http\Controllers\LocaliteReportController::class, 'index'])->name('admin.reports.localites');
                Route::post('/localites/data', [App\Http\Controllers\LocaliteReportController::class, 'getData'])->name('admin.reports.localites.data');
                Route::post('/fetch_wilaya_localite_report', [App\Http\Controllers\LocaliteReportController::class, 'fechWilayaLocaliteReport'])->name('admin.reports.localites.fechWilayaLocaliteReport');
                Route::post('/fetch_moughata_localite_report', [App\Http\Controllers\LocaliteReportController::class, 'fechMoughataLocaliteReport'])->name('admin.reports.localites.fechMoughataLocaliteReport');
                Route::post('/fetch_commune_localite_report', [App\Http\Controllers\LocaliteReportController::class, 'fechCommuneLocaliteReport'])->name('admin.reports.localites.fechCommuneLocaliteReport');
                Route::post('/fetch_operation_localite_report', [App\Http\Controllers\LocaliteReportController::class, 'fechOperationLocaliteReport'])->name('admin.reports.localites.fechOperationLocaliteReport');
                Route::post('/fetch_date_localite_report', [App\Http\Controllers\LocaliteReportController::class, 'fechDateLocaliteReport'])->name('admin.reports.localites.fechDateLocaliteReport');
                Route::post('/fetch_localite_localite_report', [App\Http\Controllers\LocaliteReportController::class, 'fechLocaliteLocaliteReport'])->name('admin.reports.localites.fechLocaliteLocaliteReport');
                Route::post('/fetch_projet_localite_report', [App\Http\Controllers\LocaliteReportController::class, 'fechProjetLocaliteReport'])->name('admin.reports.localites.fechProjetLocaliteReport');
                Route::post('/fetch_operateur_localite_report', [App\Http\Controllers\LocaliteReportController::class, 'fechOperateurLocaliteReport'])->name('admin.reports.localites.fechOperateurLocaliteReport');

                Route::get('/infrastructures', [App\Http\Controllers\InfrastructureReportController::class, 'index'])->name('admin.reports.infrastructures');

                Route::post('/fetch_wilaya_infrastructure_report', [App\Http\Controllers\InfrastructureReportController::class, 'fechWilayaInfrastructureReport'])->name('admin.reports.infrastructures.fechWilayaLocaliteReport');
                Route::post('/fetch_moughata_infrastructure_report', [App\Http\Controllers\InfrastructureReportController::class, 'fechMoughataInfrastructureReport'])->name('admin.reports.infrastructures.fechMoughataLocaliteReport');
                Route::post('/fetch_commune_infrastructure_report', [App\Http\Controllers\InfrastructureReportController::class, 'fechCommuneInfrastructureReport'])->name('admin.reports.infrastructures.fechCommuneLocaliteReport');
                Route::post('/fetch_operation_infrastructure_report', [App\Http\Controllers\InfrastructureReportController::class, 'fechOperationInfrastructureReport'])->name('admin.reports.infrastructures.fechOperationLocaliteReport');
                Route::post('/fetch_date_infrastructure_report', [App\Http\Controllers\InfrastructureReportController::class, 'fechDateInfrastructureReport'])->name('admin.reports.infrastructures.fechDateLocaliteReport');
                Route::post('/fetch_localite_infrastructure_report', [App\Http\Controllers\InfrastructureReportController::class, 'fechLocaliteInfrastructureReport'])->name('admin.reports.infrastructures.fechLocaliteLocaliteReport');
                Route::post('/fetch_projet_infrastructure_report', [App\Http\Controllers\InfrastructureReportController::class, 'fechProjetInfrastructureReport'])->name('admin.reports.infrastructures.fechProjetLocaliteReport');
                Route::post('/fetch_operateur_infrastructure_report', [App\Http\Controllers\InfrastructureReportController::class, 'fechOperateurInfrastructureReport'])->name('admin.reports.infrastructures.fechOperateurLocaliteReport');

                Route::get('/localites/edit/{id}', [App\Http\Controllers\LocaliteReportController::class, 'edit'])->name('admin.reports.localites.edit');
                Route::post('/localites/update', [App\Http\Controllers\LocaliteReportController::class, 'update'])->name('admin.reports.localites.update');
                Route::post('/localites/bulk-update', [App\Http\Controllers\LocaliteReportController::class, 'bulkUpdate'])->name('admin.reports.localites.bulkUpdate');
                Route::delete('/localites/destroy/{id}', [App\Http\Controllers\LocaliteReportController::class, 'destroy'])->name('admin.reports.localites.destroy');


                Route::get('/infrastructures/edit/{id}', [App\Http\Controllers\InfrastructureReportController::class, 'edit'])->name('admin.reports.infrastructures.edit');
                Route::post('/infrastructures/update', [App\Http\Controllers\InfrastructureReportController::class, 'update'])->name('admin.reports.infrastructures.update');
                Route::post('/infrastructures/bulk-update', [App\Http\Controllers\InfrastructureReportController::class, 'bulkUpdate'])->name('admin.reports.infrastructures.bulkUpdate');
                Route::delete('/infrastructures/destroy/{id}', [App\Http\Controllers\InfrastructureReportController::class, 'destroy'])->name('admin.reports.infrastructures.destroy');
            });
            Route::group(['prefix' => 'reporting'], function () {

                Route::get('/', [ReportingController::class, 'index'])->name('admin.reporting.index');
                Route::post('/fetch_reporting_wilaya', [ReportingController::class, 'fetch_reporting_wilaya'])->name('admin.reporting.fetch_reporting_wilaya');

                Route::post('/fetch_reporting_moughata', [ReportingController::class, 'fetch_reporting_moughata'])->name('admin.reporting.fetch_reporting_moughata');
                Route::post('/fetch_reporting_commune', [ReportingController::class, 'fetch_reporting_commune'])->name('admin.reporting.fetch_reporting_commune');
                Route::get('/fetch_reporting_localite', [ReportingController::class, 'fetch_reporting_localite'])->name('admin.reporting.fetch_reporting_localite');
                Route::post('/fetch_reporting_localite_api', [ReportingController::class, 'fetch_reporting_localite_api'])->name('admin.reporting.fetch_reporting_localite_api');
                Route::get('/fetch_reporting_annuel', [ReportingController::class, 'fetch_reporting_annuel'])->name('admin.reporting.fetch_reporting_annuel');
                Route::post('/fetch_reporting_annuel_api', [ReportingController::class, 'fetch_reporting_annuel_api'])->name('admin.reporting.fetch_reporting_annuel_api');
                Route::get('/fetch_reporting_projet', [ReportingController::class, 'fetch_reporting_projet'])->name('admin.reporting.fetch_reporting_projet');
                Route::post('/fetch_reporting_projet_api', [ReportingController::class, 'fetch_reporting_projet_api'])->name('admin.reporting.fetch_reporting_projet_api');
            });
        });



        Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => 'guest:admin'], function () {

            Route::get('login', [LoginController::class, 'show_login_view'])->name('admin.showlogin');
            Route::post('login', [LoginController::class, 'login'])->name('admin.login');
        });
    }
);
