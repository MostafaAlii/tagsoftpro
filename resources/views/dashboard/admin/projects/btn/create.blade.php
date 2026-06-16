<div class="modal fade" id="createProjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('dashboard/projects.create') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.projects.store') }}" method="POST" id="createForm"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    {{-- Tabs --}}
                    <ul class="mb-3 nav nav-tabs">
                        @foreach($locales as $locale)
                        <li class="nav-item">
                            <button type="button" class="nav-link {{ $locale === $currentLocale ? 'active' : '' }}"
                                data-bs-toggle="tab" data-bs-target="#create-tab-{{ $locale }}">
                                {{ config('laravellocalization.supportedLocales.' . $locale . '.native') }}
                            </button>
                        </li>
                        @endforeach
                    </ul>

                    {{-- Tab Content --}}
                    <div class="mb-3 tab-content">
                        @foreach($locales as $locale)
                        <div class="tab-pane fade {{ $locale === $currentLocale ? 'show active' : '' }}"
                            id="create-tab-{{ $locale }}">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/projects.name') }} ({{ strtoupper($locale)
                                    }}) <span class="text-danger">*</span></label>
                                <input type="text" name="name[{{ $locale }}]" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/projects.description') }} ({{
                                    strtoupper($locale) }})</label>
                                <textarea name="description[{{ $locale }}]" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Status --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ trans('dashboard/projects.status') }} <span
                                    class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="draft">{{ trans('dashboard/projects.status_draft') }}</option>
                                <option value="active">{{ trans('dashboard/projects.status_active') }}</option>
                                <option value="published">{{ trans('dashboard/projects.status_published') }}</option>
                                <option value="inactive">{{ trans('dashboard/projects.status_inactive') }}</option>
                            </select>
                        </div>

                        @ownerOnly
                        <div class="col-md-6">
                            <label class="form-label">{{ trans('dashboard/projects.company') }}</label>
                            <select name="company_id" class="form-select">
                                <option value="">{{ trans('dashboard/projects.select_company') }}</option>
                                @foreach($companies ?? [] as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endOwnerOnly
                    </div>

                    {{-- Project Types --}}
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">{{ trans('dashboard/projects.project_types') }}</label>
                            <div class="row">
                                @foreach($projectTypes ?? [] as $projectType)
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="project_types[]"
                                            value="{{ $projectType->id }}"
                                            id="create_project_type_{{ $projectType->id }}">
                                        <label class="form-check-label"
                                            for="create_project_type_{{ $projectType->id }}">
                                            {{ $projectType->getTranslatedName() }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Modules --}}
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">{{ trans('dashboard/projects.modules') }}</label>
                            <div class="row">
                                @foreach($modules ?? [] as $module)
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="modules[]"
                                            value="{{ $module->id }}" id="create_module_{{ $module->id }}">
                                        <label class="form-check-label" for="create_module_{{ $module->id }}">
                                            {{ $module->getTranslatedName() }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Image --}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="p-3 text-center border rounded">
                                <label for="projectInput" class="form-label fw-bold">{{
                                    trans('dashboard/projects.image') }}</label>
                                <input class="form-control" type="file" name="project" id="projectInput"
                                    accept="image/*">
                                <div class="mt-2">
                                    <img id="projectPreview" src="" alt=""
                                        style="display: none; width: 150px; height: 150px; object-fit: cover; border-radius: 8px; border: 2px solid #e0e0e0; cursor: pointer;"
                                        onclick="window.openImageModal(this.src, '{{ trans('dashboard/projects.image') }}')">
                                    <span id="imagePlaceholder" class="text-muted">{{
                                        trans('dashboard/projects.no_image') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{
                        trans('dashboard/general.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ trans('dashboard/general.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>