<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\DataTables\Dashboard\Admin\MenuDataTable;
use App\Repositories\Contracts\MenuRepositoryInterface;
use App\Http\Requests\Dashboard\Menu\StoreMenuRequest;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    protected $menuRepository;

    public function __construct(MenuRepositoryInterface $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    public function index(MenuDataTable $menuDataTable)
    {
        return $this->menuRepository->index($menuDataTable);
    }

    public function store(StoreMenuRequest $request)
    {
        return $this->menuRepository->store($request);
    }

    public function edit(Menu $menu)
    {
        return $this->menuRepository->edit($menu);
    }

    public function update(Request $request, Menu $menu)
    {
        return $this->menuRepository->update($menu, $request->all(), $request);
    }

    public function toggleStatus(Menu $menu)
    {
        return $this->menuRepository->toggleStatus($menu);
    }

    public function destroy(Menu $menu)
    {
        return $this->menuRepository->destroy($menu);
    }

    public function restore($id)
    {
        return $this->menuRepository->restore($id);
    }

    public function forceDelete($id)
    {
        return $this->menuRepository->forceDelete($id);
    }

    public function bulkAction(Request $request)
    {
        return $this->menuRepository->bulkAction($request);
    }

    public function hasTrashed()
    {
        return $this->menuRepository->hasTrashed();
    }
}