<?php

namespace Modules\Vendor\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Vendor\DataTables\VendorDataTable;
use Modules\Vendor\Repositories\VendorRepositoryInterface;
use Modules\Vendor\Http\Requests\StoreVendorRequest;
use Modules\Vendor\Entities\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    protected $vendorRepository;

    public function __construct(VendorRepositoryInterface $vendorRepository)
    {
        $this->vendorRepository = $vendorRepository;
    }

    public function index(VendorDataTable $vendorDataTable)
    {
        return $this->vendorRepository->index($vendorDataTable);
    }

    public function store(StoreVendorRequest $request)
    {
        return $this->vendorRepository->store($request);
    }

    public function edit(Vendor $vendor)
    {
        return $this->vendorRepository->edit($vendor);
    }

    public function update(Request $request, Vendor $vendor)
    {
        return $this->vendorRepository->update($vendor, $request->all(), $request);
    }

    public function toggleStatus(Vendor $vendor)
    {
        return $this->vendorRepository->toggleStatus($vendor);
    }

    public function destroy(Vendor $vendor)
    {
        return $this->vendorRepository->destroy($vendor);
    }

    public function restore($id)
    {
        return $this->vendorRepository->restore($id);
    }

    public function forceDelete($id)
    {
        return $this->vendorRepository->forceDelete($id);
    }

    public function bulkAction(Request $request)
    {
        return $this->vendorRepository->bulkAction($request);
    }

    public function hasTrashed()
    {
        return $this->vendorRepository->hasTrashed();
    }
}