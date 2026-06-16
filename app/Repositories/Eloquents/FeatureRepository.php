<?php
namespace App\Repositories\Eloquents;

use App\DataTables\Dashboard\Admin\FeatureDataTable;
use App\Repositories\Contracts\FeatureRepositoryInterface;
use App\Models\Feature;
use App\Http\Requests\Dashboard\Feature\StoreFeatureRequest;
use App\Enums\Feature\{FeatureStatus,FeatureScope};
use Illuminate\Support\Facades\DB;

class FeatureRepository implements FeatureRepositoryInterface {
    public function index(FeatureDataTable $featureDataTable)
    {
        return $featureDataTable->render('dashboard.admin.features.index', [
            'title' => trans('dashboard/features.features'),
        ]);
    }

    public function store(StoreFeatureRequest $request)
    {
        try {
            $feature = Feature::create([
                'key'    => $request->input('key'),
                'type'   => $request->input('type'),
                'scope'  => $request->input('scope'),
                'status' => $request->boolean('status'),
            ]);

            foreach ($request->name as $locale => $value) {
                if (filled($value)) {
                    $feature->translateOrNew($locale)->name = $value;
                }
            }

            $feature->save();

            return redirect()
                ->route('admin.features.index')
                ->with('success', trans('dashboard/features.created_successfully'));
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.features.index')
                ->with('error', trans('dashboard/general.error_occurred'));
        }
    }

    public function update(Feature $feature, array $data)
    {
        try {
            DB::beginTransaction();

            $feature->update([
                'key'    => $data['key']    ?? $feature->key,
                'type'   => $data['type']   ?? $feature->type,
                'scope'  => $data['scope']  ?? $feature->scope,
                'status' => $data['status'] ?? $feature->status,
            ]);

            if (isset($data['name'])) {
                foreach ($data['name'] as $locale => $value) {
                    if (filled($value)) {
                        $feature->translateOrNew($locale)->name = $value;
                    }
                }
                $feature->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('dashboard/features.updated_successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function toggleStatus(Feature $feature)
    {
        try {
            $feature->update([
                'status' => $feature->status === FeatureStatus::ACTIVE
                    ? FeatureStatus::INACTIVE
                    : FeatureStatus::ACTIVE,
            ]);

            return response()->json([
                'success' => true,
                'badge'   => $feature->status->badge(),
                'message' => trans('dashboard/features.status_updated'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }


    public function toggleScope(Feature $feature) {
        try {
            $feature->update([
                'scope' => $feature->scope === FeatureScope::MAIN
                    ? FeatureScope::ADDON
                    : FeatureScope::MAIN,
            ]);

            return response()->json([
                'success' => true,
                'badge' => $feature->scope->badge(),
                'message' => trans('dashboard/features.updated_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function destroy(Feature $feature)
    {
        try {
            $feature->delete();

            return response()->json([
                'success' => true,
                'message' => trans('dashboard/features.deleted_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }
}