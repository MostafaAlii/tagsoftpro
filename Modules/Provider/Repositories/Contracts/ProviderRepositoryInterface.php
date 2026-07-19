<?php

namespace Modules\Provider\Repositories\Contracts;

use Modules\Provider\DataTables\ProviderDataTable;
use Modules\Provider\Entities\Provider;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Provider\Http\Requests\StoreProviderRequest;
interface ProviderRepositoryInterface
{
    public function index(ProviderDataTable $providerDataTable);
    public function store(StoreProviderRequest $request);
    public function edit(Provider $provider);
    public function update(Provider $provider, array $data, ?Request $request = null);
    public function toggleStatus(Provider $provider);
    public function destroy(Provider $provider);
    public function restore($id);
    public function forceDelete($id);
    public function bulkAction(Request $request): JsonResponse;
    public function hasTrashed();
}