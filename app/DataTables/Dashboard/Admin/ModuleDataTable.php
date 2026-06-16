<?php

namespace App\DataTables\Dashboard\Admin;

use App\DataTables\Base\BaseDataTable;
use App\Models\Module;
use App\Enums\Module\ModuleStatus;
use App\Http\Middleware\EnsureOwner;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Utilities\Request as DataTableRequest;
use Illuminate\Support\Str;

class ModuleDataTable extends BaseDataTable
{
    protected $customFilters = [];

    public function __construct(DataTableRequest $request)
    {
        parent::__construct(new Module);
        $this->request = $request;
    }

    public function dataTable($query): EloquentDataTable
    {
        $dataTable = (new EloquentDataTable($query))
            ->addColumn('action', function (Module $module) {
                return view('dashboard.admin.modules.btn.actions', compact('module'));
            })
            ->editColumn('name', function (Module $module) {
                return $module->translate(app()->getLocale())?->name
                    ?? $module->translate('ar')?->name
                    ?? '-';
            })
            ->editColumn('description', function (Module $module) {
                $desc = $module->translate(app()->getLocale())?->description
                    ?? $module->translate('ar')?->description;
                return $desc ? Str::limit($desc, 50) : '-';
            })
            ->editColumn('project_types', function (Module $module) {
                return $this->renderProjectTypesBadge($module);
            })
            ->editColumn('status', function (Module $module) {
                return $this->renderStatusToggle($module);
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
            $dataTable->editColumn('company', function (Module $module) {
                return $module->company?->name ?? '-';
            });
        }

        $dataTable
            ->editColumn('created_at', fn(Module $module) => $this->formatTranslatedDate($module->created_at))
            ->editColumn('updated_at', fn(Module $module) => $this->formatTranslatedDate($module->updated_at))
            ->addIndexColumn()
            ->rawColumns(['action', 'project_types', 'status', 'created_at', 'updated_at']);

        return $dataTable;
    }

    /**
     * Render project types badge with clickable modal
     */
    private function renderProjectTypesBadge(Module $module): string
    {
        $projectTypes = $module->projectTypes()->with('translations')->get();
        $count = $projectTypes->count();

        if ($count === 0) {
            return '<span class="text-muted">-</span>';
        }

        // Build list of project types for the modal
        $listHtml = '';
        foreach ($projectTypes as $pt) {
            $listHtml .= '<span class="badge bg-secondary me-1 mb-1">' . $pt->getTranslatedName() . '</span>';
        }

        return '
        <div class="d-flex flex-column align-items-center gap-1">
            <span class="badge bg-primary" style="cursor: pointer;" 
                  data-bs-toggle="modal" 
                  data-bs-target="#projectTypesModal"
                  data-module-id="' . $module->id . '"
                  data-module-name="' . $module->getTranslatedName() . '"
                  data-project-types=\'' . json_encode($projectTypes->map(fn($pt) => ['id' => $pt->id, 'name' => $pt->getTranslatedName()])) . '\'>
                <i class="ti ti-folder me-1"></i>
                ' . $count . ' ' . trans('dashboard/modules.project_types') . '
            </span>
            <div class="d-flex flex-wrap justify-content-center" style="max-width: 200px;">
                ' . $listHtml . '
            </div>
        </div>
        ';
    }

    /**
     * Render status toggle button
     */
    private function renderStatusToggle(Module $module): string
    {
        return '
            <div class="gap-1 d-flex flex-column align-items-center">
                <span class="badge-status">' . $module->status->badge() . '</span>
                <div class="form-check form-switch">
                    <input type="checkbox"
                        class="form-check-input toggle-status"
                        data-id="' . $module->id . '"
                        data-route="' . route('admin.modules.toggleStatus', $module->id) . '"
                        ' . ($module->status === ModuleStatus::ACTIVE ? 'checked' : '') . '>
                </div>
            </div>
        ';
    }

    public function query(): QueryBuilder
    {
        $query = Module::query()
            ->with(['translations', 'projectTypes.translations'])
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
            var nameInput = $(\'<input type="text" class="mt-1 form-control form-control-sm" placeholder="' . trans('dashboard/modules.name') . '...">\');
            nameHeader.append(nameInput);

            nameInput.on("keyup change", function(e) {
                e.stopPropagation();
                api.column(nameColIndex).search(this.value).draw();
            });

            nameInput.on("click", function(e) {
                e.stopPropagation();
            });

            // ─── فلتر الحالة (status) ──────────────────────────────────────
            var statusColIndex = 4;
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
            ['name' => 'name', 'data' => 'name', 'title' => trans('dashboard/modules.name'), 'className' => 'text-center', 'searchable' => true, 'orderable' => true],
            ['name' => 'project_types', 'data' => 'project_types', 'title' => trans('dashboard/modules.project_types'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'description', 'data' => 'description', 'title' => trans('dashboard/modules.description'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'status', 'data' => 'status', 'title' => trans('dashboard/modules.status'), 'className' => 'text-center', 'orderable' => false, 'searchable' => true],
        ];

        if (EnsureOwner::check()) {
            $columns[] = ['name' => 'company', 'data' => 'company', 'title' => trans('dashboard/modules.company'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false];
        }

        $columns[] = ['name' => 'created_at', 'data' => 'created_at', 'title' => trans('dashboard/general.created_at'), 'className' => 'text-center', 'searchable' => false];
        $columns[] = ['name' => 'updated_at', 'data' => 'updated_at', 'title' => trans('dashboard/general.updated_at'), 'className' => 'text-center', 'searchable' => false];
        $columns[] = ['name' => 'action', 'data' => 'action', 'title' => trans('dashboard/general.actions'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false];

        return $columns;
    }
}