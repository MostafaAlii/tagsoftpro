<div class="row g-3">
    {{-- Key --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('zone::zones.key') }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="key" id="edit_key" value="{{ old('key', $zone->key ?? '') }}"
                placeholder="{{ trans('zone::zones.key_placeholder') }}">
            <small class="text-muted">{{ trans('zone::zones.key_helper') }}</small>
        </div>
    </div>

    {{-- Status --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('zone::zones.status') }} <span class="text-danger">*</span></label>
            <select class="form-select" name="status" id="edit_status" required>
                <option value="active" {{ old('status', $zone->status ?? '') == 'active' ? 'selected' : '' }}>
                    {{ trans('zone::zones.status_active') }}
                </option>
                <option value="inactive" {{ old('status', $zone->status ?? '') == 'inactive' ? 'selected' : '' }}>
                    {{ trans('zone::zones.status_inactive') }}
                </option>
            </select>
        </div>
    </div>

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
                                        {{ trans('zone::zones.name') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="locales[{{ $locale }}][name]"
                                        id="edit_locales_{{ $locale }}_name"
                                        value="{{ old('locales.' . $locale . '.name', $zone?->translate($locale)?->name ?? '') }}"
                                        @if($locale=='ar' ) required @endif>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">
                                        {{ trans('zone::zones.description') }}
                                    </label>
                                    <textarea class="form-control" name="locales[{{ $locale }}][description]"
                                        id="edit_locales_{{ $locale }}_description"
                                        rows="3">{{ old('locales.' . $locale . '.description', $zone?->translate($locale)?->description ?? '') }}</textarea>
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