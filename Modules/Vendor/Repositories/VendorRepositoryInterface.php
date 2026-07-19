<?php
namespace Modules\Vendor\Repositories;
use Modules\Vendor\DataTables\VendorDataTable;
use Modules\Vendor\Entities\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Vendor\Http\Requests\StoreVendorRequest;
interface VendorRepositoryInterface {
    public function index(VendorDataTable $vendorDataTable);
    public function store(StoreVendorRequest $request);
    public function edit(Vendor $vendor);
    public function update(Vendor $vendor, array $data, ?Request $request = null);
    public function toggleStatus(Vendor $vendor);
    public function destroy(Vendor $vendor);
    public function restore($id);
    public function forceDelete($id);
    public function bulkAction(Request $request): JsonResponse;
    public function hasTrashed();    
}