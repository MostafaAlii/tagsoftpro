<?php

namespace Modules\Vendor\DataTables;

use App\DataTables\Base\BaseDataTable;
use Modules\Vendor\Entities\Vendor;
use App\Http\Middleware\EnsureOwner;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Utilities\Request as DataTableRequest;

class VendorDataTable extends BaseDataTable
{
    protected $showTrashed = false;

    public function __construct(DataTableRequest $request)
    {
        parent::__construct(new Vendor);
        $this->request = $request;
        $this->showTrashed = $request->get('show_trashed', false) === 'true';
    }

    public function dataTable($query): EloquentDataTable
    {
        $dataTable = (new EloquentDataTable($query))
            ->addColumn('checkbox', function (Vendor $vendor) {
                return '<input type="checkbox" class="form-check-input row-checkbox" value="' . $vendor->id . '">';
            })
            ->addColumn('action', function (Vendor $vendor) {
                return view('vendor::btn.actions', compact('vendor'));
            })
            ->editColumn('name', function (Vendor $vendor) {
                return '<strong>' . $vendor->name . '</strong>';
            })
            ->editColumn('email', function (Vendor $vendor) {
                return '<a href="mailto:' . $vendor->email . '">' . $vendor->email . '</a>';
            })
            ->editColumn('phone', function (Vendor $vendor) {
                return $vendor->phone ?? '-';
            })
            ->editColumn('status', function (Vendor $vendor) {
                return $this->renderStatusBadge($vendor);
            })
            ->editColumn('type', function (Vendor $vendor) {
                return $vendor->type ? ucfirst($vendor->type) : '-';
            })
            ->editColumn('date', function (Vendor $vendor) {
                return $vendor->date ? $vendor->date->format('Y-m-d') : '-';
            })
            ->editColumn('created_at', fn(Vendor $vendor) => $this->formatTranslatedDate($vendor->created_at))
            ->editColumn('updated_at', fn(Vendor $vendor) => $this->formatTranslatedDate($vendor->updated_at))
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
            ->addIndexColumn()
            ->rawColumns(['checkbox', 'action', 'name', 'email', 'status']);

        if (EnsureOwner::check()) {
            $dataTable->editColumn('company', function (Vendor $vendor) {
                return $vendor->company?->name ?? '-';
            });

            $dataTable->editColumn('department', function (Vendor $vendor) {
                return $vendor->department?->name ?? '-';
            });
        }

        return $dataTable;
    }

    private function renderStatusBadge(Vendor $vendor): string
    {
        $statuses = [
            'active' => 'success',
            'inactive' => 'danger',
            'pending' => 'warning',
            'suspended' => 'secondary',
        ];

        $color = $statuses[$vendor->status] ?? 'secondary';
        $label = ucfirst($vendor->status);

        if ($this->showTrashed) {
            return '<span class="badge-status"><span class="badge bg-' . $color . '">' . $label . '</span></span>';
        }

        return '
            <div class="gap-1 d-flex flex-column align-items-center">
                <span class="badge-status"><span class="badge bg-' . $color . '">' . $label . '</span></span>
                <button type="button"
                        class="btn btn-sm btn-outline-secondary toggle-status"
                        data-id="' . $vendor->id . '"
                        data-route="' . route('admin.vendors.toggleStatus', $vendor->id) . '"
                        title="تغيير الحالة">
                    <i class="ti ti-refresh"></i>
                </button>
            </div>
        ';
    }

    public function query(): QueryBuilder
    {
        $query = Vendor::query();

        if ($this->showTrashed) {
            $query->onlyTrashed();
        } else {
            $query->whereNull('deleted_at');
        }

        $query->latest();

        if (EnsureOwner::check()) {
            $query->with(['company', 'department']);
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
                    'text' => '<i class="ti ti-refresh me-2 text-success"></i> ' . trans('vendor::vendors.bulk_restore'),
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
                    'text' => '<i class="ti ti-trash-off me-2 text-danger"></i> ' . trans('vendor::vendors.bulk_force_delete'),
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
                    'text' => '<i class="ti ti-exchange me-2 text-primary"></i> ' . trans('vendor::vendors.bulk_change_status'),
                    'action' => 'function(e, dt, node, config) {
                        if (typeof openBulkStatusModal === "function") {
                            openBulkStatusModal();
                        } else if (typeof window.openBulkStatusModal === "function") {
                            window.openBulkStatusModal();
                        }
                    }',
                ],
                [
                    'text' => '<i class="ti ti-trash me-2 text-danger"></i> ' . trans('dashboard/general.delete_selected'),
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
            'text' => '<i class="ti ti-settings me-1"></i> ' . trans('vendor::vendors.bulk_actions') . ' <span class="badge bg-light text-dark ms-1" id="selectedCount">0</span>',
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
        $allText = trans('dashboard/general.all');
        $statuses = [
            'active' => trans('vendor::vendors.status_active'),
            'inactive' => trans('vendor::vendors.status_inactive'),
            'pending' => trans('vendor::vendors.status_pending'),
            'suspended' => trans('vendor::vendors.status_suspended'),
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
            var nameInput = $(\'<input type="text" class="mt-1 form-control form-control-sm" placeholder="' . trans('dashboard/vendors.name') . '...">\');
            nameHeader.append(nameInput);

            nameInput.on("keyup change", function(e) {
                e.stopPropagation();
                api.column(nameColIndex).search(this.value).draw();
            });

            var emailColIndex = 3;
            var emailHeader = $(api.column(emailColIndex).header());
            var emailInput = $(\'<input type="text" class="mt-1 form-control form-control-sm" placeholder="' . trans('vendor::vendors.email') . '...">\');
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
            ['name' => 'name', 'data' => 'name', 'title' => trans('vendor::vendors.name'), 'className' => 'text-center', 'searchable' => true, 'orderable' => true],
            ['name' => 'email', 'data' => 'email', 'title' => trans('vendor::vendors.email'), 'className' => 'text-center', 'searchable' => true, 'orderable' => true],
            ['name' => 'phone', 'data' => 'phone', 'title' => trans('vendor::vendors.phone'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'type', 'data' => 'type', 'title' => trans('vendor::vendors.type'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'status', 'data' => 'status', 'title' => trans('vendor::vendors.status'), 'className' => 'text-center', 'orderable' => false, 'searchable' => true],
        ];

        if (EnsureOwner::check()) {
            $columns[] = ['name' => 'company', 'data' => 'company', 'title' => trans('vendor::vendors.company'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false];
            $columns[] = ['name' => 'department', 'data' => 'department', 'title' => trans('vendor::vendors.department'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false];
        }

        $columns[] = ['name' => 'date', 'data' => 'date', 'title' => trans('vendor::vendors.date'), 'className' => 'text-center', 'searchable' => false];
        $columns[] = ['name' => 'created_at', 'data' => 'created_at', 'title' => trans('dashboard/general.created_at'), 'className' => 'text-center', 'searchable' => false];
        $columns[] = ['name' => 'updated_at', 'data' => 'updated_at', 'title' => trans('dashboard/general.updated_at'), 'className' => 'text-center', 'searchable' => false];
        $columns[] = ['name' => 'action', 'data' => 'action', 'title' => trans('dashboard/general.actions'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false];

        return $columns;
    }
}