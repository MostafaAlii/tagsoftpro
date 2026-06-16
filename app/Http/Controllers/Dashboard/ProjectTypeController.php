<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\DataTables\Dashboard\Admin\ProjectTypeDataTable;
use App\Repositories\Contracts\ProjectTypeRepositoryInterface;
use App\Models\ProjectType;
use App\Http\Requests\Dashboard\ProjectType\StoreProjectTypeRequest;
use Illuminate\Http\Request;

class ProjectTypeController extends Controller
{
    public function __construct(
        protected ProjectTypeDataTable $projectTypeDataTable,
        protected ProjectTypeRepositoryInterface $projectTypeInterface
    ) {}

    public function index()
    {
        return $this->projectTypeInterface->index($this->projectTypeDataTable);
    }

    public function store(StoreProjectTypeRequest $request)
    {
        return $this->projectTypeInterface->store($request);
    }

    public function edit(ProjectType $projectType)
    {
        $projectType->load('translations');
        return response()->json([
            'success' => true,
            'data' => $projectType,
        ]);
    }

    public function update(Request $request, ProjectType $projectType)
    {
        return $this->projectTypeInterface->update($projectType, $request->all());
    }

    public function toggleStatus(ProjectType $projectType)
    {
        return $this->projectTypeInterface->toggleStatus($projectType);
    }

    public function destroy(ProjectType $projectType)
    {
        return $this->projectTypeInterface->destroy($projectType);
    }
}