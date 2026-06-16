<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\DataTables\Dashboard\Admin\SalesMatrialTypeDataTable;
use App\Repositories\Contracts\SalesMatrialTypeRepositoryInterface;
use App\Models\{SalesMatrialType};
use App\Http\Requests\Dashboard\SalesMatrialType\{StoreSalesMatrialTypeRequest};
use Illuminate\Http\Request;

class SalesMatrialTypeController extends Controller
{
    public function __construct(
        protected SalesMatrialTypeDataTable $salesMatrialTypeDataTable,
        protected SalesMatrialTypeRepositoryInterface $salesMatrialTypeInterface
    ) {}

    public function index() {
        return $this->salesMatrialTypeInterface->index($this->salesMatrialTypeDataTable);
    }

    public function store(StoreSalesMatrialTypeRequest $request) {
        return $this->salesMatrialTypeInterface->store($request);
    }

    public function edit(SalesMatrialType $salesMatrialType)
    {
        $salesMatrialType->load('translations');
        return response()->json([
            'success' => true,
            'data' => $salesMatrialType
        ]);
    }

    public function update(Request $request, SalesMatrialType $salesMatrialType) {
        return $this->salesMatrialTypeInterface->update($salesMatrialType, $request->all());
    }

    public function toggleStatus(SalesMatrialType $salesMatrialType) {
        return $this->salesMatrialTypeInterface->toggleStatus($salesMatrialType);
    }

    public function destroy(SalesMatrialType $salesMatrialType) {
        return $this->salesMatrialTypeInterface->destroy($salesMatrialType);
    }
}