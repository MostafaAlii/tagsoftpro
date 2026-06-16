<?php

namespace App\DataTables\Dashboard\Admin;

use App\DataTables\Base\BaseDataTable;
use App\Models\Department;
use App\Enums\Department\DepartmentStatus;
use App\Http\Middleware\EnsureOwner;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Utilities\Request as DataTableRequest;
use Illuminate\Support\Str;

class DepartmentDataTable extends BaseDataTable
{
    protected $customFilters = [];

    public function __construct(DataTableRequest $request)
    {
        parent::__construct(new Department);
        $this->request = $request;
    }

    public function dataTable($query): EloquentDataTable
    {
        $dataTable = (new EloquentDataTable($query))
            ->addColumn('action', function (Department $department) {
                return view('dashboard.admin.departments.btn.actions', compact('department'));
            })
            ->editColumn('name', function (Department $department) {
                return $department->getTranslatedName();
            })
            ->editColumn('description', function (Department $department) {
                $desc = $department->getTranslatedDescription();
                return $desc ? Str::limit($desc, 50) : '-';
            })
            ->editColumn('status', function (Department $department) {
                return $this->renderStatusToggle($department);
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
            $dataTable->editColumn('company', function (Department $department) {
                return $department->company?->name ?? '-';
            });
        }

        $dataTable
            ->editColumn('created_at', fn(Department $department) => $this->formatTranslatedDate($department->created_at))
            ->editColumn('updated_at', fn(Department $department) => $this->formatTranslatedDate($department->updated_at))
            ->addIndexColumn()
            ->rawColumns(['action', 'status', 'created_at', 'updated_at']);

        return $dataTable;
    }

    /**
     * Render status toggle button
     */
    private function renderStatusToggle(Department $department): string
    {
        return '
            <div class="gap-1 d-flex flex-column align-items-center">
                <span class="badge-status">' . $department->status->badge() . '</span>
                <div class="form-check form-switch">
                    <input type="checkbox"
                        class="form-check-input toggle-status"
                        data-id="' . $department->id . '"
                        data-route="' . route('admin.departments.toggleStatus', $department->id) . '"
                        ' . ($department->status === DepartmentStatus::ACTIVE ? 'checked' : '') . '>
                </div>
            </div>
        ';
    }

    public function query(): QueryBuilder
    {
        $query = Department::query()
            ->with(['translations'])
            ->latest();

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

    private function getInitCompleteScript(): string
    {
        $allText = trans('dashboard/general.all');
        $activeText = trans('dashboard/general.active');
        $inactiveText = trans('dashboard/general.in_active');

        return '
        function() {
            var api = this.api();

            // ─── Search box بالاسم ──────────────────────────
            var nameColIndex = 1;
            var nameHeader = $(api.column(nameColIndex).header());
            var nameInput = $(\'<input type="text" class="mt-1 form-control form-control-sm" placeholder="' . trans('dashboard/departments.name') . '...">\');
            nameHeader.append(nameInput);

            nameInput.on("keyup change", function(e) {
                e.stopPropagation();
                api.column(nameColIndex).search(this.value).draw();
            });

            // ─── فلتر الحالة (status) ──────────────────────
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
        }';
    }

    public function getColumns(): array
    {
        $columns = [
            ['name' => 'DT_RowIndex', 'data' => 'DT_RowIndex', 'title' => '#', 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'name', 'data' => 'name', 'title' => trans('dashboard/departments.name'), 'className' => 'text-center', 'searchable' => true, 'orderable' => false],
            ['name' => 'description', 'data' => 'description', 'title' => trans('dashboard/departments.description'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'status', 'data' => 'status', 'title' => trans('dashboard/departments.status'), 'className' => 'text-center', 'orderable' => false, 'searchable' => true],
        ];

        if (EnsureOwner::check()) {
            $columns[] = ['name' => 'company', 'data' => 'company', 'title' => trans('dashboard/departments.company'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false];
        }

        $columns[] = ['name' => 'created_at', 'data' => 'created_at', 'title' => trans('dashboard/general.created_at'), 'className' => 'text-center', 'searchable' => false];
        $columns[] = ['name' => 'updated_at', 'data' => 'updated_at', 'title' => trans('dashboard/general.updated_at'), 'className' => 'text-center', 'searchable' => false];
        $columns[] = ['name' => 'action', 'data' => 'action', 'title' => trans('dashboard/general.actions'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false];

        return $columns;
    }
}