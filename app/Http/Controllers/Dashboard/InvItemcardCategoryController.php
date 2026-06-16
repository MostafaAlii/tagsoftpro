<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\DataTables\Dashboard\Admin\InvItemcardCategoryDataTable;
use App\Repositories\Contracts\InvItemcardCategoryRepositoryInterface;
use App\Models\InvItemcardCategory;
use App\Http\Requests\Dashboard\InvItemcardCategory\StoreInvItemcardCategoryRequest;
use Illuminate\Http\Request;

class InvItemcardCategoryController extends Controller
{
    public function __construct(
        protected InvItemcardCategoryDataTable $dataTable,
        protected InvItemcardCategoryRepositoryInterface $repository
    ) {}

    public function index()
    {
        return $this->repository->index($this->dataTable);
    }

    public function store(StoreInvItemcardCategoryRequest $request)
    {
        return $this->repository->store($request);
    }

    public function edit(InvItemcardCategory $invItemcardCategory)
    {
        $invItemcardCategory->load('translations');
        return response()->json([
            'success' => true,
            'data' => $invItemcardCategory
        ]);
    }

    public function update(Request $request, InvItemcardCategory $invItemcardCategory)
    {
        return $this->repository->update($invItemcardCategory, $request->all());
    }

    public function toggleStatus(InvItemcardCategory $invItemcardCategory)
    {
        return $this->repository->toggleStatus($invItemcardCategory);
    }

    public function destroy(InvItemcardCategory $invItemcardCategory)
    {
        return $this->repository->destroy($invItemcardCategory);
    }
}