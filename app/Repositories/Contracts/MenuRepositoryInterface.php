<?php

namespace App\Repositories\Contracts;

use App\DataTables\Dashboard\Admin\MenuDataTable;
use App\Http\Requests\Dashboard\Menu\StoreMenuRequest;
use App\Models\Menu;
use Illuminate\Http\Request;

interface MenuRepositoryInterface
{
    public function index(MenuDataTable $menuDataTable);
    public function store(StoreMenuRequest $request);
    public function edit(Menu $menu);
    public function update(Menu $menu, array $data, ?Request $request = null);
    public function toggleStatus(Menu $menu);
    public function destroy(Menu $menu);
    public function restore($id);
    public function forceDelete($id);
    public function bulkAction(Request $request);
    public function hasTrashed();

    public function structure(Menu $menu);
    public function addNode(Menu $menu, Request $request);
    public function saveTree(Menu $menu, Request $request);
    public function removeNode(\App\Models\MenuNode $menuNode);
}