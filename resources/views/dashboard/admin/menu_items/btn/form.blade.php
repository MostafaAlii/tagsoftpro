<div class="row g-3">
    {{-- Type --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('dashboard/menu_items.type') }} <span
                    class="text-danger">*</span></label>
            <select class="form-select" name="type" id="edit_type" required>
                <option value="link" {{ old('type', $menuItem->type ?? '') == 'link' ? 'selected' : '' }}>
                    {{ trans('dashboard/menu_items.type_link') }}
                </option>
                <option value="dropdown" {{ old('type', $menuItem->type ?? '') == 'dropdown' ? 'selected' : '' }}>
                    {{ trans('dashboard/menu_items.type_dropdown') }}
                </option>
                <option value="header" {{ old('type', $menuItem->type ?? '') == 'header' ? 'selected' : '' }}>
                    {{ trans('dashboard/menu_items.type_header') }}
                </option>
                <option value="divider" {{ old('type', $menuItem->type ?? '') == 'divider' ? 'selected' : '' }}>
                    {{ trans('dashboard/menu_items.type_divider') }}
                </option>
            </select>
        </div>
    </div>

    {{-- Icon --}}
    {{--<div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('dashboard/menu_items.icon') }}</label>
            <input type="text" class="form-control" name="icon" id="edit_icon"
                value="{{ old('icon', $menuItem->icon ?? '') }}" placeholder="ti ti-users">
            <small class="text-muted">{{ trans('dashboard/menu_items.icon_helper') }}</small>
        </div>
    </div>--}}

    {{-- Icon --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('dashboard/menu_items.icon') }}</label>
            <div class="input-group icon-picker-group">
                <span class="input-group-text" id="icon_preview_{{ $menuItem->id ?? 'new' }}">
                    <i class="{{ $menuItem->icon ?? 'ti ti-users' }}"></i>
                </span>
                <select class="form-select icon-picker-select" name="icon" id="icon_select_{{ $menuItem->id ?? 'new' }}"
                    data-preview="#icon_preview_{{ $menuItem->id ?? 'new' }}" style="width: 100%;">
                    <option value="">{{ trans('dashboard/menu_items.select_icon') }}</option>
                    @foreach($icons ?? [] as $label => $fullClass)
                    <option value="{{ $fullClass }}" {{ (old('icon', $menuItem->icon ?? '') == $fullClass) ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
            </div>
            <small class="text-muted">{{ trans('dashboard/menus.icon_helper') }}</small>
        </div>
    </div>

    {{-- Link Type --}}
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">{{ trans('dashboard/menu_items.link_type') }}</label>
            <select class="form-select" name="link_type" id="edit_link_type">
                <option value="route" {{ old('link_type', $menuItem->link_type ?? '') == 'route' ? 'selected' : '' }}>
                    {{ trans('dashboard/menu_items.link_type_route') }}
                </option>
                <option value="url" {{ old('link_type', $menuItem->link_type ?? '') == 'url' ? 'selected' : '' }}>
                    {{ trans('dashboard/menu_items.link_type_url') }}
                </option>
                <option value="none" {{ old('link_type', $menuItem->link_type ?? '') == 'none' ? 'selected' : '' }}>
                    {{ trans('dashboard/menu_items.link_type_none') }}
                </option>
            </select>
        </div>
    </div>

    {{-- Route Name --}}
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">{{ trans('dashboard/menu_items.route_name') }}</label>
            <input type="text" class="form-control" name="route_name" id="edit_route_name"
                value="{{ old('route_name', $menuItem->route_name ?? '') }}" placeholder="admin.dashboard">
        </div>
    </div>

    {{-- URL --}}
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">{{ trans('dashboard/menu_items.url') }}</label>
            <input type="text" class="form-control" name="url" id="edit_url"
                value="{{ old('url', $menuItem->url ?? '') }}" placeholder="https://example.com">
        </div>
    </div>

    {{-- Target --}}
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">{{ trans('dashboard/menu_items.target') }}</label>
            <select class="form-select" name="target" id="edit_target">
                <option value="_self" {{ old('target', $menuItem->target ?? '') == '_self' ? 'selected' : '' }}>
                    {{ trans('dashboard/menu_items.target_self') }}
                </option>
                <option value="_blank" {{ old('target', $menuItem->target ?? '') == '_blank' ? 'selected' : '' }}>
                    {{ trans('dashboard/menu_items.target_blank') }}
                </option>
            </select>
        </div>
    </div>

    {{-- Status --}}
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">{{ trans('dashboard/menu_items.status') }} <span
                    class="text-danger">*</span></label>
            <select class="form-select" name="status" id="edit_status" required>
                <option value="active" {{ old('status', $menuItem->status ?? '') == 'active' ? 'selected' : '' }}>
                    {{ trans('dashboard/menu_items.status_active') }}
                </option>
                <option value="inactive" {{ old('status', $menuItem->status ?? '') == 'inactive' ? 'selected' : '' }}>
                    {{ trans('dashboard/menu_items.status_inactive') }}
                </option>
            </select>
        </div>
    </div>

    {{-- Is Owner Only --}}
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">{{ trans('dashboard/menu_items.is_owner_only') }}</label>
            <select class="form-select" name="is_owner_only" id="edit_is_owner_only">
                <option value="0" {{ old('is_owner_only', $menuItem->is_owner_only ?? 0) == 0 ? 'selected' : '' }}>
                    {{ trans('dashboard/general.no') }}
                </option>
                <option value="1" {{ old('is_owner_only', $menuItem->is_owner_only ?? 0) == 1 ? 'selected' : '' }}>
                    {{ trans('dashboard/general.yes') }}
                </option>
            </select>
        </div>
    </div>

    {{-- Permission Name --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('dashboard/menu_items.permission_name') }}</label>
            <input type="text" class="form-control" name="permission_name" id="edit_permission_name"
                value="{{ old('permission_name', $menuItem->permission_name ?? '') }}" placeholder="view_dashboard">
        </div>
    </div>

    {{-- Badge --}}
    <div class="col-md-3">
        <div class="mb-3">
            <label class="form-label">{{ trans('dashboard/menu_items.badge_text') }}</label>
            <input type="text" class="form-control" name="badge_text" id="edit_badge_text"
                value="{{ old('badge_text', $menuItem->badge_text ?? '') }}" placeholder="New">
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label class="form-label">{{ trans('dashboard/menu_items.badge_color') }}</label>
            <select class="form-select" name="badge_color" id="edit_badge_color">
                <option value="">{{ trans('dashboard/general.select') }}</option>
                <option value="primary" {{ old('badge_color', $menuItem->badge_color ?? '') == 'primary' ? 'selected' :
                    '' }}>Primary</option>
                <option value="secondary" {{ old('badge_color', $menuItem->badge_color ?? '') == 'secondary' ?
                    'selected' : '' }}>Secondary</option>
                <option value="success" {{ old('badge_color', $menuItem->badge_color ?? '') == 'success' ? 'selected' :
                    '' }}>Success</option>
                <option value="danger" {{ old('badge_color', $menuItem->badge_color ?? '') == 'danger' ? 'selected' : ''
                    }}>Danger</option>
                <option value="warning" {{ old('badge_color', $menuItem->badge_color ?? '') == 'warning' ? 'selected' :
                    '' }}>Warning</option>
                <option value="info" {{ old('badge_color', $menuItem->badge_color ?? '') == 'info' ? 'selected' : ''
                    }}>Info</option>
            </select>
        </div>
    </div>

    {{-- Visibility --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('dashboard/menu_items.visible_from') }}</label>
            <input type="datetime-local" class="form-control" name="visible_from" id="edit_visible_from"
                value="{{ old('visible_from', $menuItem->visible_from ?? '') }}">
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('dashboard/menu_items.visible_until') }}</label>
            <input type="datetime-local" class="form-control" name="visible_until" id="edit_visible_until"
                value="{{ old('visible_until', $menuItem->visible_until ?? '') }}">
        </div>
    </div>

    @ownerOnly
    <div class="col-md-12">
        <div class="mb-3">
            <label class="form-label">{{ trans('dashboard/menu_items.company') }}</label>
            <select class="form-select" name="company_id" id="edit_company_id">
                <option value="">-- {{ trans('dashboard/general.select') }} --</option>
                @foreach($companies as $company)
                <option value="{{ $company->id }}" {{ old('company_id', $menuItem->company_id ?? '') == $company->id ?
                    'selected' : '' }}>
                    {{ $company->name }}
                </option>
                @endforeach
            </select>
        </div>
    </div>
    @endOwnerOnly

    {{-- ─── Translations Tabs ────────────────────────────────────── --}}
    <div class="col-md-12">
        <div class="card">
            <div class="p-0 card-header">
                <ul class="nav nav-tabs w-100" role="tablist">
                    @foreach($locales as $index => $locale)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" id="lang-{{ $locale }}-tab"
                            data-bs-toggle="tab" data-bs-target="#lang-{{ $locale }}" type="button" role="tab">
                            {{ strtoupper($locale) }}
                            <span class="text-danger">*</span>
                        </button>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    @foreach($locales as $index => $locale)
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="lang-{{ $locale }}"
                        role="tabpanel">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">
                                        {{ trans('dashboard/menu_items.title') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="locales[{{ $locale }}][title]"
                                        id="edit_locales_{{ $locale }}_title"
                                        value="{{ old('locales.' . $locale . '.title', $menuItem?->translate($locale)?->title ?? '') }}"
                                        @if($locale=='ar' ) required @endif>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">
                                        {{ trans('dashboard/menu_items.description') }}
                                    </label>
                                    <textarea class="form-control" name="locales[{{ $locale }}][description]"
                                        id="edit_locales_{{ $locale }}_description"
                                        rows="3">{{ old('locales.' . $locale . '.description', $menuItem?->translate($locale)?->description ?? '') }}</textarea>
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