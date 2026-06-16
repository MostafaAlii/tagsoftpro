<div class="modal fade" id="createPlanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('dashboard/plans.create') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.plans.store') }}" method="POST" id="createForm">
                @csrf
                <div class="modal-body">
                    {{-- Tabs --}}
                    <ul class="mb-3 nav nav-tabs">
                        @foreach($locales as $locale)
                        <li class="nav-item">
                            <button type="button" class="nav-link {{ $locale === $currentLocale ? 'active' : '' }}"
                                data-bs-toggle="tab" data-bs-target="#create-tab-{{ $locale }}">
                                {{ config('laravellocalization.supportedLocales.' . $locale . '.native') }}
                            </button>
                        </li>
                        @endforeach
                    </ul>

                    {{-- Tab Content --}}
                    <div class="mb-3 tab-content">
                        @foreach($locales as $locale)
                        <div class="tab-pane fade {{ $locale === $currentLocale ? 'show active' : '' }}"
                            id="create-tab-{{ $locale }}">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/plans.name') }} ({{ strtoupper($locale)
                                    }}) <span class="text-danger">*</span></label>
                                <input type="text" name="name[{{ $locale }}]" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/plans.description') }} ({{
                                    strtoupper($locale) }})</label>
                                <textarea name="description[{{ $locale }}]" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/plans.price') }} <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/plans.billing_cycle') }} <span
                                        class="text-danger">*</span></label>
                                <select name="billing_cycle" class="form-select" required>
                                    <option value="monthly">{{ trans('dashboard/plans.billing_monthly') }}</option>
                                    <option value="yearly">{{ trans('dashboard/plans.billing_yearly') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    @ownerOnly
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/plans.company') }}</label>
                                <select name="company_id" class="form-select">
                                    <option value="">{{ trans('dashboard/plans.select_company') }}</option>
                                    @foreach($companies ?? [] as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    @endOwnerOnly

                    <div class="mb-3 d-flex align-items-center justify-content-between">
                        <label class="mb-0 form-label">{{ trans('dashboard/plans.status') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="status" value="0">
                            <input class="form-check-input" type="checkbox" name="status" value="1" id="statusSwitch"
                                checked>
                            <label class="form-check-label" for="statusSwitch">
                                <span id="statusLabel">{{ trans('dashboard/general.active') }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{
                        trans('dashboard/general.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ trans('dashboard/general.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('statusSwitch')?.addEventListener('change', function() {
        document.getElementById('statusLabel').textContent = this.checked
            ? '{{ trans("dashboard/general.active") }}'
            : '{{ trans("dashboard/general.in_active") }}';
    });
</script>