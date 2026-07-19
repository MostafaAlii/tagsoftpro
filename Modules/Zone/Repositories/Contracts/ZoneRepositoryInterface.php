<?php

namespace Modules\Zone\Repositories\Contracts;

use Modules\Zone\DataTables\ZoneDataTable;
use Modules\Zone\Entities\Zone;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Zone\Http\Requests\StoreZoneRequest;

interface ZoneRepositoryInterface {
    public function index(ZoneDataTable $zoneDataTable);
    public function store(StoreZoneRequest $request);
    public function edit(Zone $zone);
    public function update(Zone $zone, array $data, ?Request $request = null);
    public function toggleStatus(Zone $zone);
    public function destroy(Zone $zone);
    public function restore($id);
    public function forceDelete($id);
    public function bulkAction(Request $request): JsonResponse;
    public function hasTrashed();
}