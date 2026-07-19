<?php

namespace Modules\Zone\Repositories\Eloquents;

use Modules\Zone\DataTables\ZoneDataTable;
use Modules\Zone\Repositories\Contracts\ZoneRepositoryInterface;
use Modules\Zone\Entities\Zone;
use Modules\Zone\Http\Requests\StoreZoneRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\{Request, JsonResponse};

class ZoneRepository implements ZoneRepositoryInterface
{
    public function index(ZoneDataTable $zoneDataTable)
    {
        $locales = array_keys(config('laravellocalization.supportedLocales'));

        return $zoneDataTable->render('zone::index', [
            'title' => trans('zone::zones.zones'),
            'locales' => $locales,
        ]);
    }

    public function store(StoreZoneRequest $request)
    {
        try {
            DB::beginTransaction();

            $zone = Zone::create([
                'key' => $request->key,
                'status' => $request->status,
            ]);

            if ($request->has('locales') && is_array($request->locales)) {
                foreach ($request->locales as $locale => $data) {
                    if (isset($data['name']) && !empty($data['name'])) {
                        $zone->translations()->create([
                            'locale' => $locale,
                            'name' => $data['name'],
                            'description' => $data['description'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.zones.index')
                ->with('success', trans('zone::zones.created_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('admin.zones.index')
                ->with('error', trans('zone::zones.error_occurred') . ': ' . $e->getMessage());
        }
    }

    public function edit(Zone $zone)
    {
        $zone->load('translations');

        return response()->json([
            'success' => true,
            'data' => $zone,
        ]);
    }

    public function update(Zone $zone, array $data, ?Request $request = null)
    {
        try {
            DB::beginTransaction();

            $updateData = [
                'key' => $data['key'] ?? $zone->key,
                'status' => $data['status'] ?? $zone->status,
            ];

            $zone->update($updateData);

            if (isset($data['locales']) && is_array($data['locales'])) {
                foreach ($data['locales'] as $locale => $translationData) {
                    if (isset($translationData['name']) && !empty($translationData['name'])) {
                        $zone->translations()->updateOrCreate(
                            ['locale' => $locale],
                            [
                                'name' => $translationData['name'],
                                'description' => $translationData['description'] ?? null,
                            ]
                        );
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('zone::zones.updated_successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => trans('zone::zones.error_occurred') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    public function toggleStatus(Zone $zone)
    {
        try {
            $newStatus = $zone->status === 'active' ? 'inactive' : 'active';
            $zone->updateQuietly(['status' => $newStatus]);
            $zone->refresh();

            return response()->json([
                'success' => true,
                'badge' => $zone->status->badge(),
                'message' => trans('zone::zones.status_updated'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('zone::zones.error_occurred'),
            ], 500);
        }
    }

    public function destroy(Zone $zone)
    {
        try {
            $zone->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('zone::zones.deleted_successfully'),
                ]);
            }

            return redirect()->route('admin.zones.index')
                ->with('success', trans('zone::zones.deleted_successfully'));
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('zone::zones.error_occurred'),
                ], 500);
            }

            return redirect()->route('admin.zones.index')
                ->with('error', trans('zone::zones.error_occurred'));
        }
    }

    public function restore($id)
    {
        try {
            $zone = Zone::withTrashed()->findOrFail($id);
            $zone->restore();

            return response()->json([
                'success' => true,
                'message' => trans('zone::zones.restored_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('zone::zones.error_occurred'),
            ], 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $zone = Zone::withTrashed()->findOrFail($id);
            $zone->forceDelete();

            return response()->json([
                'success' => true,
                'message' => trans('zone::zones.permanently_deleted'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('zone::zones.error_occurred'),
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
                    'message' => trans('zone::zones.bulk_select_at_least_one'),
                ]);
            }

            $action = $request->action;
            $message = '';

            switch ($action) {
                case 'delete':
                    Zone::whereIn('id', $ids)->delete();
                    $message = trans('zone::zones.bulk_deleted_successfully');
                    break;
                case 'restore':
                    Zone::withTrashed()->whereIn('id', $ids)->restore();
                    $message = trans('zone::zones.bulk_restored_successfully');
                    break;
                case 'force_delete':
                    Zone::withTrashed()->whereIn('id', $ids)->forceDelete();
                    $message = trans('zone::zones.bulk_force_deleted_successfully');
                    break;
                case 'status':
                    $status = $request->status;
                    Zone::whereIn('id', $ids)->update(['status' => $status]);
                    $message = trans('zone::zones.bulk_status_updated_successfully');
                    break;
                default:
                    throw new \InvalidArgumentException(trans('zone::zones.invalid_action'));
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
                'message' => trans('zone::zones.error_occurred') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    public function hasTrashed()
    {
        return response()->json([
            'hasTrashed' => Zone::onlyTrashed()->exists(),
        ]);
    }
}