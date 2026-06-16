<?php

namespace App\Repositories\Contracts;

use App\DataTables\Dashboard\Admin\ProjectTypeDataTable;
use App\Models\ProjectType;
use App\Http\Requests\Dashboard\ProjectType\StoreProjectTypeRequest;

interface ProjectTypeRepositoryInterface
{
    public function index(ProjectTypeDataTable $projectTypeDataTable);
    public function store(StoreProjectTypeRequest $request);
    public function update(ProjectType $projectType, array $data);
    public function toggleStatus(ProjectType $projectType);
    public function destroy(ProjectType $projectType);
}