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

            Route::resource('departments', Dashboard\DepartmentController::class);
            Route::patch('departments/{department}/toggle-status', [Dashboard\DepartmentController::class, 'toggleStatus'])->name('departments.toggleStatus');

            Route::patch('employees/{employee}/toggle-status', [Dashboard\EmployeeController::class, 'toggleStatus'])->name('employees.toggleStatus');
            Route::patch('employees/{employee}/restore', [Dashboard\EmployeeController::class, 'restore'])->name('employees.restore');
            Route::delete('employees/{employee}/force-delete', [Dashboard\EmployeeController::class, 'forceDelete'])->name('employees.forceDelete');
            Route::get('employees/has-trashed', [Dashboard\EmployeeController::class, 'hasTrashed'])->name('employees.hasTrashed');
            Route::post('employees/bulk-action', [Dashboard\EmployeeController::class, 'bulkAction'])->name('employees.bulkAction');
            Route::resource('employees', Dashboard\EmployeeController::class);

            Route::resource('themes', Dashboard\ThemeController::class);
            Route::get('themes/{theme}/statuses', [Dashboard\ThemeController::class, 'getStatuses'])->name('themes.statuses');
            Route::post('themes/{theme}/bulk-update', [Dashboard\ThemeController::class, 'bulkUpdate'])->name('themes.bulkUpdate');
            Route::patch('themes/{theme}/toggle-status/{projectType}', [Dashboard\ThemeController::class, 'toggleStatus'])->name('themes.toggleStatus');
            Route::patch('themes/{theme}/toggle-default/{projectType}', [Dashboard\ThemeController::class, 'toggleDefault'])->name('themes.toggleDefault');

            Route::patch('permission_groups/{permission_group}/toggle-status', [Dashboard\PermissionGroupController::class, 'toggleStatus'])->name('permission_groups.toggleStatus');
            Route::patch('permission_groups/{permission_group}/restore', [Dashboard\PermissionGroupController::class, 'restore'])->name('permission_groups.restore');
            Route::delete('permission_groups/{permission_group}/force-delete', [Dashboard\PermissionGroupController::class, 'forceDelete'])->name('permission_groups.forceDelete');
            Route::post('permission_groups/bulk-action', [Dashboard\PermissionGroupController::class, 'bulkAction'])->name('permission_groups.bulkAction');
            Route::get('permission_groups/has-trashed', [Dashboard\PermissionGroupController::class, 'hasTrashed'])->name('permission_groups.hasTrashed');
            Route::resource('permission_groups', Dashboard\PermissionGroupController::class)->except(['show']);


            Route::get('menus/{menu}/structure', [Dashboard\MenuController::class, 'structure'])->name('menus.structure');
            Route::post('menus/{menu}/structure/add-node', [Dashboard\MenuController::class, 'addNode'])->name('menus.structure.addNode');
            Route::post('menus/{menu}/structure/save-tree', [Dashboard\MenuController::class, 'saveTree'])->name('menus.structure.saveTree');
            Route::delete('menu-nodes/{menuNode}', [Dashboard\MenuController::class, 'removeNode'])->name('menus.structure.removeNode');
            
            Route::patch('menus/{menu}/toggle-status', [Dashboard\MenuController::class, 'toggleStatus'])->name('menus.toggleStatus');
            Route::patch('menus/{menu}/restore', [Dashboard\MenuController::class, 'restore'])->name('menus.restore');
            Route::delete('menus/{menu}/force-delete', [Dashboard\MenuController::class, 'forceDelete'])->name('menus.forceDelete');
            Route::post('menus/bulk-action', [Dashboard\MenuController::class, 'bulkAction'])->name('menus.bulkAction');
            Route::get('menus/has-trashed', [Dashboard\MenuController::class, 'hasTrashed'])->name('menus.hasTrashed');
            Route::resource('menus', Dashboard\MenuController::class)->except(['show']);

            Route::get('menu_items/list', [Dashboard\MenuItemController::class, 'list'])->name('menu_items.list');
            Route::patch('menu_items/{menu_item}/toggle-owner', [Dashboard\MenuItemController::class, 'toggleOwnerOnly'])->name('menu_items.toggleOwnerOnly');
            Route::patch('menu_items/{menu_item}/toggle-status', [Dashboard\MenuItemController::class, 'toggleStatus'])->name('menu_items.toggleStatus');
            Route::patch('menu_items/{menu_item}/restore', [Dashboard\MenuItemController::class, 'restore'])->name('menu_items.restore');
            Route::delete('menu_items/{menu_item}/force-delete', [Dashboard\MenuItemController::class, 'forceDelete'])->name('menu_items.forceDelete');
            Route::post('menu_items/bulk-action', [Dashboard\MenuItemController::class, 'bulkAction'])->name('menu_items.bulkAction');
            Route::get('menu_items/has-trashed', [Dashboard\MenuItemController::class, 'hasTrashed'])->name('menu_items.hasTrashed');
            Route::resource('menu_items', Dashboard\MenuItemController::class)->except(['show']);
        });
        require __DIR__ . '../../auth.php';
    }
);