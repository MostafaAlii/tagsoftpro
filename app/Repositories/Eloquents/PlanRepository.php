<?php

namespace App\Repositories\Eloquents;

use App\DataTables\Dashboard\Admin\PlanDataTable;
use App\Repositories\Contracts\PlanRepositoryInterface;
use App\Models\{Plan, Company, Feature};
use App\Http\Requests\Dashboard\Plan\StorePlanRequest;
use App\Enums\Plan\{PlanStatus, PlanBillingCycle};
use Illuminate\Support\Facades\DB;
use App\Enums\Feature\{FeatureScope, FeatureType};

class PlanRepository implements PlanRepositoryInterface
{
    public function index(PlanDataTable $planDataTable)
    {
        $companies = Company::whereStatus('active')->get(['id', 'name']);

        return $planDataTable->render('dashboard.admin.plans.index', [
            'title'     => trans('dashboard/plans.plans'),
            'companies' => $companies,
        ]);
    }

    public function store(StorePlanRequest $request)
    {
        try {
            DB::beginTransaction();

            $plan = Plan::create([
                'price'         => $request->price,
                'billing_cycle' => $request->billing_cycle,
                'status'        => $request->boolean('status'),
                'company_id'    => $request->company_id,
                'created_by'    => auth()->id(),
            ]);

            // Translations
            foreach ($request->name as $locale => $name) {
                if (filled($name)) {
                    $plan->translateOrNew($locale)->name = $name;
                    $plan->translateOrNew($locale)->description = $request->description[$locale] ?? null;
                }
            }
            $plan->save();

            DB::commit();

            return redirect()->route('admin.plans.index')
                ->with('success', trans('dashboard/plans.created_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.plans.index')
                ->with('error', trans('dashboard/general.error_occurred'));
        }
    }

    public function update(Plan $plan, array $data) {
        try {
            DB::beginTransaction();

            $plan->update([
                'price'         => $data['price'] ?? $plan->price,
                'billing_cycle' => $data['billing_cycle'] ?? $plan->billing_cycle,
                'status'        => $data['status'] ?? $plan->status,
                'company_id'    => $data['company_id'] ?? $plan->company_id,
                'updated_by'    => auth()->id(),
            ]);

            // Translations
            if (isset($data['name'])) {
                foreach ($data['name'] as $locale => $name) {
                    if (filled($name)) {
                        $plan->translateOrNew($locale)->name = $name;
                        $plan->translateOrNew($locale)->description = $data['description'][$locale] ?? null;
                    }
                }
            }
            $plan->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('dashboard/plans.updated_successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function toggleStatus(Plan $plan)
    {
        try {
            $plan->update([
                'status' => $plan->status === PlanStatus::ACTIVE
                    ? PlanStatus::INACTIVE
                    : PlanStatus::ACTIVE,
            ]);

            return response()->json([
                'success' => true,
                'badge'   => $plan->status->badge(),
                'message' => trans('dashboard/plans.status_updated'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function toggleBillingCycle(Plan $plan)
    {
        try {
            $plan->update([
                'billing_cycle' => $plan->billing_cycle === PlanBillingCycle::MONTHLY
                    ? PlanBillingCycle::YEARLY
                    : PlanBillingCycle::MONTHLY,
            ]);

            return response()->json([
                'success' => true,
                'badge'   => $plan->billing_cycle->badge(),
                'message' => trans('dashboard/plans.billing_cycle_updated'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function destroy(Plan $plan)
    {
        try {
            $plan->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('dashboard/plans.deleted_successfully'),
                ]);
            }

            return redirect()->route('admin.plans.index')
                ->with('success', trans('dashboard/plans.deleted_successfully'));
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('dashboard/general.error_occurred'),
                ], 500);
            }

            return redirect()->route('admin.plans.index')
                ->with('error', trans('dashboard/general.error_occurred'));
        }
    }


    /**
     * Get features for a plan
     */
    public function getFeatures(Plan $plan) {
        try {
            // Get all features
            $allFeatures = Feature::active()->with('translations')->get();
            // Get current plan features with pivot data
            $planFeatures = $plan->features()->get()->keyBy('id');
            $features = $allFeatures->map(function ($feature) use ($planFeatures) {
                $pivot = $planFeatures->get($feature->id)?->pivot;
                
                // ✅ بما أن الـ type و scope هم Enums بالفعل، استخدمهم مباشرة
                return [
                    'id' => $feature->id,
                    'name' => $feature->translate(app()->getLocale())?->name 
                        ?? $feature->translate('ar')?->name 
                        ?? $feature->name,
                    'type' => [
                        'value' => $feature->type->value,      // <-- استخدم ->value
                        'label' => $feature->type->label(),    // <-- استخدم الـ method
                        'badge' => $feature->type->badge(),    // <-- استخدم الـ method
                    ],
                    'scope' => [
                        'value' => $feature->scope->value,     // <-- استخدم ->value
                        'label' => $feature->scope->label(),   // <-- استخدم الـ method
                        'badge' => $feature->scope->badge(),   // <-- استخدم الـ method
                    ],
                    'is_included' => $pivot ? (bool) $pivot->is_included : false,
                    'limit' => $pivot ? $pivot->limit : null,
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => [
                    'plan_id' => $plan->id,
                    'features' => $features,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update features for a plan
     */
    public function updateFeatures(Plan $plan, array $data)
    {
        try {
            DB::beginTransaction();
            
            $features = $data['features'] ?? [];
            $syncData = [];
            
            foreach ($features as $featureId => $featureData) {
                $syncData[$featureId] = [
                    'is_included' => isset($featureData['is_included']) && $featureData['is_included'],
                    'limit' => $featureData['limit'] ?? null,
                ];
            }
            
            $plan->features()->sync($syncData);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => trans('dashboard/plans.features_updated_successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }
}