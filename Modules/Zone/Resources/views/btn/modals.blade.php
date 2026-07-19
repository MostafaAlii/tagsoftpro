{{-- Edit Modal --}}
<div class="modal fade" id="editZoneModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-edit me-2"></i>
                    {{ trans('zone::zones.edit') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="editModalLoader" class="py-5 text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <form id="editZoneForm" class="d-none">
                    @csrf
                    <input type="hidden" id="modal_edit_zone_id">
                    <div class="row g-3">
                        {{-- Key --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('zone::zones.key') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="key" id="modal_edit_key"
                                    placeholder="{{ trans('zone::zones.key_placeholder') }}">
                                <small class="text-muted">{{ trans('zone::zones.key_helper') }}</small>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('zone::zones.status') }} <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="status" id="modal_edit_status" required>
                                    <option value="active">{{ trans('zone::zones.status_active') }}</option>
                                    <option value="inactive">{{ trans('zone::zones.status_inactive') }}</option>
                                </select>
                            </div>
                        </div>

                        {{-- ─── Translations Tabs ─────────────────── --}}
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header p-0">
                                    <ul class="nav nav-tabs w-100" role="tablist">
                                        @foreach($locales ?? [] as $index => $locale)
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link {{ $index === 0 ? 'active' : '' }}"
                                                id="modal_lang-{{ $locale }}-tab" data-bs-toggle="tab"
                                                data-bs-target="#modal_edit-lang-{{ $locale }}" type="button"
                                                role="tab">
                                                {{ strtoupper($locale) }}
                                                <span class="text-danger">*</span>
                                            </button>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        @foreach($locales ?? [] as $index => $locale)
                                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                                            id="modal_edit-lang-{{ $locale }}" role="tabpanel">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            {{ trans('zone::zones.name') }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control"
                                                            name="locales[{{ $locale }}][name]"
                                                            id="modal_edit_locales_{{ $locale }}_name" @if($locale=='ar'
                                                            ) required @endif>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            {{ trans('zone::zones.description') }}
                                                        </label>
                                                        <textarea class="form-control"
                                                            name="locales[{{ $locale }}][description]"
                                                            id="modal_edit_locales_{{ $locale }}_description"
                                                            rows="3"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ trans('zone::zones.cancel') }}
                </button>
                <button type="button" class="btn btn-primary" id="modal_save_edit_zone">
                    <span class="indicator-label">
                        <i class="ti ti-check me-1"></i>
                        {{ trans('zone::zones.save') }}
                    </span>
                    <span class="indicator-progress d-none">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        {{ trans('zone::zones.loading') }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="deleteZoneModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="ti ti-alert-triangle me-2"></i>
                    {{ trans('zone::zones.delete') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{ trans('zone::zones.delete_confirm') }}</p>
                <input type="hidden" id="deleteZoneId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ trans('zone::zones.cancel') }}
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteZone">
                    <span class="indicator-label">
                        <i class="ti ti-trash me-1"></i>
                        {{ trans('zone::zones.delete') }}
                    </span>
                    <span class="indicator-progress d-none">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        {{ trans('zone::zones.loading') }}
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
                <h5 class="modal-title" id="bulkActionModalTitle">{{ trans('zone::zones.bulk_actions') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="bulkActionMessage"></p>
                <div id="bulkStatusDropdown" style="display: none;">
                    <label class="form-label">{{ trans('zone::zones.select_status') }}</label>
                    <select class="form-select" id="bulkStatusSelect">
                        <option value="active">{{ trans('zone::zones.status_active') }}</option>
                        <option value="inactive">{{ trans('zone::zones.status_inactive') }}</option>
                    </select>
                </div>
                <div id="bulkActionZoneList"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ trans('zone::zones.cancel') }}
                </button>
                <button type="button" class="btn btn-danger" id="confirmBulkAction">
                    <span class="indicator-label">{{ trans('zone::zones.confirm') }}</span>
                    <span class="indicator-progress d-none">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        {{ trans('zone::zones.loading') }}
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
                <h5 class="modal-title text-danger" id="confirmActionTitle">{{ trans('zone::zones.confirm') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="confirmActionBody"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ trans('zone::zones.cancel') }}
                </button>
                <button type="button" class="btn btn-danger" id="confirmActionBtn">
                    <span class="indicator-label">{{ trans('zone::zones.confirm') }}</span>
                    <span class="indicator-progress d-none">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        {{ trans('zone::zones.loading') }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>