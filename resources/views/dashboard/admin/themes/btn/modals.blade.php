{{-- ============================= --}}
{{-- EDIT MODAL --}}
{{-- ============================= --}}
<div class="modal fade" id="editThemeModal" tabindex="-1" aria-labelledby="editThemeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="editThemeModalLabel">
                    <i class="ti ti-edit me-2"></i>
                    {{ trans('dashboard/themes.edit') }}
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
                <form id="editThemeForm" class="d-none">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editThemeId">

                    <div class="row g-3">
                        {{-- Name --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/themes.name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="edit_name">
                            </div>
                        </div>

                        {{-- Code --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/themes.code') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="code" id="edit_code">
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/themes.description') }}</label>
                                <textarea class="form-control" name="description" id="edit_description" rows="2"></textarea>
                            </div>
                        </div>

                        {{-- Paid Type --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/themes.paid_type') }} <span class="text-danger">*</span></label>
                                <select class="form-select" name="paid_type" id="edit_paid_type">
                                    <option value="free">{{ trans('dashboard/themes.paid_type_free') }}</option>
                                    <option value="paid">{{ trans('dashboard/themes.paid_type_paid') }}</option>
                                </select>
                            </div>
                        </div>

                        {{-- Price --}}
                        <div class="col-md-6">
                            <div class="mb-3" id="edit_price_wrapper">
                                <label class="form-label">{{ trans('dashboard/themes.price') }}</label>
                                <input type="number" class="form-control" name="price" id="edit_price" step="0.01" min="0">
                            </div>
                        </div>

                        @ownerOnly
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/themes.company') }}</label>
                                <select class="form-select" name="company_id" id="edit_company_id">
                                    <option value="">-- {{ trans('dashboard/general.select') }} --</option>
                                    @foreach(\App\Models\Company::whereStatus('active')->get(['id','name']) as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endOwnerOnly

                        {{-- ✅ Project Types with Status & Default --}}
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <strong>{{ trans('dashboard/themes.project_types_config') }}</strong>
                                </div>
                                <div class="card-body" id="editProjectTypesContainer">
                                    {{-- سيتم تعبئتها بواسطة JavaScript --}}
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
                <button type="button" class="btn btn-primary" id="saveEditTheme">
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
<div class="modal fade" id="deleteThemeModal" tabindex="-1" aria-labelledby="deleteThemeModalLabel" aria-hidden="true">
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
                <input type="hidden" id="deleteThemeId">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    {{ trans('dashboard/general.cancel') }}
                </button>
                <button type="button" class="btn btn-danger px-4" id="confirmDeleteTheme">
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
{{-- DEFAULT STATUS MODAL --}}
{{-- ============================= --}}
<div class="modal fade" id="defaultStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-star me-2 text-primary"></i>
                    {{ trans('dashboard/themes.default_status_for') }}: <span id="defaultModalThemeName"
                        class="fw-bold"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="defaultForm">
                @csrf
                <div class="modal-body" id="defaultStatusModalBody">
                    <div class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ trans('dashboard/general.close') }}
                    </button>
                    <button type="button" class="btn btn-primary" id="saveDefaultBtn">
                        <span class="indicator-label">
                            <i class="ti ti-device-floppy me-1"></i>
                            {{ trans('dashboard/general.save') }}
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================= --}}
{{-- STATUS MODAL --}}
{{-- ============================= --}}
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-check-circle me-2 text-success"></i>
                    {{ trans('dashboard/themes.active_status_for') }}: <span id="statusModalThemeName"
                        class="fw-bold"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusForm">
                @csrf
                <div class="modal-body" id="statusModalBody">
                    <div class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ trans('dashboard/general.close') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('edit_paid_type')?.addEventListener('change', function() {
        const priceWrapper = document.getElementById('edit_price_wrapper');
        if (this.value === 'paid') {
            priceWrapper.style.display = 'block';
        } else {
            priceWrapper.style.display = 'none';
        }
    });
</script>
