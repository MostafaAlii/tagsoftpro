<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\DataTables\Dashboard\Admin\PlanDataTable;
use App\Repositories\Contracts\PlanRepositoryInterface;
use App\Models\Plan;
use App\Http\Requests\Dashboard\Plan\StorePlanRequest;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function __construct(
        protected PlanDataTable $planDataTable,
        protected PlanRepositoryInterface $planInterface
    ) {}

    public function index()
    {
        return $this->planInterface->index($this->planDataTable);
    }

    public function store(StorePlanRequest $request)
    {
        return $this->planInterface->store($request);
    }

    public function edit(Plan $plan)
    {
        $plan->load('translations');
        return response()->json([
            'success' => true,
            'data'    => $plan,
        ]);
    }

    public function update(Request $request, Plan $plan)
    {
        return $this->planInterface->update($plan, $request->all());
    }

    public function toggleStatus(Plan $plan)
    {
        return $this->planInterface->toggleStatus($plan);
    }

    public function toggleBillingCycle(Plan $plan)
    {
        return $this->planInterface->toggleBillingCycle($plan);
    }

    public function destroy(Plan $plan)
    {
        return $this->planInterface->destroy($plan);
    }

    public function getFeatures(Plan $plan)
    {
        return $this->planInterface->getFeatures($plan);
    }

    public function updateFeatures(Request $request, Plan $plan)
    {
        return $this->planInterface->updateFeatures($plan, $request->all());
    }
}