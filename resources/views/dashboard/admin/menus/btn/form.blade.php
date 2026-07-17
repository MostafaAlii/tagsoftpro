<div class="row g-3">
    {{-- Key --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('dashboard/menus.key') }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="key" id="edit_key" value="{{ old('key', $menu->key ?? '') }}"
                placeholder="example_menu_key" required>
            <small class="text-muted">{{ trans('dashboard/menus.key_helper') }}</small>
        </div>
    </div>

    {{-- Icon --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('dashboard/menus.icon') }}</label>
            <div class="input-group">
                <span class="input-group-text" id="icon_preview_{{ $menu->id ?? 'new' }}">
                    <i class="{{ $menu->icon ?? 'ti ti-users' }}"></i>
                </span>
                <select class="form-select icon-picker-select" name="icon" id="icon_select_{{ $menu->id ?? 'new' }}"
                    data-preview="#icon_preview_{{ $menu->id ?? 'new' }}" style="width: 100%;">
                    <option value="">{{ trans('dashboard/menus.select_icon') }}</option>
                    @foreach($icons ?? [] as $label => $fullClass)
                    <option value="{{ $fullClass }}" {{ (old('icon', $menu->icon ?? '') == $fullClass) ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
            </div>
            <small class="text-muted">{{ trans('dashboard/menus.icon_helper') }}</small>
        </div>
    </div>

    {{-- Route Prefix --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('dashboard/menus.route_prefix') }}</label>
            <input type="text" class="form-control" name="route_prefix" id="edit_route_prefix"
                value="{{ old('route_prefix', $menu->route_prefix ?? '') }}" placeholder="admin.example">
        </div>
    </div>

    {{-- Status --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('dashboard/menus.status') }} <span class="text-danger">*</span></label>
            <select class="form-select" name="status" id="edit_status" required>
                <option value="active" {{ old('status', $menu->status ?? '') == 'active' ? 'selected' : '' }}>
                    {{ trans('dashboard/menus.status_active') }}
                </option>
                <option value="inactive" {{ old('status', $menu->status ?? '') == 'inactive' ? 'selected' : '' }}>
                    {{ trans('dashboard/menus.status_inactive') }}
                </option>
            </select>
        </div>
    </div>

    @ownerOnly
    <div class="col-md-12">
        <div class="mb-3">
            <label class="form-label">{{ trans('dashboard/menus.company') }}</label>
            <select class="form-select" name="company_id" id="edit_company_id">
                <option value="">-- {{ trans('dashboard/general.select') }} --</option>
                @foreach($companies as $company)
                <option value="{{ $company->id }}" {{ old('company_id', $menu->company_id ?? '') == $company->id ?
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
            <div class="card-header p-0">
                <ul class="nav nav-tabs w-100" role="tablist">
                    @foreach($locales as $index => $locale)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" id="lang-{{ $locale }}-tab"
                            data-bs-toggle="tab" data-bs-target="#edit-lang-{{ $locale }}" type="button" role="tab">
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
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="edit-lang-{{ $locale }}"
                        role="tabpanel">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">
                                        {{ trans('dashboard/menus.name') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="locales[{{ $locale }}][name]"
                                        id="edit_locales_{{ $locale }}_name"
                                        value="{{ old('locales.' . $locale . '.name', $menu?->translate($locale)?->name ?? '') }}"
                                        @if($locale=='ar' ) required @endif>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">
                                        {{ trans('dashboard/menus.description') }}
                                    </label>
                                    <textarea class="form-control" name="locales[{{ $locale }}][description]"
                                        id="edit_locales_{{ $locale }}_description"
                                        rows="3">{{ old('locales.' . $locale . '.description', $menu?->translate($locale)?->description ?? '') }}</textarea>
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
