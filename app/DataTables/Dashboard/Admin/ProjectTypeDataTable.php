<?php

namespace App\DataTables\Dashboard\Admin;

use App\DataTables\Base\BaseDataTable;
use App\Models\ProjectType;
use App\Enums\ProjectType\ProjectTypeStatus;
use App\Http\Middleware\EnsureOwner;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Utilities\Request as DataTableRequest;
use Illuminate\Support\Str;

class ProjectTypeDataTable extends BaseDataTable
{
    protected $customFilters = [];

    public function __construct(DataTableRequest $request)
    {
        parent::__construct(new ProjectType);
        $this->request = $request;
    }

    public function dataTable($query): EloquentDataTable
    {
        $dataTable = (new EloquentDataTable($query))
            ->addColumn('action', function (ProjectType $projectType) {
                return view('dashboard.admin.projectTypes.btn.actions', compact('projectType'));
            })
            ->editColumn('name', function (ProjectType $projectType) {
                return $projectType->translate(app()->getLocale())?->name
                    ?? $projectType->translate('ar')?->name
                    ?? '-';
            })
            ->editColumn('description', function (ProjectType $projectType) {
                $desc = $projectType->translate(app()->getLocale())?->description
                    ?? $projectType->translate('ar')?->description;
                return $desc ? Str::limit($desc, 50) : '-';
            })
            ->editColumn('status', function (ProjectType $projectType) {
                return $this->renderStatusToggle($projectType);
            })

            // ─── Filters ──────────────────────────────────────
            ->filterColumn('name', function ($query, $keyword) {
                if ($keyword) {
                    $query->whereHas('translations', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                }
            })
            ->filterColumn('status', function ($query, $keyword) {
                if ($keyword !== '') {
                    $query->where('status', (int) $keyword);
                }
            });

        // ─── Company column for owners ──────────────────────
        if (EnsureOwner::check()) {
            $dataTable->editColumn('company', function (ProjectType $projectType) {
                return $projectType->company?->name ?? '-';
            });
        }

        $dataTable
            ->editColumn('created_at', fn(ProjectType $projectType) => $this->formatTranslatedDate($projectType->created_at))
            ->editColumn('updated_at', fn(ProjectType $projectType) => $this->formatTranslatedDate($projectType->updated_at))
            ->addIndexColumn()
            ->rawColumns(['action', 'status', 'created_at', 'updated_at']);

        return $dataTable;
    }

    /**
     * Render status toggle button
     */
    private function renderStatusToggle(ProjectType $projectType): string
    {
        return '
            <div class="gap-1 d-flex flex-column align-items-center">
                <span class="badge-status">' . $projectType->status->badge() . '</span>
                <div class="form-check form-switch">
                    <input type="checkbox"
                        class="form-check-input toggle-status"
                        data-id="' . $projectType->id . '"
                        data-route="' . route('admin.projectTypes.toggleStatus', $projectType->id) . '"
                        ' . ($projectType->status === ProjectTypeStatus::ACTIVE ? 'checked' : '') . '>
                </div>
            </div>
        ';
    }

    public function query(): QueryBuilder
    {
        $query = ProjectType::query()->with(['translations'])->latest();

        if (EnsureOwner::check()) {
            $query->with(['company']);
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId($this->model->getTable() . '_datatable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters(array_merge($this->getParameters(), [
                'initComplete' => $this->getInitCompleteScript(),
            ]));
    }

    /**
     * Generate init complete script for filters
     */
    private function getInitCompleteScript(): string
    {
        $allText = trans('dashboard/general.all');
        $activeText = trans('dashboard/general.active');
        $inactiveText = trans('dashboard/general.in_active');

        return '
        function() {
            var api = this.api();

            // ─── Search box بالاسم (فلتر العمود name) ───────────────
            var nameColIndex = 1;
            var nameHeader = $(api.column(nameColIndex).header());
            var nameInput = $(\'<input type="text" class="mt-1 form-control form-control-sm" placeholder="' . trans('dashboard/project_types.name') . '...">\');
            nameHeader.append(nameInput);

            nameInput.on("keyup change", function(e) {
                e.stopPropagation();
                api.column(nameColIndex).search(this.value).draw();
            });

            nameInput.on("click", function(e) {
                e.stopPropagation();
            });

            // ─── فلتر الحالة (status) ──────────────────────────────────────
            var statusColIndex = 3;
            var statusHeader = $(api.column(statusColIndex).header());
            var statusSelect = $(\'<select class="mt-1 form-select form-select-sm">\' +
                \'<option value="">' . $allText . '</option>\' +
                \'<option value="1">' . $activeText . '</option>\' +
                \'<option value="0">' . $inactiveText . '</option>\' +
                \'</select>\');
            statusHeader.append(statusSelect);

            statusSelect.on("change", function(e) {
                e.stopPropagation();
                api.column(statusColIndex).search(this.value).draw();
            });

            statusSelect.on("click", function(e) {
                e.stopPropagation();
            });
        }';
    }

    public function getColumns(): array
    {
        $columns = [
            ['name' => 'DT_RowIndex', 'data' => 'DT_RowIndex', 'title' => '#', 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'name', 'data' => 'name', 'title' => trans('dashboard/project_types.name'), 'className' => 'text-center', 'searchable' => true, 'orderable' => true],
            ['name' => 'description', 'data' => 'description', 'title' => trans('dashboard/project_types.description'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'status', 'data' => 'status', 'title' => trans('dashboard/project_types.status'), 'className' => 'text-center', 'orderable' => false, 'searchable' => true],
        ];

        if (EnsureOwner::check()) {
            $columns[] = ['name' => 'company', 'data' => 'company', 'title' => trans('dashboard/project_types.company'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false];
        }

        $columns[] = ['name' => 'created_at', 'data' => 'created_at', 'title' => trans('dashboard/general.created_at'), 'className' => 'text-center', 'searchable' => false];
        $columns[] = ['name' => 'updated_at', 'data' => 'updated_at', 'title' => trans('dashboard/general.updated_at'), 'className' => 'text-center', 'searchable' => false];
        $columns[] = ['name' => 'action', 'data' => 'action', 'title' => trans('dashboard/general.actions'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false];

        return $columns;
    }
}