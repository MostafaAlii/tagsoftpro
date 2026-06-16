<?php

namespace App\DataTables\Dashboard\Admin;

use App\DataTables\Base\BaseDataTable;
use App\Models\Project;
use App\Enums\Project\ProjectStatus;
use App\Http\Middleware\EnsureOwner;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Utilities\Request as DataTableRequest;
use Illuminate\Support\Str;
use App\Models\Concerns\UploadMedia;
class ProjectDataTable extends BaseDataTable
{
    use UploadMedia;
    protected $customFilters = [];

    public function __construct(DataTableRequest $request)
    {
        parent::__construct(new Project);
        $this->request = $request;
    }

    public function dataTable($query): EloquentDataTable
    {
        $dataTable = (new EloquentDataTable($query))
            ->addColumn('action', function (Project $project) {
                return view('dashboard.admin.projects.btn.actions', compact('project'));
            })
            ->addColumn('image', function (Project $project) {
                return $this->renderImage($project);
            })
            ->editColumn('name', function (Project $project) {
                return $project->getTranslatedName();
            })
            ->editColumn('description', function (Project $project) {
                $desc = $project->getTranslatedDescription();
                return $desc ? Str::limit($desc, 50) : '-';
            })
            ->editColumn('project_types', function (Project $project) {
                return $project->getProjectTypesNames();
            })
            ->editColumn('modules', function (Project $project) {
                return $project->getModulesNames();
            })
            ->editColumn('status', function (Project $project) {
                return $this->renderStatusBadge($project);
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
                    $query->where('status', $keyword);
                }
            });

        // ─── Company column for owners ──────────────────────
        if (EnsureOwner::check()) {
            $dataTable->editColumn('company', function (Project $project) {
                return $project->company?->name ?? '-';
            });
        }

        $dataTable
            ->editColumn('created_at', fn(Project $project) => $this->formatTranslatedDate($project->created_at))
            ->editColumn('updated_at', fn(Project $project) => $this->formatTranslatedDate($project->updated_at))
            ->addIndexColumn()
            ->rawColumns(['action', 'image', 'status', 'created_at', 'updated_at']);

        return $dataTable;
    }

    /**
     * Render image column
     */
    private function renderImage(Project $project): string
    {
        // ✅ استخدم getMediaUrl من الـ Trait
        $imageUrl = $this->getMediaUrl('project', $project, null, 'media', 'project');

        if ($imageUrl) {
            return '
                <img src="' . $imageUrl . '" 
                     alt="' . e($project->getTranslatedName()) . '" 
                     width="50" 
                     height="50" 
                     style="object-fit: cover; border-radius: 4px; cursor: pointer;"
                     onclick="openImageModal(\'' . $imageUrl . '\', \'' . e($project->getTranslatedName()) . '\')">
            ';
        }

        return '<span class="text-muted">-</span>';
    }

    /**
     * Render status badge with toggle button
     */
    private function renderStatusBadge(Project $project): string
    {
        return '
            <div class="gap-1 d-flex flex-column align-items-center">
                <span class="badge-status">' . $project->status->badge() . '</span>
                <button type="button" 
                        class="btn btn-sm btn-outline-secondary toggle-status"
                        data-id="' . $project->id . '"
                        data-route="' . route('admin.projects.toggleStatus', $project->id) . '"
                        title="تغيير الحالة">
                    <i class="ti ti-refresh"></i>
                </button>
            </div>
        ';
    }

    public function query(): QueryBuilder
    {
        $query = Project::query()
            ->with(['translations', 'projectTypes.translations', 'modules.translations', 'media',])
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
        $activeText = trans('dashboard/projects.status_active');
        $inactiveText = trans('dashboard/projects.status_inactive');
        $publishedText = trans('dashboard/projects.status_published');
        $draftText = trans('dashboard/projects.status_draft');

        return '
        function() {
            var api = this.api();

            // ─── Search box بالاسم ──────────────────────────
            var nameColIndex = 2;
            var nameHeader = $(api.column(nameColIndex).header());
            var nameInput = $(\'<input type="text" class="mt-1 form-control form-control-sm" placeholder="' . trans('dashboard/projects.name') . '...">\');
            nameHeader.append(nameInput);

            nameInput.on("keyup change", function(e) {
                e.stopPropagation();
                api.column(nameColIndex).search(this.value).draw();
            });

            // ─── فلتر الحالة (status) ──────────────────────
            var statusColIndex = 6;
            var statusHeader = $(api.column(statusColIndex).header());
            var statusSelect = $(\'<select class="mt-1 form-select form-select-sm">\' +
                \'<option value="">' . $allText . '</option>\' +
                \'<option value="active">' . $activeText . '</option>\' +
                \'<option value="inactive">' . $inactiveText . '</option>\' +
                \'<option value="published">' . $publishedText . '</option>\' +
                \'<option value="draft">' . $draftText . '</option>\' +
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
            ['name' => 'image', 'data' => 'image', 'title' => trans('dashboard/projects.image'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'name', 'data' => 'name', 'title' => trans('dashboard/projects.name'), 'className' => 'text-center', 'searchable' => true, 'orderable' => true],
            ['name' => 'description', 'data' => 'description', 'title' => trans('dashboard/projects.description'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'project_types', 'data' => 'project_types', 'title' => trans('dashboard/projects.project_types'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'modules', 'data' => 'modules', 'title' => trans('dashboard/projects.modules'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false],
            ['name' => 'status', 'data' => 'status', 'title' => trans('dashboard/projects.status'), 'className' => 'text-center', 'orderable' => false, 'searchable' => true],
        ];

        if (EnsureOwner::check()) {
            $columns[] = ['name' => 'company', 'data' => 'company', 'title' => trans('dashboard/projects.company'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false];
        }

        $columns[] = ['name' => 'created_at', 'data' => 'created_at', 'title' => trans('dashboard/general.created_at'), 'className' => 'text-center', 'searchable' => false];
        $columns[] = ['name' => 'updated_at', 'data' => 'updated_at', 'title' => trans('dashboard/general.updated_at'), 'className' => 'text-center', 'searchable' => false];
        $columns[] = ['name' => 'action', 'data' => 'action', 'title' => trans('dashboard/general.actions'), 'className' => 'text-center', 'orderable' => false, 'searchable' => false];

        return $columns;
    }
}