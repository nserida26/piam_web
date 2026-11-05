<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
//use App\Http\Controllers\Auth\CodeCheckController;
//use App\Http\Controllers\Auth\ResetPasswordController;
//use App\Http\Controllers\Auth\ForgotPasswordController;

 // Password reset routes
//Route::post('password/email',  ForgotPasswordController::class);
//Route::post('password/code/check', CodeCheckController::class);
//Route::post('password/reset', ResetPasswordController::class);

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware("localization")->group(function () {
    Route::controller(App\Http\Controllers\Api\AuthController::class)->group(function(){

        Route::post('register', 'register');
    
        Route::post('login', 'login');
    
    });
    
    
    // Diagnostic de synchronisation (sans authentification, aucun secret renvoyé)
    Route::match(['get', 'post'], 'diagnostic', App\Http\Controllers\Api\DiagnosticController::class);

    Route::post('password/forgot', [App\Http\Controllers\Api\ForgotPasswordController::class,'forgot']);

    
    Route::middleware("auth:sanctum")->group(function () {
        Route::controller(App\Http\Controllers\Api\AuthController::class)->group(function(){
            
            Route::post('/localites', [App\Http\Controllers\Api\LocaliteController::class, 'store']);

            Route::post('localite_reports', [App\Http\Controllers\Api\ReportController::class,'store_localite_reports']);
            Route::post('menage_reports', [App\Http\Controllers\Api\ReportController::class,'store_menage_reports']);
            Route::post('infrastructure_reports', [App\Http\Controllers\Api\ReportController::class,'store_infrastructure_reports']);
            Route::post('controle-travaux', [App\Http\Controllers\Api\ReportController::class,'store_controle_travaux']);


            Route::get('/', [App\Http\Controllers\Api\HomeController::class,'index']);
            Route::get('/reports', [App\Http\Controllers\Api\ReportController::class,'index']);

            Route::patch('/reports/{id}', [App\Http\Controllers\Api\ReportController::class,'update']);
            
            
            Route::get('logout', 'logout');
            

        
        });
    });
});
