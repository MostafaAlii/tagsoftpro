<?php

namespace App\DataTables\Dashboard\Admin;

use App\DataTables\Base\BaseDataTable;
use App\Models\PermissionGroup;
use App\Http\Middleware\EnsureOwner;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Utilities\Request as DataTableRequest;

class PermissionGroupDataTable extends BaseDataTable
{
    protected $customFilters = [];
    protected $showTrashed = false;

    public function __construct(DataTableRequest $request)
    {
        parent::__construct(new PermissionGroup);
        $this->request = $request;
        $this->showTrashed = $request->get('show_trashed', false) === 'true';
    }

    public function dataTable($query): EloquentDataTable
    {
        $dataTable = (new EloquentDataTable($query))
            ->addColumn('checkbox', function (PermissionGroup $permissionGroup) {
                return '<input type="checkbox" class="form-check-input row-checkbox" value="' . $permissionGroup->id . '">';
            })
            ->addColumn('action', function (PermissionGroup $permissionGroup) {
                if (!$permissionGroup) 
                    return '';
                return view('dashboard.admin.permission.permission_groups.btn.actions', compact('permissionGroup'));
            })
            ->editColumn('name', function (PermissionGroup $permissionGroup) {
                return $permissionGroup->getTranslatedName();
            })
            ->editColumn('description', function (PermissionGroup $permissionGroup) {
                return $permissionGroup->getTranslatedDescription() ?? '-';
            })
            ->editColumn('icon', function (PermissionGroup $permissionGroup) {
                return $permissionGroup->icon ? '<i class="' . $permissionGroup->icon . ' fs-3"></i>' : '-';
            })
            ->editColumn('status', function (PermissionGroup $permissionGroup) {
                return $this->renderStatusBadge($permissionGroup);
            })
            ->editColumn('created_at', fn(PermissionGroup $permissionGroup) => $this->formatTranslatedDate($permissionGroup->created_at))
            ->editColumn('updated_at', fn(PermissionGroup $permissionGroup) => $this->formatTranslatedDate($permissionGroup->updated_at))
            ->filterColumn('name', function ($query, $keyword) {
                if ($keyword) {
                    $query->whereHas('translations', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                }
            })
            ->filterColumn('status', function ($query, $keyword) {
                if ($keyword !== '') {
                    $query->where('status', $keyword);
                }
            })
            ->addIndexColumn()
            ->rawColumns(['checkbox', 'action', 'icon', 'status', 'created_at', 'updated_at']);

        // ─── Company column for owners ──────────────────────
        if (EnsureOwner::check()) {
            $dataTable->editColumn('company', function (PermissionGroup $permissionGroup) {
                return $permissionGroup->company?->name ?? '-';
            });
        }

        return $dataTable;
    }

    private function renderStatusBadge(PermissionGroup $permissionGroup): string
    {
        if ($this->showTrashed) {
            return '<span class="badge-status">' . $permissionGroup->status->badge() . '</span>';
        }

        return '
            <div class="gap-1 d-flex flex-column align-items-center">
                <span class="badge-status">' . $permissionGroup->status->badge() . '</span>
                <button type="button"
                        class="btn btn-sm btn-outline-secondary toggle-status"
                        data-id="' . $permissionGroup->id . '"
                        data-route="' . route('admin.permission_groups.toggleStatus', $permissionGroup->id) . '"
                        title="تغيير الحالة">
                    <i class="ti ti-refresh"></i>
                </button>
            </div>
        ';
    }

    public function query(): QueryBuilder
    {
        $query = PermissionGroup::query()->with(['translations']);

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

    protected function getParameters()
    {
        $params = parent::getParameters();
        $bulkButtons = [];

        if ($this->showTrashed) {
            $bulkButtons = [
                [
                    'text' => '<i class="ti ti-refresh me-2 text-success"></i> ' . trans('dashboard/permission_groups.bulk_restore'),
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
                    'text' => '<i class="ti ti-trash-off me-2 text-danger"></i> ' . trans('dashboard/permission_groups.bulk_force_delete'),
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
                    'text' => '<i class="ti ti-exchange me-2 text-primary"></i> ' . trans('dashboard/permission_groups.bulk_change_status'),
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
            'text' => '<i class="ti ti-settings me-1"></i> ' . trans('dashboard/permission_groups.bulk_actions') . ' <span class="badge bg-light text-dark ms-1" id="selectedCount">0</span>',
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
            'active' => trans('dashboard/permission_groups.status_active'),
            'inactive' => trans('dashboard/permission_groups.status_inactive'),
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
            var nameInput = $(\'<input type="text" class="mt-1 form-control form-control-sm" placeholder="' . trans('dashboard/permission_groups.name') . '...">\');
            nameHeader.append(nameInput);

            nameInput.on("keyup change", function(e) {
                e.stopPropagation();
                api.column(nameColIndex).search(this.value).draw();
            });

            var statusColIndex = 5;
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
            ['name' => 'name', 'data' => 'name', 'title' => trans('dashboard/permission_groups.name'), 'className' => 'text-center', 'searchable' => true, 'orderable' => true],
            ['name' => 'description', 'data' => 'description', 'title' => trans('dashboard/permission_groups.description'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'icon', 'data' => 'icon', 'title' => trans('dashboard/permission_groups.icon'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'status', 'data' => 'status', 'title' => trans('dashboard/permission_groups.status'), 'className' => 'text-center', 'orderable' => false, 'searchable' => true],
        ];

        if (EnsureOwner::check()) {
            $columns[] = ['name' => 'company', 'data' => 'company', 'title' => trans('dashboard/permission_groups.company'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false];
        }

        $columns[] = ['name' => 'created_at', 'data' => 'created_at', 'title' => trans('dashboard/general.created_at'), 'className' => 'text-center', 'searchable' => false];
        $columns[] = ['name' => 'updated_at', 'data' => 'updated_at', 'title' => trans('dashboard/general.updated_at'), 'className' => 'text-center', 'searchable' => false];
        $columns[] = ['name' => 'action', 'data' => 'action', 'title' => trans('dashboard/general.actions'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false];

        return $columns;
    }
}