<div class="row g-3">
    {{-- Icon --}}
    <div class="col-md-12">
        <div class="mb-3">
            <label class="form-label">{{ trans('dashboard/permission_groups.icon') }}</label>
            <input type="text" class="form-control" name="icon" value="{{ old('icon', $permissionGroup->icon ?? '') }}"
                placeholder="ti ti-users">
            <small class="text-muted">{{ trans('dashboard/permission_groups.icon_helper') }}</small>
        </div>
    </div>

    {{-- Status --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('dashboard/permission_groups.status') }} <span
                    class="text-danger">*</span></label>
            <select class="form-select" name="status">
                <option value="active" {{ old('status', $permissionGroup->status ?? '') == 'active' ? 'selected' : ''
                    }}>
                    {{ trans('dashboard/permission_groups.status_active') }}
                </option>
                <option value="inactive" {{ old('status', $permissionGroup->status ?? '') == 'inactive' ? 'selected' :
                    '' }}>
                    {{ trans('dashboard/permission_groups.status_inactive') }}
                </option>
            </select>
        </div>
    </div>

    @ownerOnly
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('dashboard/permission_groups.company') }}</label>
            <select class="form-select" name="company_id">
                <option value="">-- {{ trans('dashboard/general.select') }} --</option>
                @foreach($companies as $company)
                <option value="{{ $company->id }}" {{ old('company_id', $permissionGroup->company_id ?? '') ==
                    $company->id ? 'selected' : '' }}>
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
                            {{ config('laravellocalization.supportedLocales.' . $locale . '.native') }}
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
                                        {{ trans('dashboard/permission_groups.name') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="locales[{{ $locale }}][name]"
                                        value="{{ old('locales.' . $locale . '.name', $permissionGroup?->translate($locale)?->name ?? '') }}"
                                        @if($locale=='ar' ) required @endif>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">
                                        {{ trans('dashboard/permission_groups.description') }}
                                    </label>
                                    <textarea class="form-control" name="locales[{{ $locale }}][description]"
                                        rows="3">{{ old('locales.' . $locale . '.description', $permissionGroup?->translate($locale)?->description ?? '') }}</textarea>
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