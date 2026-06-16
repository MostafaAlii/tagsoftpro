<?php
use App\Http\Controllers\Auth\{Admin, Company, Client};
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('login', [Admin\AdminAuthenticatedSessionController::class, 'create'])->name('admin.login');
        Route::post('login', [Admin\AdminAuthenticatedSessionController::class, 'store'])->name('admin.post.login');
        Route::get('forgot/password', [Admin\AdminAuthenticatedSessionController::class, 'forgot_password'])->name('admin.forgot.password');
        Route::post('forgot/password', [Admin\AdminAuthenticatedSessionController::class, 'forgot_password_post'])->name('admin.post.forgot.password');
        Route::get('reset/password/{token}', [Admin\AdminAuthenticatedSessionController::class, 'reset_password'])->name('admin.reset.password');
        Route::post('reset/password/{token}', [Admin\AdminAuthenticatedSessionController::class, 'do_reset_password'])->name('admin.do.reset.password');
    });

    Route::prefix('client')->as('client.')->group(function () {
        Route::get('login', [Client\ClientAuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [Client\ClientAuthenticatedSessionController::class, 'store'])->name('post.login');
        Route::get('forgot/password', [Client\ClientAuthenticatedSessionController::class, 'forgot_password'])->name('forgot.password');
        Route::post('forgot/password', [Client\ClientAuthenticatedSessionController::class, 'forgot_password_post'])->name('post.forgot.password');
        Route::get('reset/password/{token}', [Client\ClientAuthenticatedSessionController::class, 'reset_password'])->name('reset.password');
        Route::post('reset/password/{token}', [Client\ClientAuthenticatedSessionController::class, 'do_reset_password'])->name('do.reset.password');
    });

    Route::prefix('company')->as('company.')->group(function () {
        Route::get('login', [Company\CompanyAuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [Company\CompanyAuthenticatedSessionController::class, 'store'])->name('post.login');
        Route::get('forgot/password', [Company\CompanyAuthenticatedSessionController::class, 'forgot_password'])->name('forgot.password');
        Route::post('forgot/password', [Company\CompanyAuthenticatedSessionController::class, 'forgot_password_post'])->name('post.forgot.password');
        Route::get('reset/password/{token}', [Company\CompanyAuthenticatedSessionController::class, 'reset_password'])->name('reset.password');
        Route::post('reset/password/{token}', [Company\CompanyAuthenticatedSessionController::class, 'do_reset_password'])->name('do.reset.password');
    });
});

Route::middleware('auth:admin')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::post('logout', [Admin\AdminAuthenticatedSessionController::class, 'destroy'])->name('admin.logout');
    });
});

Route::middleware('auth:company')->group(function () {
    Route::prefix('company')->as('company.')->group(function () {
        Route::post('logout', [Company\CompanyAuthenticatedSessionController::class, 'destroy'])->name('logout');
    });
});

Route::middleware('auth:client')->group(function () {
    Route::prefix('client')->as('client.')->group(function () {
        Route::post('logout', [Client\ClientAuthenticatedSessionController::class, 'destroy'])->name('logout');
    });
});