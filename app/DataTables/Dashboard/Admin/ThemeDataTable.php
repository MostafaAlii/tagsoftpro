<?php

namespace App\DataTables\Dashboard\Admin;

use App\DataTables\Base\BaseDataTable;
use App\Models\{Theme, ProjectType, Company};
use App\Enums\Theme\ThemeStatus;
use App\Http\Middleware\EnsureOwner;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Utilities\Request as DataTableRequest;
use Illuminate\Support\Str;

class ThemeDataTable extends BaseDataTable
{
    protected $customFilters = [];
    protected $projectTypes;
    
    public function __construct(DataTableRequest $request)
    {
        parent::__construct(new Theme);
        $this->request = $request;
        $this->projectTypes = ProjectType::active()->get();
    }

    public function dataTable($query): EloquentDataTable
    {
        $dataTable = (new EloquentDataTable($query))
            ->addColumn('action', function (Theme $theme) {
                return view('dashboard.admin.themes.btn.actions', compact('theme'));
            })
            ->editColumn('name', function (Theme $theme) {
                return $theme->name;
            })
            ->editColumn('code', function (Theme $theme) {
                return '<span class="badge bg-secondary">' . $theme->code . '</span>';
            })
            ->editColumn('description', function (Theme $theme) {
                return $theme->description ? Str::limit($theme->description, 50) : '-';
            })
            ->editColumn('paid_type', function (Theme $theme) {
                return $theme->paid_type->badge();
            })
            ->editColumn('price', function (Theme $theme) {
                return $theme->getFormattedPrice();
            })
            // ✅ عرض عدد الـ Default
            ->addColumn('default_count', function (Theme $theme) {
                $count = $theme->defaultProjectTypes()->count();
                $total = $this->projectTypes->count();
                return $this->renderDefaultCount($theme, $count, $total);
            })
            // ✅ عرض عدد الـ Active
            ->addColumn('active_count', function (Theme $theme) {
                $count = $theme->activeProjectTypes()->count();
                $total = $this->projectTypes->count();
                return $this->renderActiveCount($theme, $count, $total);
            })

            // ─── Filters ──────────────────────────────────────
            ->filterColumn('name', function ($query, $keyword) {
                if ($keyword) {
                    $query->where('name', 'like', "%{$keyword}%");
                }
            })
            ->filterColumn('code', function ($query, $keyword) {
                if ($keyword) {
                    $query->where('code', 'like', "%{$keyword}%");
                }
            })
            ->filterColumn('paid_type', function ($query, $keyword) {
                if ($keyword !== '') {
                    $query->where('paid_type', $keyword);
                }
            });

        // ─── Company column for owners ──────────────────────
        if (EnsureOwner::check()) {
            $dataTable->editColumn('company', function (Theme $theme) {
                return $theme->company?->name ?? '-';
            });
        }

        $dataTable
            ->editColumn('created_at', fn(Theme $theme) => $this->formatTranslatedDate($theme->created_at))
            ->editColumn('updated_at', fn(Theme $theme) => $this->formatTranslatedDate($theme->updated_at))
            ->addIndexColumn()
            ->rawColumns(['action', 'code', 'default_count', 'active_count', 'paid_type', 'price', 'created_at', 'updated_at']);

        return $dataTable;
    }

    /**
     * ✅ عرض عدد الـ Default مع أيقونة للضغط
     */
    private function renderDefaultCount(Theme $theme, int $count, int $total): string
    {
        $badgeColor = $count > 0 ? 'primary' : 'secondary';
        return '
            <button type="button" 
                class="btn btn-sm btn-outline-' . $badgeColor . ' btn-show-defaults" 
                data-theme-id="' . $theme->id . '"
                data-theme-name="' . e($theme->name) . '"
                data-bs-toggle="modal" 
                data-bs-target="#defaultStatusModal">
                <i class="ti ti-star me-1"></i>
                ' . $count . '/' . $total . '
            </button>
        ';
    }

    /**
     * ✅ عرض عدد الـ Active مع أيقونة للضغط
     */
    private function renderActiveCount(Theme $theme, int $count, int $total): string
    {
        $badgeColor = $count > 0 ? 'success' : 'secondary';
        return '
            <button type="button" 
                class="btn btn-sm btn-outline-' . $badgeColor . ' btn-show-statuses" 
                data-theme-id="' . $theme->id . '"
                data-theme-name="' . e($theme->name) . '"
                data-bs-toggle="modal" 
                data-bs-target="#statusModal">
                <i class="ti ti-check-circle me-1"></i>
                ' . $count . '/' . $total . '
            </button>
        ';
    }

    public function query(): QueryBuilder
    {
        $query = Theme::query()->latest();
        if (EnsureOwner::check()) {
            $query->with(['company']);
        }
        $query->with(['projectTypes']);
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
        $freeText = trans('dashboard/themes.paid_type_free');
        $paidText = trans('dashboard/themes.paid_type_paid');

        return '
        function() {
            var api = this.api();

            // ─── Search box بالاسم ──────────────────────────
            var nameColIndex = 1;
            var nameHeader = $(api.column(nameColIndex).header());
            var nameInput = $(\'<input type="text" class="mt-1 form-control form-control-sm" placeholder="' . trans('dashboard/themes.name') . '...">\');
            nameHeader.append(nameInput);

            nameInput.on("keyup change", function(e) {
                e.stopPropagation();
                api.column(nameColIndex).search(this.value).draw();
            });

            // ─── فلتر Paid Type ──────────────────────────────
            var paidTypeColIndex = 4;
            var paidTypeHeader = $(api.column(paidTypeColIndex).header());
            var paidTypeSelect = $(\'<select class="mt-1 form-select form-select-sm">\' +
                \'<option value="">' . $allText . '</option>\' +
                \'<option value="free">' . $freeText . '</option>\' +
                \'<option value="paid">' . $paidText . '</option>\' +
                \'</select>\');
            paidTypeHeader.append(paidTypeSelect);

            paidTypeSelect.on("change", function(e) {
                e.stopPropagation();
                api.column(paidTypeColIndex).search(this.value).draw();
            });
        }';
    }

    public function getColumns(): array
    {
        $columns = [
            ['name' => 'DT_RowIndex', 'data' => 'DT_RowIndex', 'title' => '#', 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'name', 'data' => 'name', 'title' => trans('dashboard/themes.name'), 'className' => 'text-center', 'searchable' => true, 'orderable' => true],
            ['name' => 'code', 'data' => 'code', 'title' => trans('dashboard/themes.code'), 'className' => 'text-center', 'searchable' => true, 'orderable' => true],
            ['name' => 'description', 'data' => 'description', 'title' => trans('dashboard/themes.description'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'paid_type', 'data' => 'paid_type', 'title' => trans('dashboard/themes.paid_type'), 'className' => 'text-center', 'orderable' => false, 'searchable' => true],
            ['name' => 'price', 'data' => 'price', 'title' => trans('dashboard/themes.price'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'default_count', 'data' => 'default_count', 'title' => trans('dashboard/themes.default_status'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'active_count', 'data' => 'active_count', 'title' => trans('dashboard/themes.active_status'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
        ];

        if (EnsureOwner::check()) {
            $columns[] = ['name' => 'company', 'data' => 'company', 'title' => trans('dashboard/themes.company'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false];
        }

        $columns[] = ['name' => 'created_at', 'data' => 'created_at', 'title' => trans('dashboard/general.created_at'), 'className' => 'text-center', 'searchable' => false];
        $columns[] = ['name' => 'updated_at', 'data' => 'updated_at', 'title' => trans('dashboard/general.updated_at'), 'className' => 'text-center', 'searchable' => false];
        $columns[] = ['name' => 'action', 'data' => 'action', 'title' => trans('dashboard/general.actions'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false];

        return $columns;
    }
}