<?php
namespace App\DataTables\Dashboard\Admin;

use App\DataTables\Base\BaseDataTable;
use App\Models\Feature;
use App\Enums\Feature\FeatureStatus;
use App\Http\Middleware\EnsureOwner;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Utilities\Request as DataTableRequest;

class FeatureDataTable extends BaseDataTable {
    public function __construct(DataTableRequest $request)
    {
        parent::__construct(new Feature);
        $this->request = $request;
    }

    public function dataTable($query): EloquentDataTable
    {
        $dataTable = (new EloquentDataTable($query))
            ->addColumn('action', function (Feature $feature) {
                return view('dashboard.admin.features.btn.actions', compact('feature'));
            })
            ->editColumn('name', function (Feature $feature) {
                return $feature->translate(app()->getLocale())?->name
                    ?? $feature->translate('ar')?->name
                    ?? '-';
            })
            ->editColumn('type', function (Feature $feature) {
                return $feature->type->badge();
            })
            ->editColumn('scope', function (Feature $feature) {
                return '
                    <div class="d-flex flex-column align-items-center gap-1">
                        <span class="badge-status">' . $feature->scope->badge() . '</span>

                        <div class="form-check form-switch">
                            <input type="checkbox"
                                class="form-check-input toggle-scope"
                                data-route="' . route('admin.features.toggleScope', $feature->id) . '"
                                ' . ($feature->scope === \App\Enums\Feature\FeatureScope::MAIN ? 'checked' : '') . '>
                        </div>
                    </div>
                ';
            })
            ->editColumn('status', function (Feature $feature) {
                return '
                    <div class="gap-1 d-flex flex-column align-items-center">
                        <span class="badge-status">' . $feature->status->badge() . '</span>
                        <div class="form-check form-switch">
                            <input type="checkbox"
                                class="form-check-input toggle-status"
                                data-route="' . route('admin.features.toggleStatus', $feature->id) . '"
                                ' . ($feature->status === FeatureStatus::ACTIVE ? 'checked' : '') . '>
                        </div>
                    </div>
                ';
            });

        if (EnsureOwner::check()) {
            $dataTable->editColumn('company', function (Feature $feature) {
                return $feature->company?->name ?? '-';
            });
        }

        $dataTable
            ->editColumn('created_at', fn(Feature $f) => $this->formatTranslatedDate($f->created_at))
            ->editColumn('updated_at', fn(Feature $f) => $this->formatTranslatedDate($f->updated_at))
            ->addIndexColumn()
            ->rawColumns(['action', 'status', 'type', 'scope', 'created_at', 'updated_at']);

        return $dataTable;
    }

    public function query(): QueryBuilder
    {
        $query = Feature::query()
            ->with(['translations'])
            ->latest();

        if (EnsureOwner::check()) {
            $query->with(['company']);
        }

        return $query;
    }

    public function getColumns(): array
    {
        $columns = [
            ['name' => 'DT_RowIndex', 'data' => 'DT_RowIndex', 'title' => '#',                                              'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'name',        'data' => 'name',        'title' => trans('dashboard/features.name'),        'className' => 'text-center', 'searchable' => false],
            ['name' => 'type',        'data' => 'type',        'title' => trans('dashboard/features.type'),        'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'scope',       'data' => 'scope',       'title' => trans('dashboard/features.scope'),       'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'status',      'data' => 'status',      'title' => trans('dashboard/features.status'),      'className' => 'text-center', 'orderable' => false, 'searchable' => false],
        ];

        if (EnsureOwner::check()) {
            $columns[] = ['name' => 'company', 'data' => 'company', 'title' => trans('dashboard/features.company'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false];
        }

        $columns[] = ['name' => 'created_at', 'data' => 'created_at', 'title' => trans('dashboard/general.created_at'), 'className' => 'text-center'];
        $columns[] = ['name' => 'updated_at', 'data' => 'updated_at', 'title' => trans('dashboard/general.updated_at'), 'className' => 'text-center'];
        $columns[] = ['name' => 'action',     'data' => 'action',     'title' => trans('dashboard/general.actions'),    'className' => 'text-center', 'orderable' => false, 'searchable' => false];

        return $columns;
    }
}