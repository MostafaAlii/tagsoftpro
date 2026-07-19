<?php

use Modules\Zone\Http\Controllers\ZoneController;
use Illuminate\Support\Facades\Route;
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
        Route::group(['middleware' => 'auth:admin', 'prefix' => 'admin', 'as' => 'admin.'], function () {
            Route::resource('zones', ZoneController::class)->except(['show']);
            Route::patch('zones/{zone}/toggle-status', [ZoneController::class, 'toggleStatus'])->name('zones.toggleStatus');
            Route::patch('zones/{zone}/restore', [ZoneController::class, 'restore'])->name('zones.restore');
            Route::delete('zones/{zone}/force-delete', [ZoneController::class, 'forceDelete'])->name('zones.forceDelete');
            Route::post('zones/bulk-action', [ZoneController::class, 'bulkAction'])->name('zones.bulkAction');
            Route::get('zones/has-trashed', [ZoneController::class, 'hasTrashed'])->name('zones.hasTrashed');
        });
        require base_path('routes/auth.php');
    }
);