@php
$locales = array_keys(config('laravellocalization.supportedLocales'));
@endphp

{{-- ============================= --}}
{{-- EDIT MODAL --}}
{{-- ============================= --}}
<div class="modal fade" id="editProjectModal" tabindex="-1" aria-labelledby="editProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="editProjectModalLabel">
                    <i class="ti ti-edit me-2"></i>
                    {{ trans('dashboard/projects.edit') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                {{-- Loader --}}
                <div id="editModalLoader" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                {{-- Form --}}
                <form id="editProjectForm" class="d-none" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="editProjectId">

                    <div class="row g-3">

                        {{-- Translations as Tabs --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                {{ trans('dashboard/projects.name') }} <span class="text-danger">*</span>
                            </label>

                            <ul class="nav nav-tabs nav-tabs-bordered mb-3" id="editLangTabs" role="tablist">
                                @foreach($locales as $index => $locale)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $index === 0 ? 'active' : '' }}"
                                        id="edit-tab-{{ $locale }}" data-bs-toggle="tab"
                                        data-bs-target="#edit-pane-{{ $locale }}" type="button" role="tab">
                                        {{ strtoupper($locale) }}
                                    </button>
                                </li>
                                @endforeach
                            </ul>

                            <div class="tab-content" id="editLangTabsContent">
                                @foreach($locales as $index => $locale)
                                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                                    id="edit-pane-{{ $locale }}" role="tabpanel">
                                    <div class="mb-2">
                                        <input type="text" class="form-control" name="name[{{ $locale }}]"
                                            id="edit_name_{{ $locale }}"
                                            placeholder="{{ trans('dashboard/projects.name') }} ({{ strtoupper($locale) }})">
                                    </div>
                                    <div>
                                        <textarea class="form-control" name="description[{{ $locale }}]"
                                            id="edit_description_{{ $locale }}" rows="2"
                                            placeholder="{{ trans('dashboard/projects.description') }} ({{ strtoupper($locale) }})"></textarea>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
                            <label class="form-label">{{ trans('dashboard/projects.status') }}</label>
                            <select class="form-select" name="status" id="edit_status">
                                <option value="draft">{{ trans('dashboard/projects.status_draft') }}</option>
                                <option value="active">{{ trans('dashboard/projects.status_active') }}</option>
                                <option value="published">{{ trans('dashboard/projects.status_published') }}</option>
                                <option value="inactive">{{ trans('dashboard/projects.status_inactive') }}</option>
                            </select>
                        </div>

                        {{-- Company --}}
                        @ownerOnly
                        <div class="col-md-6">
                            <label class="form-label">{{ trans('dashboard/projects.company') }}</label>
                            <select class="form-select" name="company_id" id="edit_company_id">
                                <option value="">-- {{ trans('dashboard/general.select') }} --</option>
                                @foreach(\App\Models\Company::whereStatus('active')->get(['id','name']) as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endOwnerOnly

                        {{-- Project Types --}}
                        <div class="col-md-12">
                            <label class="form-label">{{ trans('dashboard/projects.project_types') }}</label>
                            <div class="row">
                                @foreach(\App\Models\ProjectType::active()->with('translations')->get() as $projectType)
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="project_types[]"
                                            value="{{ $projectType->id }}"
                                            id="edit_project_type_{{ $projectType->id }}">
                                        <label class="form-check-label" for="edit_project_type_{{ $projectType->id }}">
                                            {{ $projectType->getTranslatedName() }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Modules --}}
                        <div class="col-md-12">
                            <label class="form-label">{{ trans('dashboard/projects.modules') }}</label>
                            <div class="row">
                                @foreach(\App\Models\Module::active()->with('translations')->get() as $module)
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="modules[]"
                                            value="{{ $module->id }}" id="edit_module_{{ $module->id }}">
                                        <label class="form-check-label" for="edit_module_{{ $module->id }}">
                                            {{ $module->getTranslatedName() }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Image --}}
                        <div class="col-md-12">
                            <div class="p-3 text-center border rounded">
                                <label for="editProjectInput" class="form-label fw-bold">{{ trans('dashboard/projects.image') }}</label>
                                <input class="form-control" type="file" name="project" id="editProjectInput" accept="image/*">
                                <div class="mt-2">
                                    <img id="editProjectPreview" src="" alt=""
                                        style="display: none; width: 150px; height: 150px; object-fit: cover; border-radius: 8px; border: 2px solid #e0e0e0; cursor: pointer;"
                                        onclick="window.openImageModal(this.src, '{{ trans('dashboard/projects.image') }}')">
                                    <span id="editImagePlaceholder" class="text-muted">{{ trans('dashboard/projects.no_image') }}</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ trans('dashboard/general.cancel') }}
                </button>
                <button type="button" class="btn btn-primary" id="saveEditProject">
                    <span class="indicator-label">
                        <i class="ti ti-check me-1"></i>
                        {{ trans('dashboard/general.save') }}
                    </span>
                    <span class="indicator-progress d-none">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        {{ trans('dashboard/general.loading') }}
                    </span>
                </button>
            </div>

        </div>
    </div>
</div>

{{-- ============================= --}}
{{-- DELETE MODAL --}}
{{-- ============================= --}}
<div class="modal fade" id="deleteProjectModal" tabindex="-1" aria-labelledby="deleteProjectModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body text-center px-4 pb-2">
                <div class="mb-3">
                    <span
                        class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center"
                        style="width:64px;height:64px;">
                        <i class="ti ti-trash fs-2"></i>
                    </span>
                </div>
                <h5 class="fw-bold mb-1">{{ trans('dashboard/general.delete_confirm_title') }}</h5>
                <p class="text-muted mb-0">{{ trans('dashboard/general.delete_confirm_text') }}</p>
            </div>

            <div class="modal-footer justify-content-center border-0 pt-2">
                <input type="hidden" id="deleteProjectId">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    {{ trans('dashboard/general.cancel') }}
                </button>
                <button type="button" class="btn btn-danger px-4" id="confirmDeleteProject">
                    <span class="indicator-label">
                        <i class="ti ti-trash me-1"></i>
                        {{ trans('dashboard/general.delete') }}
                    </span>
                    <span class="indicator-progress d-none">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        {{ trans('dashboard/general.loading') }}
                    </span>
                </button>
            </div>

        </div>
    </div>
</div>