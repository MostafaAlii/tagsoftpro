<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\DataTables\Dashboard\Admin\MenuItemDataTable;
use App\Repositories\Contracts\MenuItemRepositoryInterface;
use App\Http\Requests\Dashboard\MenuItem\StoreMenuItemRequest;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    protected $menuItemRepository;

    public function __construct(MenuItemRepositoryInterface $menuItemRepository)
    {
        $this->menuItemRepository = $menuItemRepository;
    }

    public function index(MenuItemDataTable $menuItemDataTable)
    {
        return $this->menuItemRepository->index($menuItemDataTable);
    }

    public function store(StoreMenuItemRequest $request)
    {
        return $this->menuItemRepository->store($request);
    }

    public function edit(MenuItem $menuItem)
    {
        return $this->menuItemRepository->edit($menuItem);
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        return $this->menuItemRepository->update($menuItem, $request->all(), $request);
    }

    public function toggleStatus(MenuItem $menuItem)
    {
        return $this->menuItemRepository->toggleStatus($menuItem);
    }

    public function destroy(MenuItem $menuItem)
    {
        return $this->menuItemRepository->destroy($menuItem);
    }

    public function restore($id)
    {
        return $this->menuItemRepository->restore($id);
    }

    public function forceDelete($id)
    {
        return $this->menuItemRepository->forceDelete($id);
    }

    public function bulkAction(Request $request)
    {
        return $this->menuItemRepository->bulkAction($request);
    }

    public function hasTrashed()
    {
        return $this->menuItemRepository->hasTrashed();
    }
}