<?php

namespace App\DataTables\Dashboard\Admin;

use App\DataTables\Base\BaseDataTable;
use App\Models\MenuItem;
use App\Http\Middleware\EnsureOwner;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Utilities\Request as DataTableRequest;

class MenuItemDataTable extends BaseDataTable
{
    protected $customFilters = [];
    protected $showTrashed = false;

    public function __construct(DataTableRequest $request)
    {
        parent::__construct(new MenuItem);
        $this->request = $request;
        $this->showTrashed = $request->get('show_trashed', false) === 'true';
    }

    public function dataTable($query): EloquentDataTable
    {
        $dataTable = (new EloquentDataTable($query))
            ->addColumn('checkbox', function (MenuItem $menuItem) {
                return '<input type="checkbox" class="form-check-input row-checkbox" value="' . $menuItem->id . '">';
            })
            ->addColumn('action', function (MenuItem $menuItem) {
                return view('dashboard.admin.menu_items.btn.actions', compact('menuItem'));
            })
            ->editColumn('title', function (MenuItem $menuItem) {
                return $menuItem->getTranslatedTitle();
            })
            ->editColumn('description', function (MenuItem $menuItem) {
                return $menuItem->getTranslatedDescription() ?? '-';
            })
            ->editColumn('type', function (MenuItem $menuItem) {
                return $menuItem->type->badge();
            })
            ->editColumn('icon', function (MenuItem $menuItem) {
                return $menuItem->icon ? '<i class="' . $menuItem->icon . ' fs-3"></i>' : '-';
            })
            ->editColumn('link', function (MenuItem $menuItem) {
                $link = $menuItem->getLink();
                return $link ? '<code>' . $link . '</code>' : '-';
            })
            ->editColumn('status', function (MenuItem $menuItem) {
                return $this->renderStatusBadge($menuItem);
            })
            ->editColumn('visible_from', function (MenuItem $menuItem) {
                return $menuItem->visible_from ? $this->formatTranslatedDate($menuItem->visible_from) : '-';
            })
            ->editColumn('visible_until', function (MenuItem $menuItem) {
                return $menuItem->visible_until ? $this->formatTranslatedDate($menuItem->visible_until) : '-';
            })
            ->editColumn('created_at', fn(MenuItem $menuItem) => $this->formatTranslatedDate($menuItem->created_at))
            ->editColumn('updated_at', fn(MenuItem $menuItem) => $this->formatTranslatedDate($menuItem->updated_at))
            ->filterColumn('title', function ($query, $keyword) {
                if ($keyword) {
                    $query->whereHas('translations', function ($q) use ($keyword) {
                        $q->where('title', 'like', "%{$keyword}%");
                    });
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
            })
            ->addIndexColumn()
            ->rawColumns(['checkbox', 'action', 'icon', 'type', 'status', 'link', 'created_at', 'updated_at']);

        if (EnsureOwner::check()) {
            $dataTable->editColumn('company', function (MenuItem $menuItem) {
                return $menuItem->company?->name ?? '-';
            });
        }

        return $dataTable;
    }

    private function renderStatusBadge(MenuItem $menuItem): string
    {
        if ($this->showTrashed) {
            return '<span class="badge-status">' . $menuItem->status->badge() . '</span>';
        }

        return '
            <div class="gap-1 d-flex flex-column align-items-center">
                <span class="badge-status">' . $menuItem->status->badge() . '</span>
                <button type="button"
                        class="btn btn-sm btn-outline-secondary toggle-status"
                        data-id="' . $menuItem->id . '"
                        data-route="' . route('admin.menu_items.toggleStatus', $menuItem->id) . '"
                        title="تغيير الحالة">
                    <i class="ti ti-refresh"></i>
                </button>
            </div>
        ';
    }

    public function query(): QueryBuilder
    {
        $query = MenuItem::query()->with(['translations']);

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
                    'text' => '<i class="ti ti-refresh me-2 text-success"></i> ' . trans('dashboard/menu_items.bulk_restore'),
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
                    'text' => '<i class="ti ti-trash-off me-2 text-danger"></i> ' . trans('dashboard/menu_items.bulk_force_delete'),
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
                    'text' => '<i class="ti ti-exchange me-2 text-primary"></i> ' . trans('dashboard/menu_items.bulk_change_status'),
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
            'text' => '<i class="ti ti-settings me-1"></i> ' . trans('dashboard/menu_items.bulk_actions') . ' <span class="badge bg-light text-dark ms-1" id="selectedCount">0</span>',
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

        $types = [
            'link' => trans('dashboard/menu_items.type_link'),
            'dropdown' => trans('dashboard/menu_items.type_dropdown'),
            'header' => trans('dashboard/menu_items.type_header'),
            'divider' => trans('dashboard/menu_items.type_divider'),
        ];

        $statuses = [
            'active' => trans('dashboard/menu_items.status_active'),
            'inactive' => trans('dashboard/menu_items.status_inactive'),
        ];

        $typeOptions = '';
        foreach ($types as $value => $label) {
            $typeOptions .= '<option value="' . $value . '">' . $label . '</option>';
        }

        $statusOptions = '';
        foreach ($statuses as $value => $label) {
            $statusOptions .= '<option value="' . $value . '">' . $label . '</option>';
        }

        return '
        function() {
            var api = this.api();

            var titleColIndex = 2;
            var titleHeader = $(api.column(titleColIndex).header());
            var titleInput = $(\'<input type="text" class="mt-1 form-control form-control-sm" placeholder="' . trans('dashboard/menu_items.title') . '...">\');
            titleHeader.append(titleInput);

            titleInput.on("keyup change", function(e) {
                e.stopPropagation();
                api.column(titleColIndex).search(this.value).draw();
            });

            var typeColIndex = 4;
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

            var statusColIndex = 7;
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
            ['name' => 'title', 'data' => 'title', 'title' => trans('dashboard/menu_items.title'), 'className' => 'text-center', 'searchable' => true, 'orderable' => false],
            ['name' => 'description', 'data' => 'description', 'title' => trans('dashboard/menu_items.description'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'type', 'data' => 'type', 'title' => trans('dashboard/menu_items.type'), 'className' => 'text-center', 'orderable' => false, 'searchable' => true],
            ['name' => 'icon', 'data' => 'icon', 'title' => trans('dashboard/menu_items.icon'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'link', 'data' => 'link', 'title' => trans('dashboard/menu_items.link'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'status', 'data' => 'status', 'title' => trans('dashboard/menu_items.status'), 'className' => 'text-center', 'orderable' => false, 'searchable' => true],
        ];

        if (EnsureOwner::check()) {
            $columns[] = ['name' => 'company', 'data' => 'company', 'title' => trans('dashboard/menu_items.company'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false];
        }

        $columns[] = ['name' => 'visible_from', 'data' => 'visible_from', 'title' => trans('dashboard/menu_items.visible_from'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false];
        $columns[] = ['name' => 'visible_until', 'data' => 'visible_until', 'title' => trans('dashboard/menu_items.visible_until'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false];
        $columns[] = ['name' => 'created_at', 'data' => 'created_at', 'title' => trans('dashboard/general.created_at'), 'className' => 'text-center', 'searchable' => false];
        $columns[] = ['name' => 'updated_at', 'data' => 'updated_at', 'title' => trans('dashboard/general.updated_at'), 'className' => 'text-center', 'searchable' => false];
        $columns[] = ['name' => 'action', 'data' => 'action', 'title' => trans('dashboard/general.actions'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false];

        return $columns;
    }
}