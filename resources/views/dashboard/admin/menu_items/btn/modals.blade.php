{{-- Edit Modal --}}
<div class="modal fade" id="editMenuItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-edit me-2"></i>
                    {{ trans('dashboard/menu_items.edit') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="editModalLoader" class="py-5 text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <form id="editMenuItemForm" class="d-none">
                    @csrf
                    <input type="hidden" id="editMenuItemId">

                    {{-- ─── الحقول مباشرة هنا ─────────────────── --}}
                    <div class="row g-3">
                        {{-- Type --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/menu_items.type') }} <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="type" id="edit_type" required>
                                    <option value="link">{{ trans('dashboard/menu_items.type_link') }}</option>
                                    <option value="dropdown">{{ trans('dashboard/menu_items.type_dropdown') }}</option>
                                    <option value="header">{{ trans('dashboard/menu_items.type_header') }}</option>
                                    <option value="divider">{{ trans('dashboard/menu_items.type_divider') }}</option>
                                </select>
                            </div>
                        </div>

                        {{-- Icon --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/menu_items.icon') }}</label>
                                <div class="input-group icon-picker-group">
                                    <span class="input-group-text" id="edit_icon_preview">
                                        <i class="ti ti-users"></i>
                                    </span>
                                    <select class="form-select icon-picker-select" name="icon" id="edit_icon" data-preview="#edit_icon_preview"
                                        style="width: 100%;">
                                        <option value="">{{ trans('dashboard/menu_items.select_icon') }}</option>
                                        @foreach($icons ?? [] as $label => $fullClass)
                                        <option value="{{ $fullClass }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <small class="text-muted">{{ trans('dashboard/menu_items.icon_helper') }}</small>
                            </div>
                        </div>

                        {{-- Link Type --}}
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/menu_items.link_type') }}</label>
                                <select class="form-select" name="link_type" id="edit_link_type">
                                    <option value="route">{{ trans('dashboard/menu_items.link_type_route') }}</option>
                                    <option value="url">{{ trans('dashboard/menu_items.link_type_url') }}</option>
                                    <option value="none">{{ trans('dashboard/menu_items.link_type_none') }}</option>
                                </select>
                            </div>
                        </div>

                        {{-- Route Name --}}
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/menu_items.route_name') }}</label>
                                <input type="text" class="form-control" name="route_name" id="edit_route_name"
                                    placeholder="admin.dashboard">
                            </div>
                        </div>

                        {{-- URL --}}
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/menu_items.url') }}</label>
                                <input type="text" class="form-control" name="url" id="edit_url"
                                    placeholder="https://example.com">
                            </div>
                        </div>

                        {{-- Target --}}
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/menu_items.target') }}</label>
                                <select class="form-select" name="target" id="edit_target">
                                    <option value="_self">{{ trans('dashboard/menu_items.target_self') }}</option>
                                    <option value="_blank">{{ trans('dashboard/menu_items.target_blank') }}</option>
                                </select>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/menu_items.status') }} <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="status" id="edit_status" required>
                                    <option value="active">{{ trans('dashboard/menu_items.status_active') }}</option>
                                    <option value="inactive">{{ trans('dashboard/menu_items.status_inactive') }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        {{-- Is Owner Only --}}
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/menu_items.is_owner_only') }}</label>
                                <select class="form-select" name="is_owner_only" id="edit_is_owner_only">
                                    <option value="0">{{ trans('dashboard/general.no') }}</option>
                                    <option value="1">{{ trans('dashboard/general.yes') }}</option>
                                </select>
                            </div>
                        </div>

                        {{-- Permission Name --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/menu_items.permission_name') }}</label>
                                <input type="text" class="form-control" name="permission_name" id="edit_permission_name"
                                    placeholder="view_dashboard">
                            </div>
                        </div>

                        {{-- Badge --}}
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/menu_items.badge_text') }}</label>
                                <input type="text" class="form-control" name="badge_text" id="edit_badge_text"
                                    placeholder="New">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/menu_items.badge_color') }}</label>
                                <select class="form-select" name="badge_color" id="edit_badge_color">
                                    <option value="">{{ trans('dashboard/general.select') }}</option>
                                    <option value="primary">Primary</option>
                                    <option value="secondary">Secondary</option>
                                    <option value="success">Success</option>
                                    <option value="danger">Danger</option>
                                    <option value="warning">Warning</option>
                                    <option value="info">Info</option>
                                </select>
                            </div>
                        </div>

                        {{-- Visibility --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/menu_items.visible_from') }}</label>
                                <input type="datetime-local" class="form-control" name="visible_from"
                                    id="edit_visible_from">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/menu_items.visible_until') }}</label>
                                <input type="datetime-local" class="form-control" name="visible_until"
                                    id="edit_visible_until">
                            </div>
                        </div>

                        @ownerOnly
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/menu_items.company') }}</label>
                                <select class="form-select" name="company_id" id="edit_company_id">
                                    <option value="">-- {{ trans('dashboard/general.select') }} --</option>
                                    @foreach($companies ?? [] as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endOwnerOnly

                        {{-- ─── Translations Tabs ─────────────────── --}}
                        <div class="col-md-12">
                            <div class="card">
                                <div class="p-0 card-header">
                                    <ul class="nav nav-tabs w-100" role="tablist">
                                        @foreach($locales ?? [] as $index => $locale)
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link {{ $index === 0 ? 'active' : '' }}"
                                                id="lang-{{ $locale }}-tab" data-bs-toggle="tab"
                                                data-bs-target="#edit-lang-{{ $locale }}" type="button" role="tab">
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
                                            id="edit-lang-{{ $locale }}" role="tabpanel">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            {{ trans('dashboard/menu_items.title') }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control"
                                                            name="locales[{{ $locale }}][title]"
                                                            id="edit_locales_{{ $locale }}_title" @if($locale=='ar' )
                                                            required @endif>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            {{ trans('dashboard/menu_items.description') }}
                                                        </label>
                                                        <textarea class="form-control"
                                                            name="locales[{{ $locale }}][description]"
                                                            id="edit_locales_{{ $locale }}_description"
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
                    {{ trans('dashboard/general.cancel') }}
                </button>
                <button type="button" class="btn btn-primary" id="saveEditMenuItem">
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
<div class="modal fade" id="deleteMenuItemModal" tabindex="-1" aria-hidden="true">
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
                <p>{{ trans('dashboard/menu_items.delete_confirm') }}</p>
                <input type="hidden" id="deleteMenuItemId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ trans('dashboard/general.cancel') }}
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteMenuItem">
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
                <h5 class="modal-title" id="bulkActionModalTitle">{{ trans('dashboard/menu_items.bulk_actions') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="bulkActionMessage"></p>
                <div id="bulkStatusDropdown" style="display: none;">
                    <label class="form-label">{{ trans('dashboard/menu_items.select_status') }}</label>
                    <select class="form-select" id="bulkStatusSelect">
                        <option value="active">{{ trans('dashboard/menu_items.status_active') }}</option>
                        <option value="inactive">{{ trans('dashboard/menu_items.status_inactive') }}</option>
                    </select>
                </div>
                <div id="bulkActionItemList"></div>
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