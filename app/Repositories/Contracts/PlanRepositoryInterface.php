<?php
namespace App\Repositories\Contracts;
use App\DataTables\Dashboard\Admin\PlanDataTable;
use App\Models\Plan;
use App\Http\Requests\Dashboard\Plan\StorePlanRequest;
interface PlanRepositoryInterface {
    public function index(PlanDataTable $planDataTable);
    public function store(StorePlanRequest $request);
    public function update(Plan $plan, array $data);
    public function toggleStatus(Plan $plan);
    public function toggleBillingCycle(Plan $plan);
    public function destroy(Plan $plan);
    public function getFeatures(Plan $plan);
    public function updateFeatures(Plan $plan, array $data);
}