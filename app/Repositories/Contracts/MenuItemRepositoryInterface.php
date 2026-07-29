<?php

namespace App\Repositories\Contracts;

use App\DataTables\Dashboard\Admin\MenuItemDataTable;
use App\Http\Requests\Dashboard\MenuItem\StoreMenuItemRequest;
use App\Models\MenuItem;
use Illuminate\Http\Request;

interface MenuItemRepositoryInterface
{
    public function list(Request $request);
    public function toggleOwnerOnly(MenuItem $menuItem);
    public function index(MenuItemDataTable $menuItemDataTable);
    public function store(StoreMenuItemRequest $request);
    public function edit(MenuItem $menuItem);
    public function update(MenuItem $menuItem, array $data, ?Request $request = null);
    public function toggleStatus(MenuItem $menuItem);
    public function destroy(MenuItem $menuItem);
    public function restore($id);
    public function forceDelete($id);
    public function bulkAction(Request $request);
    public function hasTrashed();
}