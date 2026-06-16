@php
$locales = array_keys(config('laravellocalization.supportedLocales'));
@endphp

{{-- ============================= --}}
{{-- EDIT MODAL --}}
{{-- ============================= --}}
<div class="modal fade" id="editModuleModal" tabindex="-1" aria-labelledby="editModuleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="editModuleModalLabel">
                    <i class="ti ti-edit me-2"></i>
                    {{ trans('dashboard/modules.edit') }}
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
                <form id="editModuleForm" class="d-none">
                    @csrf
                    <input type="hidden" id="editModuleId">

                    <div class="row g-3">

                        {{-- Translations as Tabs --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                {{ trans('dashboard/modules.name') }} <span class="text-danger">*</span>
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
                                            placeholder="{{ trans('dashboard/modules.name') }} ({{ strtoupper($locale) }})">
                                    </div>
                                    <div>
                                        <textarea class="form-control" name="description[{{ $locale }}]"
                                            id="edit_description_{{ $locale }}" rows="2"
                                            placeholder="{{ trans('dashboard/modules.description') }} ({{ strtoupper($locale) }})"></textarea>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Company --}}
                        @ownerOnly
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/modules.company') }}</label>
                                <select class="form-select" name="company_id" id="edit_company_id">
                                    <option value="">-- {{ trans('dashboard/general.select') }} --</option>
                                    @foreach(\App\Models\Company::whereStatus('active')->get(['id','name']) as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endOwnerOnly

                        {{-- Project Types (Checkboxes) --}}
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/modules.project_types') }}</label>
                                <div class="row">
                                    @foreach(\App\Models\ProjectType::active()->with('translations')->get() as $projectType)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="project_types[]"
                                                value="{{ $projectType->id }}" id="edit_project_type_{{ $projectType->id }}">
                                            <label class="form-check-label" for="edit_project_type_{{ $projectType->id }}">
                                                {{ $projectType->getTranslatedName() }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-12">
                            <label class="form-label d-block">{{ trans('dashboard/modules.status') }}</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" id="edit_status"
                                    value="1">
                                <label class="form-check-label" for="edit_status">
                                    {{ trans('dashboard/general.active') }}
                                </label>
                            </div>
                        </div>

                    </div>
                </form>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ trans('dashboard/general.cancel') }}
                </button>
                <button type="button" class="btn btn-primary" id="saveEditModule">
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
<div class="modal fade" id="deleteModuleModal" tabindex="-1" aria-labelledby="deleteModuleModalLabel"
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
                <input type="hidden" id="deleteModuleId">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    {{ trans('dashboard/general.cancel') }}
                </button>
                <button type="button" class="btn btn-danger px-4" id="confirmDeleteModule">
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


{{-- ============================= --}}
{{-- PROJECT TYPES MODAL --}}
{{-- ============================= --}}
<div class="modal fade" id="projectTypesModal" tabindex="-1" aria-labelledby="projectTypesModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="projectTypesModalLabel">
                    <i class="ti ti-folder me-2"></i>
                    <span id="projectTypesModalTitle">{{ trans('dashboard/modules.project_types') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="projectTypesList" class="d-flex flex-wrap gap-2">
                    <!-- Dynamic content -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ trans('dashboard/general.close') }}
                </button>
            </div>
        </div>
    </div>
</div>