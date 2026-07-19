{{-- Edit Modal --}}
<div class="modal fade" id="editVendorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-edit me-2"></i>
                    {{ trans('vendor::vendors.edit') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="editModalLoader" class="py-5 text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <form id="editVendorForm" class="d-none">
                    @csrf
                    <input type="hidden" id="modal_edit_vendor_id">
                    <div class="row g-3">
                        {{-- Name --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('vendor::vendors.name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="modal_edit_name"
                                    placeholder="{{ trans('vendor::vendors.name_placeholder') }}" required>
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('vendor::vendors.email') }} <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" id="modal_edit_email" placeholder="{{ trans('vendor::vendors.email_placeholder') }}" required>
                            </div>
                        </div>

                        {{-- Phone --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('vendor::vendors.phone') }}</label>
                                <input type="text" class="form-control" name="phone" id="modal_edit_phone"
                                    placeholder="{{ trans('vendor::vendors.phone_placeholder') }}">
                            </div>
                        </div>

                        {{-- Password --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('vendor::vendors.password') }}</label>
                                <input type="password" class="form-control" name="password" id="modal_edit_password"
                                    placeholder="{{ trans('dashboard/vendors.password_leave_blank') }}">
                                <small class="text-muted">{{ trans('vendor::vendors.password_leave_blank') }}</small>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('vendor::vendors.status') }} <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="status" id="modal_edit_status" required>
                                    <option value="active">{{ trans('vendor::vendors.status_active') }}</option>
                                    <option value="inactive">{{ trans('vendor::vendors.status_inactive') }}</option>
                                    <option value="pending">{{ trans('vendor::vendors.status_pending') }}</option>
                                    <option value="suspended">{{ trans('vendor::vendors.status_suspended') }}</option>
                                </select>
                            </div>
                        </div>

                        {{-- Type --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('vendor::vendors.type') }}</label>
                                <select class="form-select" name="type" id="modal_edit_type">
                                    <option value="">{{ trans('dashboard/general.select') }}</option>
                                    <option value="individual">{{ trans('vendor::vendors.type_individual') }}</option>
                                    <option value="company">{{ trans('vendor::vendors.type_company') }}</option>
                                    <option value="government">{{ trans('vendor::vendors.type_government') }}</option>
                                </select>
                            </div>
                        </div>

                        {{-- Date --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('vendor::vendors.date') }}</label>
                                <input type="date" class="form-control" name="date" id="modal_edit_date">
                            </div>
                        </div>

                        {{-- Company --}}
                        @ownerOnly
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('vendor::vendors.company') }}</label>
                                <select class="form-select" name="company_id" id="modal_edit_company_id">
                                    <option value="">{{ trans('dashboard/general.select') }}</option>
                                    @foreach($companies ?? [] as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endOwnerOnly

                        {{-- Department --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('vendor::vendors.department') }}</label>
                                <select class="form-select" name="department_id" id="modal_edit_department_id">
                                    <option value="">{{ trans('dashboard/general.select') }}</option>
                                    @foreach($departments ?? [] as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ trans('dashboard/general.cancel') }}
                </button>
                <button type="button" class="btn btn-primary" id="modal_save_edit_vendor">
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

{{-- Delete Modal --}}
<div class="modal fade" id="deleteVendorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="ti ti-alert-triangle me-2"></i>
                    {{ trans('dashboard/general.delete') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{ trans('vendor::vendors.delete_confirm') }}</p>
                <input type="hidden" id="deleteVendorId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ trans('dashboard/general.cancel') }}
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteVendor">
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

{{-- Bulk Action Modal --}}
<div class="modal fade" id="bulkActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkActionModalTitle">{{ trans('vendor::vendors.bulk_actions') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="bulkActionMessage"></p>
                <div id="bulkStatusDropdown" style="display: none;">
                    <label class="form-label">{{ trans('vendor::vendors.select_status') }}</label>
                    <select class="form-select" id="bulkStatusSelect">
                        <option value="active">{{ trans('vendor::vendors.status_active') }}</option>
                        <option value="inactive">{{ trans('vendor::vendors.status_inactive') }}</option>
                        <option value="pending">{{ trans('vendor::vendors.status_pending') }}</option>
                        <option value="suspended">{{ trans('vendor::vendors.status_suspended') }}</option>
                    </select>
                </div>
                <div id="bulkActionVendorList"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ trans('dashboard/general.cancel') }}
                </button>
                <button type="button" class="btn btn-danger" id="confirmBulkAction">
                    <span class="indicator-label">{{ trans('dashboard/general.confirm') }}</span>
                    <span class="indicator-progress d-none">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        {{ trans('dashboard/general.loading') }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Confirm Action Modal --}}
<div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="confirmActionTitle">{{ trans('dashboard/general.confirm') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="confirmActionBody"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ trans('dashboard/general.cancel') }}
                </button>
                <button type="button" class="btn btn-danger" id="confirmActionBtn">
                    <span class="indicator-label">{{ trans('dashboard/general.confirm') }}</span>
                    <span class="indicator-progress d-none">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        {{ trans('dashboard/general.loading') }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>