<button type="button" class="btn btn-sm btn-primary btn-edit-feature" data-bs-toggle="modal"
    data-bs-target="#editFeatureModal" data-id="{{ $feature->id }}">
    <i class="fa fa-edit"></i>
</button>

{{-- Edit Modal --}}
<div class="modal fade" id="editFeatureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('dashboard/features.edit') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editForm" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PUT">

                <div class="modal-body">
                    @php
                    $locales = array_keys(config('laravellocalization.supportedLocales'));
                    $currentLocale = app()->getLocale();
                    @endphp

                    {{-- Translations --}}
                    <ul class="nav nav-tabs mb-3">
                        @foreach($locales as $locale)
                        <li class="nav-item">
                            <button type="button" class="nav-link {{ $locale === $currentLocale ? 'active' : '' }}"
                                data-bs-toggle="tab" data-bs-target="#edit-tab-{{ $locale }}">
                                {{ config('laravellocalization.supportedLocales.' . $locale . '.native') }}
                            </button>
                        </li>
                        @endforeach
                    </ul>

                    <div class="tab-content mb-3">
                        @foreach($locales as $locale)
                        <div class="tab-pane fade {{ $locale === $currentLocale ? 'show active' : '' }}"
                            id="edit-tab-{{ $locale }}">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/features.name') }}</label>
                                <input type="text" name="name[{{ $locale }}]" class="form-control"
                                    id="edit_name_{{ $locale }}" placeholder="{{ trans('dashboard/features.name') }}">
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Key --}}
                    <div class="mb-3">
                        <label class="form-label">{{ trans('dashboard/features.key') }}</label>
                        <input type="text" name="key" id="edit_key" class="form-control">
                    </div>

                    {{-- Type --}}
                    <div class="mb-3">
                        <label class="form-label">{{ trans('dashboard/features.type') }}</label>
                        <select name="type" id="edit_type" class="form-select">
                            <option value="ui">{{ trans('dashboard/features.type_ui') }}</option>
                            <option value="ordering">{{ trans('dashboard/features.type_ordering') }}</option>
                            <option value="analytics">{{ trans('dashboard/features.type_analytics') }}</option>
                        </select>
                    </div>

                    {{-- Scope --}}
                    <div class="mb-3">
                        <label class="form-label">{{ trans('dashboard/features.scope') }}</label>
                        <select name="scope" id="edit_scope" class="form-select">
                            <option value="main">{{ trans('dashboard/features.scope_main') }}</option>
                            <option value="addon">{{ trans('dashboard/features.scope_addon') }}</option>
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="mb-3 d-flex align-items-center justify-content-between">
                        <label class="mb-0 form-label">{{ trans('dashboard/features.status') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="status" value="0">
                            <input class="form-check-input" type="checkbox" name="status" value="1"
                                id="editStatusSwitch">
                            <label class="form-check-label" for="editStatusSwitch">
                                <span id="editStatusLabel">{{ trans('dashboard/general.in_active') }}</span>
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

{{-- Delete Button --}}
<button type="button" class="mx-1 btn btn-danger btn-sm btn-delete-feature" data-id="{{ $feature->id }}"
    data-name="{{ $feature->translate(app()->getLocale())?->name ?? $feature->translate('ar')?->name }}">
    <i class="fas fa-trash"></i>
</button>

{{-- Delete Modal --}}
<div class="modal fade" id="deleteFeatureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('dashboard/features.delete') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="text-center modal-body">
                <p id="deleteMessage"></p>
                <p class="text-danger">{{ trans('dashboard/general.action_irreversible') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ trans('dashboard/general.cancel') }}
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    {{ trans('dashboard/general.confirm_delete') }}
                </button>
            </div>
        </div>
    </div>
</div>