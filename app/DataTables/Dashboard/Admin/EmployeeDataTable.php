<?php

namespace App\DataTables\Dashboard\Admin;

use App\DataTables\Base\BaseDataTable;
use App\Models\Employee;
use App\Http\Middleware\EnsureOwner;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Utilities\Request as DataTableRequest;
use App\Models\Concerns\UploadMedia;

class EmployeeDataTable extends BaseDataTable
{
    use UploadMedia;
    protected $customFilters = [];
    protected $showTrashed = false;
    public function __construct(DataTableRequest $request)
    {
        parent::__construct(new Employee);
        $this->request = $request;
        $this->showTrashed = $request->get('show_trashed', false) === 'true';
    }

    public function dataTable($query): EloquentDataTable
    {
        $dataTable = (new EloquentDataTable($query))
            ->addColumn('checkbox', function (Employee $employee) {
                return '<input type="checkbox" class="form-check-input row-checkbox" value="' . $employee->id . '">';
            })
            ->addColumn('action', function (Employee $employee) {
                return view('dashboard.admin.employees.btn.actions', compact('employee'));
            })
            ->addColumn('employee', function (Employee $employee) {
                return $this->renderAvatar($employee);
            })
            ->editColumn('name', function (Employee $employee) {
                return $employee->name;
            })
            ->editColumn('email', function (Employee $employee) {
                return '<a href="mailto:' . $employee->email . '">' . $employee->email . '</a>';
            })
            ->editColumn('phone', function (Employee $employee) {
                return $employee->phone ?? '-';
            })
            ->editColumn('department', function (Employee $employee) {
                return $employee->department?->getTranslatedName() ?? '-';
            })
            ->editColumn('status', function (Employee $employee) {
                return $this->renderStatusBadge($employee);
            })
            ->editColumn('type', function (Employee $employee) {
                return $employee->type->badge();
            })
            ->editColumn('date', function (Employee $employee) {
                return $employee->date ? $this->formatTranslatedDate($employee->date) : '-';
            })

            // ─── Filters ──────────────────────────────────────
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
            ->filterColumn('type', function ($query, $keyword) {
                if ($keyword !== '') {
                    $query->where('type', $keyword);
                }
            });

        // ─── Company column for owners ──────────────────────
        if (EnsureOwner::check()) {
            $dataTable->editColumn('company', function (Employee $employee) {
                return $employee->company?->name ?? '-';
            });
        }

        $dataTable
            ->editColumn('created_at', fn(Employee $employee) => $this->formatTranslatedDate($employee->created_at))
            ->editColumn('updated_at', fn(Employee $employee) => $this->formatTranslatedDate($employee->updated_at))
            ->addIndexColumn()
            ->rawColumns(['checkbox', 'action', 'email', 'employee', 'status', 'type', 'created_at', 'updated_at']);

        return $dataTable;
    }

    /**
     * Render avatar image
     */
    private function renderAvatar(Employee $employee): string
    {
        $imageUrl = $this->getMediaUrl('employee', $employee, null, 'media', 'employee');
        if ($imageUrl) {
            return '
                <img src="' . $imageUrl . '"
                     alt="' . e($employee->name) . '"
                     width="40"
                     height="40"
                     style="object-fit: cover; border-radius: 50%; cursor: pointer; border: 2px solid #e0e0e0;"
                     onclick="window.openImageModal(\'' . $imageUrl . '\', \'' . e($employee->name) . '\')">
            ';
        }

        $initials = strtoupper(substr($employee->name, 0, 2));
        return '
            <div class="avatar-placeholder"
                 style="width: 40px; height: 40px; border-radius: 50%; background: #e9ecef; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #6c757d; font-size: 14px; margin: 0 auto;">
                ' . $initials . '
            </div>
        ';
    }

    /**
     * Render status badge with toggle button
     */
    private function renderStatusBadge(Employee $employee): string
    {
        return '
            <div class="gap-1 d-flex flex-column align-items-center">
                <span class="badge-status">' . $employee->status->badge() . '</span>
                <button type="button"
                        class="btn btn-sm btn-outline-secondary toggle-status"
                        data-id="' . $employee->id . '"
                        data-route="' . route('admin.employees.toggleStatus', $employee->id) . '"
                        title="تغيير الحالة">
                    <i class="ti ti-refresh"></i>
                </button>
            </div>
        ';
    }

    public function query(): QueryBuilder
    {
        $query = Employee::query()->with(['department.translations', 'media']);
        if ($this->showTrashed) {
            $query->onlyTrashed();
        } else {
            $query->whereNull('deleted_at');
        }
        $query->latest();
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
                'drawCallback' => $this->getDrawCallbackScript(),
            ]));
    }

    /*protected function getParameters()
    {
        $params = parent::getParameters();
        $bulkActionButton = [
            'extend' => 'collection',
            'text' => '<i class="ti ti-settings me-1"></i> ' . trans('dashboard/employees.bulk_actions') . ' <span class="badge bg-light text-dark ms-1" id="selectedCount">0</span>',
            'className' => 'btn btn-warning',
            'buttons' => [
                [
                    'text' => '<i class="ti ti-exchange me-2 text-primary"></i> ' . trans('dashboard/employees.bulk_change_status'),
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
            ],
            'attr' => [
                'id' => 'bulkActionsBtn',
            ]
        ];
        $params['buttons'] = array_merge([$bulkActionButton], $params['buttons'] ?? []);
        return $params;
    }*/
    protected function getParameters()
    {
        $params = parent::getParameters();
        $bulkButtons = [];

        if ($this->showTrashed) {
            // ─── وضع المحذوفين: استعادة + حذف نهائي ──────────
            $bulkButtons = [
                [
                    'text' => '<i class="ti ti-refresh me-2 text-success"></i> ' . trans('dashboard/employees.bulk_restore'),
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
                    'text' => '<i class="ti ti-trash-off me-2 text-danger"></i> ' . trans('dashboard/employees.bulk_force_delete'),
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
            // ─── وضع النشطين: تغيير حالة + حذف ناعم ──────────
            $bulkButtons = [
                [
                    'text' => '<i class="ti ti-exchange me-2 text-primary"></i> ' . trans('dashboard/employees.bulk_change_status'),
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

        // ─── بناء زر الـ Bulk Actions بالخيارات المناسبة ──────
        $bulkActionButton = [
            'extend' => 'collection',
            'text' => '<i class="ti ti-settings me-1"></i> ' . trans('dashboard/employees.bulk_actions') . ' <span class="badge bg-light text-dark ms-1" id="selectedCount">0</span>',
            'className' => 'btn btn-warning',
            'buttons' => $bulkButtons,
            'attr' => [
                'id' => 'bulkActionsBtn',
            ]
        ];

        $params['buttons'] = array_merge([$bulkActionButton], $params['buttons'] ?? []);
        return $params;
    }
    /**
     * Draw callback for checkbox select all and bulk actions
     */
    private function getDrawCallbackScript(): string
    {
        return '
            function() {
                var table = this;

                // ─── تحديث حالة الـ Bulk Actions ──────────────────
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

                // ─── Individual Checkbox ──────────────────────────
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

                // ─── Select All Checkbox ──────────────────────────
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

                // ─── تنفيذ updateBulkActions بعد التحميل ──────────
                setTimeout(updateBulkActions, 100);
            }';
    }

    private function getInitCompleteScript(): string
    {
        $allText = trans('dashboard/general.all');
        $statuses = [
            'active' => trans('dashboard/employees.status_active'),
            'inactive' => trans('dashboard/employees.status_inactive'),
            'on_leave' => trans('dashboard/employees.status_on_leave'),
            'terminated' => trans('dashboard/employees.status_terminated'),
        ];
        $types = [
            'full_time' => trans('dashboard/employees.type_full_time'),
            'part_time' => trans('dashboard/employees.type_part_time'),
            'contractor' => trans('dashboard/employees.type_contractor'),
            'intern' => trans('dashboard/employees.type_intern'),
            'remote' => trans('dashboard/employees.type_remote'),
        ];
        $statusOptions = '';
        foreach ($statuses as $value => $label) {
            $statusOptions .= '<option value="' . $value . '">' . $label . '</option>';
        }

        $typeOptions = '';
        foreach ($types as $value => $label) {
            $typeOptions .= '<option value="' . $value . '">' . $label . '</option>';
        }

        return '
        function() {
            var api = this.api();

            // ─── Search box بالاسم ──────────────────────────
            var nameColIndex = 3;
            var nameHeader = $(api.column(nameColIndex).header());
            var nameInput = $(\'<input type="text" class="mt-1 form-control form-control-sm" placeholder="' . trans('dashboard/employees.name') . '...">\');
            nameHeader.append(nameInput);

            nameInput.on("keyup change", function(e) {
                e.stopPropagation();
                api.column(nameColIndex).search(this.value).draw();
            });

            // ─── فلتر Status ──────────────────────────────
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

            // ─── فلتر Type ──────────────────────────────────
            var typeColIndex = 7;
            var typeHeader = $(api.column(typeColIndex).header());
            var typeSelect = $(\'<select class="mt-1 form-select form-select-sm">\' +
                \'<option value="">' . $allText . '</option>\' +
                \'' . $typeOptions . '\' +
                \'</select>\');
            typeHeader.append(typeSelect);

            typeSelect.on("change", function(e) {
                e.stopPropagation();
                api.column(typeColIndex).search(this.value).draw();
            });
        }';
    }

    public function getColumns(): array
    {
        $columns = [
            ['name' => 'checkbox', 'data' => 'checkbox', 'title' => '<input type="checkbox" class="form-check-input" id="selectAllCheckbox">', 'className' => 'text-center', 'orderable' => false, 'searchable' => false, 'width' => '50px'],
            ['name' => 'DT_RowIndex', 'data' => 'DT_RowIndex', 'title' => '#', 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'employee', 'data' => 'employee', 'title' => trans('dashboard/employees.avatar'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'name', 'data' => 'name', 'title' => trans('dashboard/employees.name'), 'className' => 'text-center', 'searchable' => true, 'orderable' => true],
            ['name' => 'email', 'data' => 'email', 'title' => trans('dashboard/employees.email'), 'className' => 'text-center', 'searchable' => true, 'orderable' => true],
            ['name' => 'phone', 'data' => 'phone', 'title' => trans('dashboard/employees.phone'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'status', 'data' => 'status', 'title' => trans('dashboard/employees.status'), 'className' => 'text-center', 'orderable' => false, 'searchable' => true],
            ['name' => 'type', 'data' => 'type', 'title' => trans('dashboard/employees.type'), 'className' => 'text-center', 'orderable' => false, 'searchable' => true],
        ];

        if (EnsureOwner::check()) {
            $columns[] = ['name' => 'company', 'data' => 'company', 'title' => trans('dashboard/employees.company'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false];
        }

        $columns[] = ['name' => 'department', 'data' => 'department', 'title' => trans('dashboard/employees.department'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false];
        $columns[] = ['name' => 'date', 'data' => 'date', 'title' => trans('dashboard/employees.date'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false];
        $columns[] = ['name' => 'created_at', 'data' => 'created_at', 'title' => trans('dashboard/general.created_at'), 'className' => 'text-center', 'searchable' => false];
        $columns[] = ['name' => 'updated_at', 'data' => 'updated_at', 'title' => trans('dashboard/general.updated_at'), 'className' => 'text-center', 'searchable' => false];
        $columns[] = ['name' => 'action', 'data' => 'action', 'title' => trans('dashboard/general.actions'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false];
        return $columns;
    }
}
