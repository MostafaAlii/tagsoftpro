<?php

namespace Modules\Provider\DataTables;

use App\DataTables\Base\BaseDataTable;
use Modules\Provider\Entities\Provider;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Utilities\Request as DataTableRequest;

class ProviderDataTable extends BaseDataTable
{
    protected $showTrashed = false;

    public function __construct(DataTableRequest $request)
    {
        parent::__construct(new Provider);
        $this->request = $request;
        $this->showTrashed = $request->get('show_trashed', false) === 'true';
    }

    public function dataTable($query): EloquentDataTable
    {
        $dataTable = (new EloquentDataTable($query))
            ->addColumn('checkbox', function (Provider $provider) {
                return '<input type="checkbox" class="form-check-input row-checkbox" value="' . $provider->id . '">';
            })
            ->addColumn('action', function (Provider $provider) {
                return view('provider::btn.actions', compact('provider'));
            })
            ->editColumn('name', function (Provider $provider) {
                return '<strong>' . e($provider->name) . '</strong>';
            })
            ->editColumn('email', function (Provider $provider) {
                return '<a href="mailto:' . e($provider->email) . '">' . e($provider->email) . '</a>';
            })
            ->editColumn('phone', function (Provider $provider) {
                return $provider->phone ?? '-';
            })
            ->editColumn('status', function (Provider $provider) {
                return $this->renderStatusBadge($provider);
            })
            ->editColumn('zone', function (Provider $provider) {
                return $provider->zone?->name ?? '-';
            })
            ->editColumn('date', function (Provider $provider) {
                return $provider->date ? $provider->date->format('Y-m-d') : '-';
            })
            ->editColumn('created_at', fn(Provider $provider) => $this->formatTranslatedDate($provider->created_at))
            ->editColumn('updated_at', fn(Provider $provider) => $this->formatTranslatedDate($provider->updated_at))
            ->filterColumn('name', function ($query, $keyword) {
                if ($keyword) {
                    $query->where('name', 'like', "%{$keyword}%");
                }
            })
            ->filterColumn('email', function ($query, $keyword) {
                if ($keyword) {
                    $query->where('email', 'like', "%{$keyword}%");
                }
            })
            ->filterColumn('status', function ($query, $keyword) {
                if ($keyword !== '') {
                    $query->where('status', $keyword);
                }
            })
            ->filterColumn('zone', function ($query, $keyword) {
                if ($keyword) {
                    $query->whereHas('zone', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                }
            })
            ->addIndexColumn()
            ->rawColumns(['checkbox', 'action', 'name', 'email', 'status']);

        return $dataTable;
    }

    private function renderStatusBadge(Provider $provider): string
    {
        try {
            // ─── استخدم الـ badge من الـ Enum ────────────────────
            $badge = $provider->status->badge();
        } catch (\Exception $e) {
            // ─── Fallback ──────────────────────────────────────────
            $statuses = [
                'active' => 'success',
                'inactive' => 'danger',
                'pending' => 'warning',
                'suspended' => 'secondary',
            ];
            $statusValue = $provider->status instanceof \BackedEnum
                ? $provider->status->value
                : $provider->status;
            $color = $statuses[$statusValue] ?? 'secondary';
            $label = ucfirst($statusValue);
            $badge = '<span class="badge bg-' . $color . '">' . $label . '</span>';
        }

        if ($this->showTrashed) {
            return '<span class="badge-status">' . $badge . '</span>';
        }

        return '
            <div class="gap-1 d-flex flex-column align-items-center">
                <span class="badge-status">' . $badge . '</span>
                <button type="button"
                        class="btn btn-sm btn-outline-secondary toggle-status"
                        data-id="' . $provider->id . '"
                        data-route="' . route('admin.providers.toggleStatus', $provider->id) . '"
                        title="تغيير الحالة">
                    <i class="ti ti-refresh"></i>
                </button>
            </div>
        ';
    }

    public function query(): QueryBuilder
    {
        $query = Provider::query()->with(['zone']);

        if ($this->showTrashed) {
            $query->onlyTrashed();
        } else {
            $query->whereNull('deleted_at');
        }

        $query->latest();

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('providers_datatable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters(array_merge($this->getParameters(), [
                'initComplete' => $this->getInitCompleteScript(),
                'drawCallback' => $this->getDrawCallbackScript(),
            ]));
    }

    protected function getParameters()
    {
        $params = parent::getParameters();
        $bulkButtons = [];

        if ($this->showTrashed) {
            $bulkButtons = [
                [
                    'text' => '<i class="ti ti-refresh me-2 text-success"></i> ' . trans('provider::providers.bulk_restore'),
                    'className' => 'text-success',
                    'action' => 'function(e, dt, node, config) {
                        if (typeof openBulkRestoreModal === "function") {
                            openBulkRestoreModal();
                        } else if (typeof window.openBulkRestoreModal === "function") {
                            window.openBulkRestoreModal();
                        }
                    }',
                ],
                [
                    'text' => '<i class="ti ti-trash-off me-2 text-danger"></i> ' . trans('provider::providers.bulk_force_delete'),
                    'className' => 'text-danger',
                    'action' => 'function(e, dt, node, config) {
                        if (typeof openBulkForceDeleteModal === "function") {
                            openBulkForceDeleteModal();
                        } else if (typeof window.openBulkForceDeleteModal === "function") {
                            window.openBulkForceDeleteModal();
                        }
                    }',
                ],
            ];
        } else {
            $bulkButtons = [
                [
                    'text' => '<i class="ti ti-exchange me-2 text-primary"></i> ' . trans('provider::providers.bulk_change_status'),
                    'action' => 'function(e, dt, node, config) {
                        if (typeof openBulkStatusModal === "function") {
                            openBulkStatusModal();
                        } else if (typeof window.openBulkStatusModal === "function") {
                            window.openBulkStatusModal();
                        }
                    }',
                ],
                [
                    'text' => '<i class="ti ti-trash me-2 text-danger"></i> ' . trans('provider::providers.delete_selected'),
                    'className' => 'text-danger',
                    'action' => 'function(e, dt, node, config) {
                        if (typeof openBulkDeleteModal === "function") {
                            openBulkDeleteModal();
                        } else if (typeof window.openBulkDeleteModal === "function") {
                            window.openBulkDeleteModal();
                        }
                    }',
                ],
            ];
        }

        $bulkActionButton = [
            'extend' => 'collection',
            'text' => '<i class="ti ti-settings me-1"></i> ' . trans('provider::providers.bulk_actions') . ' <span class="badge bg-light text-dark ms-1" id="selectedCount">0</span>',
            'className' => 'btn btn-warning',
            'buttons' => $bulkButtons,
            'attr' => [
                'id' => 'bulkActionsBtn',
            ]
        ];

        $params['buttons'] = array_merge([$bulkActionButton], $params['buttons'] ?? []);
        return $params;
    }

    private function getDrawCallbackScript(): string
    {
        return '
            function() {
                var table = this;

                function updateBulkActions() {
                    var checked = document.querySelectorAll("tbody .row-checkbox:checked");
                    var selectedCount = document.getElementById("selectedCount");
                    var bulkBtn = document.getElementById("bulkActionsBtn");

                    if (checked.length > 0) {
                        if (bulkBtn) {
                            bulkBtn.style.display = "inline-block";
                        }
                        if (selectedCount) {
                            selectedCount.textContent = checked.length;
                        }
                    } else {
                        if (bulkBtn) {
                            bulkBtn.style.display = "none";
                        }
                    }
                }

                document.querySelectorAll("tbody .row-checkbox").forEach(function(cb) {
                    cb.removeEventListener("change", handleCheckboxChange);
                    cb.addEventListener("change", handleCheckboxChange);
                });

                function handleCheckboxChange() {
                    var allChecked = true;
                    document.querySelectorAll("tbody .row-checkbox").forEach(function(c) {
                        if (!c.checked) allChecked = false;
                    });
                    var selectAll = document.getElementById("selectAllCheckbox");
                    if (selectAll) {
                        selectAll.checked = allChecked;
                    }
                    updateBulkActions();
                }

                var selectAll = document.getElementById("selectAllCheckbox");
                if (selectAll) {
                    selectAll.removeEventListener("change", handleSelectAllChange);
                    selectAll.addEventListener("change", handleSelectAllChange);
                }

                function handleSelectAllChange() {
                    var isChecked = this.checked;
                    document.querySelectorAll("tbody .row-checkbox").forEach(function(cb) {
                        cb.checked = isChecked;
                    });
                    updateBulkActions();
                }

                setTimeout(updateBulkActions, 100);
            }';
    }

    private function getInitCompleteScript(): string
    {
        $allText = trans('provider::providers.all');
        $statuses = [
            'active' => trans('provider::providers.status_active'),
            'inactive' => trans('provider::providers.status_inactive'),
            'pending' => trans('provider::providers.status_pending'),
            'suspended' => trans('provider::providers.status_suspended'),
        ];

        $statusOptions = '';
        foreach ($statuses as $value => $label) {
            $statusOptions .= '<option value="' . $value . '">' . $label . '</option>';
        }

        return '
        function() {
            var api = this.api();

            var nameColIndex = 2;
            var nameHeader = $(api.column(nameColIndex).header());
            var nameInput = $(\'<input type="text" class="mt-1 form-control form-control-sm" placeholder="' . trans('provider::providers.name') . '...">\');
            nameHeader.append(nameInput);

            nameInput.on("keyup change", function(e) {
                e.stopPropagation();
                api.column(nameColIndex).search(this.value).draw();
            });

            var emailColIndex = 3;
            var emailHeader = $(api.column(emailColIndex).header());
            var emailInput = $(\'<input type="text" class="mt-1 form-control form-control-sm" placeholder="' . trans('provider::providers.email') . '...">\');
            emailHeader.append(emailInput);

            emailInput.on("keyup change", function(e) {
                e.stopPropagation();
                api.column(emailColIndex).search(this.value).draw();
            });

            var statusColIndex = 6;
            var statusHeader = $(api.column(statusColIndex).header());
            var statusSelect = $(\'<select class="mt-1 form-select form-select-sm">\' +
                \'<option value="">' . $allText . '</option>\' +
                \'' . $statusOptions . '\' +
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
            ['name' => 'checkbox', 'data' => 'checkbox', 'title' => '<input type="checkbox" class="form-check-input" id="selectAllCheckbox">', 'className' => 'text-center', 'orderable' => false, 'searchable' => false, 'width' => '50px'],
            ['name' => 'DT_RowIndex', 'data' => 'DT_RowIndex', 'title' => '#', 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'name', 'data' => 'name', 'title' => trans('provider::providers.name'), 'className' => 'text-center', 'searchable' => true, 'orderable' => true],
            ['name' => 'email', 'data' => 'email', 'title' => trans('provider::providers.email'), 'className' => 'text-center', 'searchable' => true, 'orderable' => true],
            ['name' => 'phone', 'data' => 'phone', 'title' => trans('provider::providers.phone'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'zone', 'data' => 'zone', 'title' => trans('provider::providers.zone'), 'className' => 'text-center', 'orderable' => false, 'searchable' => true],
            ['name' => 'status', 'data' => 'status', 'title' => trans('provider::providers.status'), 'className' => 'text-center', 'orderable' => false, 'searchable' => true],
            ['name' => 'date', 'data' => 'date', 'title' => trans('provider::providers.date'), 'className' => 'text-center', 'searchable' => false],
            ['name' => 'created_at', 'data' => 'created_at', 'title' => trans('provider::providers.created_at'), 'className' => 'text-center', 'searchable' => false],
            ['name' => 'updated_at', 'data' => 'updated_at', 'title' => trans('provider::providers.updated_at'), 'className' => 'text-center', 'searchable' => false],
            ['name' => 'action', 'data' => 'action', 'title' => trans('provider::providers.actions'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
        ];

        return $columns;
    }
}