<?php

namespace App\DataTables\Dashboard\Admin;

use App\DataTables\Base\BaseDataTable;
use App\Models\Plan;
use App\Enums\Plan\{PlanStatus, PlanBillingCycle};
use App\Http\Middleware\EnsureOwner;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Utilities\Request as DataTableRequest;

class PlanDataTable extends BaseDataTable
{
    protected $customFilters = [];

    public function __construct(DataTableRequest $request)
    {
        parent::__construct(new Plan);
        $this->request = $request;
    }

    public function dataTable($query): EloquentDataTable
    {
        $dataTable = (new EloquentDataTable($query))
            ->addColumn('action', function (Plan $plan) {
                return view('dashboard.admin.plans.btn.actions', compact('plan'));
            })
            ->editColumn('name', function (Plan $plan) {
                return $plan->translate(app()->getLocale())?->name
                    ?? $plan->translate('ar')?->name
                    ?? '-';
            })
            ->editColumn('price', function (Plan $plan) {
                return number_format($plan->price, 2) . ' ' . (config('app.currency') ?? 'EGP');
            })
            ->editColumn('billing_cycle', function (Plan $plan) {
                return $this->renderBillingCycleDropdown($plan);
            })
            ->editColumn('status', function (Plan $plan) {
                return $this->renderStatusToggle($plan);
            })

            // ─── Filters ──────────────────────────────────────
            ->filterColumn('name', function ($query, $keyword) {
                if ($keyword) {
                    $query->whereHas('translations', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                }
            })
            ->filterColumn('billing_cycle', function ($query, $keyword) {
                if ($keyword !== '') {
                    $query->where('billing_cycle', $keyword);
                }
            })
            ->filterColumn('status', function ($query, $keyword) {
                if ($keyword !== '') {
                    $query->where('status', (int) $keyword);
                }
            });

        // ─── Company column for owners ──────────────────────
        if (EnsureOwner::check()) {
            $dataTable->editColumn('company', function (Plan $plan) {
                return $plan->company?->name ?? '-';
            });
        }

        $dataTable
            ->editColumn('created_at', fn(Plan $plan) => $this->formatTranslatedDate($plan->created_at))
            ->editColumn('updated_at', fn(Plan $plan) => $this->formatTranslatedDate($plan->updated_at))
            ->addIndexColumn()
            ->rawColumns(['action', 'billing_cycle', 'status', 'created_at', 'updated_at']);

        return $dataTable;
    }

    /**
     * Render status toggle button
     */
    private function renderStatusToggle(Plan $plan): string
    {
        return '
            <div class="gap-1 d-flex flex-column align-items-center">
                <span class="badge-status">' . $plan->status->badge() . '</span>
                <div class="form-check form-switch">
                    <input type="checkbox"
                        class="form-check-input toggle-status"
                        data-id="' . $plan->id . '"
                        data-route="' . route('admin.plans.toggleStatus', $plan->id) . '"
                        ' . ($plan->status === PlanStatus::ACTIVE ? 'checked' : '') . '>
                </div>
            </div>
        ';
    }

    /**
     * Render billing cycle dropdown
     */
    private function renderBillingCycleDropdown(Plan $plan): string {
        $isMonthly = $plan->billing_cycle === PlanBillingCycle::MONTHLY;
        $label = $isMonthly ? trans('dashboard/plans.billing_monthly') : trans('dashboard/plans.billing_yearly');
        $badgeClass = $isMonthly ? 'bg-success' : 'bg-info';

        return '
            <div class="dropdown d-inline-block billing-cycle-dropdown" style="min-width: 110px;">
                <button class="btn btn-sm ' . $badgeClass . ' dropdown-toggle toggle-billing-cycle-btn" 
                        type="button" 
                        data-bs-toggle="dropdown" 
                        aria-expanded="false"
                        data-id="' . $plan->id . '"
                        style="font-size: 12px; padding: 4px 12px; min-width: 70px;">
                    ' . $label . '
                </button>
                <ul class="dropdown-menu" style="min-width: 100px;">
                    <li>
                        <a class="dropdown-item toggle-billing-cycle-item ' . ($isMonthly ? 'active' : '') . '" 
                        href="#" 
                        data-value="monthly"
                        data-id="' . $plan->id . '"
                        data-route="' . route('admin.plans.toggleBillingCycle', $plan->id) . '">
                            <i class="ti ti-calendar-month me-2"></i>
                            ' . trans('dashboard/plans.billing_monthly') . '
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item toggle-billing-cycle-item ' . (!$isMonthly ? 'active' : '') . '" 
                        href="#" 
                        data-value="yearly"
                        data-id="' . $plan->id . '"
                        data-route="' . route('admin.plans.toggleBillingCycle', $plan->id) . '">
                            <i class="ti ti-calendar-year me-2"></i>
                            ' . trans('dashboard/plans.billing_yearly') . '
                        </a>
                    </li>
                </ul>
            </div>
        ';
    }

    public function query(): QueryBuilder
    {
        $query = Plan::query()->with(['translations'])->latest();

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
    private function getInitCompleteScript(): string {
        $allText = trans('dashboard/general.all');
        $activeText = trans('dashboard/general.active');
        $inactiveText = trans('dashboard/general.in_active');
        $monthlyText = trans('dashboard/plans.billing_monthly');
        $yearlyText = trans('dashboard/plans.billing_yearly');
        return '
        function() {
            var api = this.api();

            // ─── Search box بالاسم (فلتر العمود name) ───────────────
            var nameColIndex = 1;
            var nameHeader = $(api.column(nameColIndex).header());
            var nameInput = $(\'<input type="text" class="mt-1 form-control form-control-sm" placeholder="' . trans('dashboard/plans.name') . '...">\');
            nameHeader.append(nameInput);

            nameInput.on("keyup change", function(e) {
                e.stopPropagation();
                api.column(nameColIndex).search(this.value).draw();
            });

            nameInput.on("click", function(e) {
                e.stopPropagation();
            });

            // ─── فلتر billing_cycle ──────────────────────────────────
            var cycleColIndex = 3;
            var cycleHeader = $(api.column(cycleColIndex).header());
            var cycleSelect = $(\'<select class="mt-1 form-select form-select-sm">\' +
                \'<option value="">' . $allText . '</option>\' +
                \'<option value="monthly">' . $monthlyText . '</option>\' +
                \'<option value="yearly">' . $yearlyText . '</option>\' +
                \'</select>\');
            cycleHeader.append(cycleSelect);

            cycleSelect.on("change", function(e) {
                e.stopPropagation();
                api.column(cycleColIndex).search(this.value).draw();
            });

            cycleSelect.on("click", function(e) {
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
            ['name' => 'name', 'data' => 'name', 'title' => trans('dashboard/plans.name'), 'className' => 'text-center', 'searchable' => true, 'orderable' => true],
            ['name' => 'price', 'data' => 'price', 'title' => trans('dashboard/plans.price'), 'className' => 'text-center', 'orderable' => true, 'searchable' => false],
            ['name' => 'billing_cycle', 'data' => 'billing_cycle', 'title' => trans('dashboard/plans.billing_cycle'), 'className' => 'text-center', 'orderable' => false, 'searchable' => true],
            ['name' => 'status', 'data' => 'status', 'title' => trans('dashboard/plans.status'), 'className' => 'text-center', 'orderable' => false, 'searchable' => true],
        ];

        if (EnsureOwner::check()) {
            $columns[] = ['name' => 'company', 'data' => 'company', 'title' => trans('dashboard/plans.company'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false];
        }

        $columns[] = ['name' => 'created_at', 'data' => 'created_at', 'title' => trans('dashboard/general.created_at'), 'className' => 'text-center', 'searchable' => false];
        $columns[] = ['name' => 'updated_at', 'data' => 'updated_at', 'title' => trans('dashboard/general.updated_at'), 'className' => 'text-center', 'searchable' => false];
        $columns[] = ['name' => 'action', 'data' => 'action', 'title' => trans('dashboard/general.actions'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false];

        return $columns;
    }
}