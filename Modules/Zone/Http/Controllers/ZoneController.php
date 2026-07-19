<?php

namespace Modules\Zone\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Zone\DataTables\ZoneDataTable;
use Modules\Zone\Repositories\Contracts\ZoneRepositoryInterface;
use Modules\Zone\Http\Requests\StoreZoneRequest;
use Modules\Zone\Entities\Zone;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    protected $zoneRepository;

    public function __construct(ZoneRepositoryInterface $zoneRepository)
    {
        $this->zoneRepository = $zoneRepository;
    }

    public function index(ZoneDataTable $zoneDataTable)
    {
        return $this->zoneRepository->index($zoneDataTable);
    }

    public function store(StoreZoneRequest $request)
    {
        return $this->zoneRepository->store($request);
    }

    public function edit(Zone $zone)
    {
        return $this->zoneRepository->edit($zone);
    }

    public function update(Request $request, Zone $zone)
    {
        return $this->zoneRepository->update($zone, $request->all(), $request);
    }

    public function toggleStatus(Zone $zone)
    {
        return $this->zoneRepository->toggleStatus($zone);
    }

    public function destroy(Zone $zone)
    {
        return $this->zoneRepository->destroy($zone);
    }

    public function restore($id)
    {
        return $this->zoneRepository->restore($id);
    }

    public function forceDelete($id)
    {
        return $this->zoneRepository->forceDelete($id);
    }

    public function bulkAction(Request $request)
    {
        return $this->zoneRepository->bulkAction($request);
    }

    public function hasTrashed()
    {
        return $this->zoneRepository->hasTrashed();
    }
}