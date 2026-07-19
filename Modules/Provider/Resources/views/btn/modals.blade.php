{{-- Edit Modal --}}
<div class="modal fade" id="editProviderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-edit me-2"></i>
                    {{ trans('provider::providers.edit') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="editModalLoader" class="py-5 text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <form id="editProviderForm" class="d-none">
                    @csrf
                    <input type="hidden" id="modal_edit_provider_id">
                    <div class="row g-3">
                        {{-- Name --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('provider::providers.name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="modal_edit_name"
                                    placeholder="{{ trans('provider::providers.name_placeholder') }}" required>
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('provider::providers.email') }} <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" id="modal_edit_email"
                                    placeholder="{{ trans('provider::providers.email_placeholder') }}" required>
                            </div>
                        </div>

                        {{-- Phone --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('provider::providers.phone') }}</label>
                                <input type="text" class="form-control" name="phone" id="modal_edit_phone"
                                    placeholder="{{ trans('provider::providers.phone_placeholder') }}">
                            </div>
                        </div>

                        {{-- Password --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('provider::providers.password') }}</label>
                                <input type="password" class="form-control" name="password" id="modal_edit_password"
                                    placeholder="{{ trans('provider::providers.password_leave_blank') }}">
                                <small class="text-muted">{{ trans('provider::providers.password_leave_blank')
                                    }}</small>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('provider::providers.status') }} <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="status" id="modal_edit_status" required>
                                    <option value="active">{{ trans('provider::providers.status_active') }}</option>
                                    <option value="inactive">{{ trans('provider::providers.status_inactive') }}</option>
                                    <option value="pending">{{ trans('provider::providers.status_pending') }}</option>
                                    <option value="suspended">{{ trans('provider::providers.status_suspended') }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        {{-- Date --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('provider::providers.date') }}</label>
                                <input type="date" class="form-control" name="date" id="modal_edit_date">
                            </div>
                        </div>

                        {{-- Address --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('provider::providers.address') }}</label>
                                <input type="text" class="form-control" name="address" id="modal_edit_address"
                                    placeholder="{{ trans('provider::providers.address_placeholder') }}">
                            </div>
                        </div>

                        {{-- Website --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('provider::providers.website') }}</label>
                                <input type="url" class="form-control" name="website" id="modal_edit_website"
                                    placeholder="{{ trans('provider::providers.website_placeholder') }}">
                            </div>
                        </div>

                        {{-- Zone --}}
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('provider::providers.zone') }}</label>
                                <select class="form-select" name="zone_id" id="modal_edit_zone_id">
                                    <option value="">{{ trans('provider::providers.select_zone') }}</option>
                                    @foreach($zones ?? [] as $zone)
                                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ trans('provider::providers.cancel') }}
                </button>
                <button type="button" class="btn btn-primary" id="modal_save_edit_provider">
                    <span class="indicator-label">
                        <i class="ti ti-check me-1"></i>
                        {{ trans('provider::providers.save') }}
                    </span>
                    <span class="indicator-progress d-none">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        {{ trans('provider::providers.loading') }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="deleteProviderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="ti ti-alert-triangle me-2"></i>
                    {{ trans('provider::providers.delete') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{ trans('provider::providers.delete_confirm') }}</p>
                <input type="hidden" id="deleteProviderId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ trans('provider::providers.cancel') }}
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteProvider">
                    <span class="indicator-label">
                        <i class="ti ti-trash me-1"></i>
                        {{ trans('provider::providers.delete') }}
                    </span>
                    <span class="indicator-progress d-none">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        {{ trans('provider::providers.loading') }}
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
                <h5 class="modal-title" id="bulkActionModalTitle">{{ trans('provider::providers.bulk_actions') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="bulkActionMessage"></p>
                <div id="bulkStatusDropdown" style="display: none;">
                    <label class="form-label">{{ trans('provider::providers.select_status') }}</label>
                    <select class="form-select" id="bulkStatusSelect">
                        <option value="active">{{ trans('provider::providers.status_active') }}</option>
                        <option value="inactive">{{ trans('provider::providers.status_inactive') }}</option>
                        <option value="pending">{{ trans('provider::providers.status_pending') }}</option>
                        <option value="suspended">{{ trans('provider::providers.status_suspended') }}</option>
                    </select>
                </div>
                <div id="bulkActionProviderList"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ trans('provider::providers.cancel') }}
                </button>
                <button type="button" class="btn btn-danger" id="confirmBulkAction">
                    <span class="indicator-label">{{ trans('provider::providers.confirm') }}</span>
                    <span class="indicator-progress d-none">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        {{ trans('provider::providers.loading') }}
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
                <h5 class="modal-title text-danger" id="confirmActionTitle">{{ trans('provider::providers.confirm') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="confirmActionBody"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ trans('provider::providers.cancel') }}
                </button>
                <button type="button" class="btn btn-danger" id="confirmActionBtn">
                    <span class="indicator-label">{{ trans('provider::providers.confirm') }}</span>
                    <span class="indicator-progress d-none">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        {{ trans('provider::providers.loading') }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>