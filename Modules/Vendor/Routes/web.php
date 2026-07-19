<?php

use Modules\Vendor\Http\Controllers\VendorController;
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
            Route::resource('vendors', VendorController::class)->except(['show']);
            Route::patch('vendors/{vendor}/toggle-status', [VendorController::class, 'toggleStatus'])->name('vendors.toggleStatus');
            Route::patch('vendors/{vendor}/restore', [VendorController::class, 'restore'])->name('vendors.restore');
            Route::delete('vendors/{vendor}/force-delete', [VendorController::class, 'forceDelete'])->name('vendors.forceDelete');
            Route::post('vendors/bulk-action', [VendorController::class, 'bulkAction'])->name('vendors.bulkAction');
            Route::get('vendors/has-trashed', [VendorController::class, 'hasTrashed'])->name('vendors.hasTrashed');
        });
        require base_path('routes/auth.php');
    }
);