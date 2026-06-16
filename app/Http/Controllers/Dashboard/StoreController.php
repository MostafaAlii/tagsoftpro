<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\DataTables\Dashboard\Admin\StoreDataTable;
use App\Repositories\Contracts\StoreRepositoryInterface;
use App\Models\{Store};
use App\Http\Requests\Dashboard\Store\{StoreStoreRequest};
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function __construct(
        protected StoreDataTable $storeDataTable,
        protected StoreRepositoryInterface $storeInterface
    ) {}

    public function index()
    {
        return $this->storeInterface->index($this->storeDataTable);
    }

    public function store(StoreStoreRequest $request)
    {
        return $this->storeInterface->store($request);
    }

    public function edit(Store $store)
    {
        $store->load('translations');
        return response()->json([
            'success' => true,
            'data' => $store
        ]);
    }

    public function update(Request $request, Store $store)
    {
        return $this->storeInterface->update($store, $request->all());
    }

    public function toggleStatus(Store $store)
    {
        return $this->storeInterface->toggleStatus($store);
    }

    public function destroy(Store $store)
    {
        return $this->storeInterface->destroy($store);
    }
}