<?php

use Modules\Provider\Http\Controllers\ProviderController;
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
            Route::resource('providers', ProviderController::class)->except(['show']);
            Route::patch('providers/{provider}/toggle-status', [ProviderController::class, 'toggleStatus'])->name('providers.toggleStatus');
            Route::patch('providers/{provider}/restore', [ProviderController::class, 'restore'])->name('providers.restore');
            Route::delete('providers/{provider}/force-delete', [ProviderController::class, 'forceDelete'])->name('providers.forceDelete');
            Route::post('providers/bulk-action', [ProviderController::class, 'bulkAction'])->name('providers.bulkAction');
            Route::get('providers/has-trashed', [ProviderController::class, 'hasTrashed'])->name('providers.hasTrashed');
        });
        require base_path('routes/auth.php');
    }
);