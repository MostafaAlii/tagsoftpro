<?php

use App\Http\Controllers\Dashboard;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
/*
|--------------------------------------------------------------------------
| Dashboard Routes
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
        Route::group(['middleware' => 'auth:admin', 'prefix' => 'admin', 'as' => 'admin.'], function () {
            Route::get('dashboard', Dashboard\DashboardController::class)->name('dashboard');
            Route::controller(Dashboard\MainSettingsController::class)->prefix('mainSettings')->as('mainSettings.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('store', 'store')->name('store');
            });
            Route::middleware(['ensure.owner'])->group(function () {
                Route::resource('clients', Dashboard\ClientController::class);
                Route::post('clients/{client}/companies/store', [Dashboard\ClientController::class, 'storeCompany'])->name('clients.companies.store');
                Route::patch('clients/{client}/companies/{company}/status', [Dashboard\ClientController::class, 'updateCompanyStatus'])->name('clients.companies.updateStatus');
            });


            Route::resource('features', Dashboard\FeatureController::class);
            Route::patch('features/{feature}/toggle-status', [Dashboard\FeatureController::class, 'toggleStatus'])->name('features.toggleStatus');
            Route::patch('features/{feature}/toggle-scope', [Dashboard\FeatureController::class, 'toggleScope'])->name('features.toggleScope');

            Route::resource('plans', Dashboard\PlanController::class);
            Route::patch('plans/{plan}/toggle-status', [Dashboard\PlanController::class, 'toggleStatus'])->name('plans.toggleStatus');
            Route::patch('plans/{plan}/billing_cycle', [Dashboard\PlanController::class,'toggleBillingCycle'])->name('plans.toggleBillingCycle');
            Route::get('plans/{plan}/features', [Dashboard\PlanController::class, 'getFeatures'])->name('plans.getFeatures');
            Route::post('plans/{plan}/features', [Dashboard\PlanController::class, 'updateFeatures'])->name('plans.updateFeatures');

            Route::resource('modules', Dashboard\ModuleController::class);
            Route::patch('modules/{module}/toggle-status', [Dashboard\ModuleController::class, 'toggleStatus'])->name('modules.toggleStatus');

            Route::resource('projectTypes', Dashboard\ProjectTypeController::class);
            Route::patch('projectTypes/{projectType}/toggle-status', [Dashboard\ProjectTypeController::class, 'toggleStatus'])->name('projectTypes.toggleStatus');

            Route::resource('projects', Dashboard\ProjectController::class);
            Route::patch('projects/{project}/toggle-status', [Dashboard\ProjectController::class, 'toggleStatus'])->name('projects.toggleStatus');
            
        });
        require __DIR__ . '../../auth.php';
    }
);