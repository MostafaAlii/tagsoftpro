<div class="modal fade" id="createFeatureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('dashboard/features.create') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="createFeatureForm" action="{{ route('admin.features.store') }}" method="POST">
                @csrf
                <div class="modal-body">

                    {{-- Translations --}}
                    <ul class="nav nav-tabs mb-3">
                        @foreach($locales as $locale)
                        <li class="nav-item">
                            <button type="button" class="nav-link {{ $locale === $currentLocale ? 'active' : '' }}"
                                data-bs-toggle="tab" data-bs-target="#tab-{{ $locale }}">
                                {{ config('laravellocalization.supportedLocales.' . $locale . '.native') }}
                            </button>
                        </li>
                        @endforeach
                    </ul>

                    <div class="tab-content mb-3">
                        @foreach($locales as $locale)
                        <div class="tab-pane fade {{ $locale === $currentLocale ? 'show active' : '' }}"
                            id="tab-{{ $locale }}">
                            <div class="mb-3">
                                <label class="form-label">
                                    {{ trans('dashboard/features.name') }}
                                    @if($locale === 'ar') <span class="text-danger">*</span> @endif
                                </label>
                                <input type="text" name="name[{{ $locale }}]"
                                    class="form-control {{ $errors->has('name.' . $locale) ? 'is-invalid' : '' }}"
                                    value="{{ old('name.' . $locale) }}"
                                    placeholder="{{ trans('dashboard/features.name') }}">
                                @error('name.' . $locale)
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Key --}}
                    <div class="mb-3">
                        <label class="form-label">{{ trans('dashboard/features.key') }}</label>
                        <input type="text" name="key" class="form-control" value="{{ old('key') }}"
                            placeholder="feature_slider">
                    </div>

                    {{-- Type --}}
                    <div class="mb-3">
                        <label class="form-label">{{ trans('dashboard/features.type') }} <span
                                class="text-danger">*</span></label>
                        <select name="type" class="form-select">
                            <option value="ui">{{ trans('dashboard/features.type_ui') }}</option>
                            <option value="ordering">{{ trans('dashboard/features.type_ordering') }}</option>
                            <option value="analytics">{{ trans('dashboard/features.type_analytics') }}</option>
                        </select>
                    </div>

                    {{-- Scope --}}
                    <div class="mb-3">
                        <label class="form-label">{{ trans('dashboard/features.scope') }} <span
                                class="text-danger">*</span></label>
                        <select name="scope" class="form-select">
                            <option value="main">{{ trans('dashboard/features.scope_main') }}</option>
                            <option value="addon">{{ trans('dashboard/features.scope_addon') }}</option>
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="mb-3 d-flex align-items-center justify-content-between">
                        <label class="form-label mb-0">{{ trans('dashboard/features.status') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="status" value="0">
                            <input class="form-check-input" type="checkbox" name="status" value="1" id="isActiveSwitch"
                                checked>
                            <label class="form-check-label" for="isActiveSwitch">
                                <span id="isActiveLabel">{{ trans('dashboard/general.active') }}</span>
                            </label>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ trans('dashboard/general.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        {{ trans('dashboard/general.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>