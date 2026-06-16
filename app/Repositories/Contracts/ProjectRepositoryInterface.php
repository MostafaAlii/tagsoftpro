<?php

namespace App\Repositories\Contracts;

use App\DataTables\Dashboard\Admin\ProjectDataTable;
use App\Models\Project;
use App\Http\Requests\Dashboard\Project\StoreProjectRequest;
use Illuminate\Http\Request;
interface ProjectRepositoryInterface
{
    public function index(ProjectDataTable $projectDataTable);
    public function store(StoreProjectRequest $request);
    public function update(Project $project, array $data ,?Request $request = null);
    public function edit(Project $project);
    public function toggleStatus(Project $project);
    public function destroy(Project $project);
}