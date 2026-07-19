<?php

namespace Modules\Provider\Repositories\Eloquents;

use Modules\Provider\DataTables\ProviderDataTable;
use Modules\Provider\Repositories\Contracts\ProviderRepositoryInterface;
use Modules\Provider\Entities\Provider;
use Modules\Provider\Http\Requests\StoreProviderRequest;
use Modules\Zone\Entities\Zone;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\{Request, JsonResponse};

class ProviderRepository implements ProviderRepositoryInterface
{
    public function index(ProviderDataTable $providerDataTable)
    {
        $zones = Zone::whereStatus('active')->get();

        return $providerDataTable->render('provider::index', [
            'title' => trans('provider::providers.providers'),
            'zones' => $zones,
        ]);
    }

    public function store(StoreProviderRequest $request)
    {
        try {
            DB::beginTransaction();

            $provider = Provider::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => $request->status,
                'password' => bcrypt($request->password),
                'date' => $request->date,
                'address' => $request->address,
                'website' => $request->website,
                'zone_id' => $request->zone_id,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('admin.providers.index')
                ->with('success', trans('provider::providers.created_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('admin.providers.index')
                ->with('error', trans('provider::providers.error_occurred') . ': ' . $e->getMessage());
        }
    }

    public function edit(Provider $provider)
    {
        return response()->json([
            'success' => true,
            'data' => $provider,
        ]);
    }

    public function update(Provider $provider, array $data, ?Request $request = null)
    {
        try {
            DB::beginTransaction();

            $updateData = [
                'name' => $data['name'] ?? $provider->name,
                'email' => $data['email'] ?? $provider->email,
                'phone' => $data['phone'] ?? $provider->phone,
                'status' => $data['status'] ?? $provider->status,
                'date' => $data['date'] ?? $provider->date,
                'address' => $data['address'] ?? $provider->address,
                'website' => $data['website'] ?? $provider->website,
                'zone_id' => $data['zone_id'] ?? $provider->zone_id,
                'updated_by' => auth()->id(),
            ];

            if (!empty($data['password'])) {
                $updateData['password'] = bcrypt($data['password']);
            }

            $provider->update($updateData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('provider::providers.updated_successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => trans('provider::providers.error_occurred') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    public function toggleStatus(Provider $provider)
    {
        try {
            $statuses = ['active', 'inactive', 'pending', 'suspended'];
            $currentIndex = array_search($provider->status, $statuses);
            $newIndex = ($currentIndex + 1) % count($statuses);
            $newStatus = $statuses[$newIndex];

            $provider->updateQuietly(['status' => $newStatus]);
            $provider->refresh();

            return response()->json([
                'success' => true,
                'badge' => $provider->status_badge,
                'message' => trans('provider::providers.status_updated'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('provider::providers.error_occurred'),
            ], 500);
        }
    }

    public function destroy(Provider $provider)
    {
        try {
            $provider->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('provider::providers.deleted_successfully'),
                ]);
            }

            return redirect()->route('admin.providers.index')
                ->with('success', trans('provider::providers.deleted_successfully'));
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('provider::providers.error_occurred'),
                ], 500);
            }

            return redirect()->route('admin.providers.index')
                ->with('error', trans('provider::providers.error_occurred'));
        }
    }

    public function restore($id)
    {
        try {
            $provider = Provider::withTrashed()->findOrFail($id);
            $provider->restore();

            return response()->json([
                'success' => true,
                'message' => trans('provider::providers.restored_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('provider::providers.error_occurred'),
            ], 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $provider = Provider::withTrashed()->findOrFail($id);
            $provider->forceDelete();

            return response()->json([
                'success' => true,
                'message' => trans('provider::providers.permanently_deleted'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('provider::providers.error_occurred'),
            ], 500);
        }
    }

    public function bulkAction(Request $request): JsonResponse
    {
        try {
            $ids = $request->ids;

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => trans('provider::providers.bulk_select_at_least_one'),
                ]);
            }

            $action = $request->action;
            $message = '';

            switch ($action) {
                case 'delete':
                    Provider::whereIn('id', $ids)->delete();
                    $message = trans('provider::providers.bulk_deleted_successfully');
                    break;
                case 'restore':
                    Provider::withTrashed()->whereIn('id', $ids)->restore();
                    $message = trans('provider::providers.bulk_restored_successfully');
                    break;
                case 'force_delete':
                    Provider::withTrashed()->whereIn('id', $ids)->forceDelete();
                    $message = trans('provider::providers.bulk_force_deleted_successfully');
                    break;
                case 'status':
                    $status = $request->status;
                    Provider::whereIn('id', $ids)->update(['status' => $status]);
                    $message = trans('provider::providers.bulk_status_updated_successfully');
                    break;
                default:
                    throw new \InvalidArgumentException(trans('provider::providers.invalid_action'));
            }

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('provider::providers.error_occurred') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    public function hasTrashed()
    {
        return response()->json([
            'hasTrashed' => Provider::onlyTrashed()->exists(),
        ]);
    }
}