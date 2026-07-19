<?php

namespace Modules\Provider\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Provider\DataTables\ProviderDataTable;
use Modules\Provider\Repositories\Contracts\ProviderRepositoryInterface;
use Modules\Provider\Http\Requests\StoreProviderRequest;
use Modules\Provider\Entities\Provider;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    protected $providerRepository;

    public function __construct(ProviderRepositoryInterface $providerRepository)
    {
        $this->providerRepository = $providerRepository;
    }

    public function index(ProviderDataTable $providerDataTable)
    {
        return $this->providerRepository->index($providerDataTable);
    }

    public function store(StoreProviderRequest $request)
    {
        return $this->providerRepository->store($request);
    }

    public function edit(Provider $provider)
    {
        return $this->providerRepository->edit($provider);
    }

    public function update(Request $request, Provider $provider)
    {
        return $this->providerRepository->update($provider, $request->all(), $request);
    }

    public function toggleStatus(Provider $provider)
    {
        return $this->providerRepository->toggleStatus($provider);
    }

    public function destroy(Provider $provider)
    {
        return $this->providerRepository->destroy($provider);
    }

    public function restore($id)
    {
        return $this->providerRepository->restore($id);
    }

    public function forceDelete($id)
    {
        return $this->providerRepository->forceDelete($id);
    }

    public function bulkAction(Request $request)
    {
        return $this->providerRepository->bulkAction($request);
    }

    public function hasTrashed()
    {
        return $this->providerRepository->hasTrashed();
    }
}