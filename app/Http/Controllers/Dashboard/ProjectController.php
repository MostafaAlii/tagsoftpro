<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\DataTables\Dashboard\Admin\ProjectDataTable;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Models\Project;
use App\Http\Requests\Dashboard\Project\StoreProjectRequest;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct(
        protected ProjectDataTable $projectDataTable,
        protected ProjectRepositoryInterface $projectInterface
    ) {}

    public function index()
    {
        return $this->projectInterface->index($this->projectDataTable);
    }

    public function store(StoreProjectRequest $request)
    {
        return $this->projectInterface->store($request);
    }

    public function edit(Project $project)
    {
        return $this->projectInterface->edit($project);
    }

    public function update(Request $request, Project $project)
    {
        return $this->projectInterface->update($project, $request->all(), $request);
    }

    public function toggleStatus(Project $project)
    {
        return $this->projectInterface->toggleStatus($project);
    }

    public function destroy(Project $project)
    {
        return $this->projectInterface->destroy($project);
    }
}