@php
$locales = array_keys(config('laravellocalization.supportedLocales'));
@endphp

{{-- ============================= --}}
{{-- EDIT MODAL --}}
{{-- ============================= --}}
<div class="modal fade" id="editPlanModal" tabindex="-1" aria-labelledby="editPlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="editPlanModalLabel">
                    <i class="ti ti-edit me-2"></i>
                    {{ trans('dashboard/plans.edit') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                {{-- Loader --}}
                <div id="editModalLoader" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                {{-- Form --}}
                <form id="editPlanForm" class="d-none">
                    @csrf
                    <input type="hidden" id="editPlanId">

                    <div class="row g-3">

                        {{-- Translations as Tabs --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                {{ trans('dashboard/plans.name') }} <span class="text-danger">*</span>
                            </label>

                            <ul class="nav nav-tabs nav-tabs-bordered mb-3" id="editLangTabs" role="tablist">
                                @foreach($locales as $index => $locale)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $index === 0 ? 'active' : '' }}"
                                        id="edit-tab-{{ $locale }}" data-bs-toggle="tab"
                                        data-bs-target="#edit-pane-{{ $locale }}" type="button" role="tab">
                                        {{ strtoupper($locale) }}
                                    </button>
                                </li>
                                @endforeach
                            </ul>

                            <div class="tab-content" id="editLangTabsContent">
                                @foreach($locales as $index => $locale)
                                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                                    id="edit-pane-{{ $locale }}" role="tabpanel">
                                    <div class="mb-2">
                                        <input type="text" class="form-control" name="name[{{ $locale }}]"
                                            id="edit_name_{{ $locale }}"
                                            placeholder="{{ trans('dashboard/plans.name') }} ({{ strtoupper($locale) }})">
                                    </div>
                                    <div>
                                        <textarea class="form-control" name="description[{{ $locale }}]"
                                            id="edit_description_{{ $locale }}" rows="2"
                                            placeholder="{{ trans('dashboard/plans.description') }} ({{ strtoupper($locale) }})"></textarea>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Price & Billing Cycle --}}
                        <div class="col-md-6">
                            <label class="form-label">{{ trans('dashboard/plans.price') }} <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="price" id="edit_price" step="0.01" min="0">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ trans('dashboard/plans.billing_cycle') }}</label>
                            <select class="form-select" name="billing_cycle" id="edit_billing_cycle">
                                <option value="monthly">{{ trans('dashboard/plans.billing_monthly') }}</option>
                                <option value="yearly">{{ trans('dashboard/plans.billing_yearly') }}</option>
                            </select>
                        </div>

                        {{-- Company --}}
                        @ownerOnly
                        <div class="col-md-12">
                            <label class="form-label">{{ trans('dashboard/plans.company') }}</label>
                            <select class="form-select" name="company_id" id="edit_company_id">
                                <option value="">-- {{ trans('dashboard/general.select') }} --</option>
                                @foreach(\App\Models\Company::whereStatus('active')->get(['id','name']) as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endOwnerOnly

                        {{-- Status --}}
                        <div class="col-md-12">
                            <label class="form-label d-block">{{ trans('dashboard/plans.status') }}</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" id="edit_status"
                                    value="1">
                                <label class="form-check-label" for="edit_status">
                                    {{ trans('dashboard/general.active') }}
                                </label>
                            </div>
                        </div>

                    </div>
                </form>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ trans('dashboard/general.cancel') }}
                </button>
                <button type="button" class="btn btn-primary" id="saveEditPlan">
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

{{-- ============================= --}}
{{-- DELETE MODAL --}}
{{-- ============================= --}}
<div class="modal fade" id="deletePlanModal" tabindex="-1" aria-labelledby="deletePlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body text-center px-4 pb-2">
                <div class="mb-3">
                    <span
                        class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center"
                        style="width:64px;height:64px;">
                        <i class="ti ti-trash fs-2"></i>
                    </span>
                </div>
                <h5 class="fw-bold mb-1">{{ trans('dashboard/general.delete_confirm_title') }}</h5>
                <p class="text-muted mb-0">{{ trans('dashboard/general.delete_confirm_text') }}</p>
            </div>

            <div class="modal-footer justify-content-center border-0 pt-2">
                <input type="hidden" id="deletePlanId">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    {{ trans('dashboard/general.cancel') }}
                </button>
                <button type="button" class="btn btn-danger px-4" id="confirmDeletePlan">
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

{{-- ============================= --}}
{{-- FEATURES MODAL --}}
{{-- ============================= --}}
<div class="modal fade" id="featuresPlanModal" tabindex="-1" aria-labelledby="featuresPlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="featuresPlanModalLabel">
                    <i class="ti ti-list-check me-2"></i>
                    {{ trans('dashboard/plans.manage_features') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                {{-- Loader --}}
                <div id="featuresModalLoader" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                {{-- Content --}}
                <div id="featuresContent" class="d-none">
                    <div class="mb-3">
                        <h6 class="fw-bold mb-2">{{ trans('dashboard/plans.plan_features') }}</h6>
                        <p class="text-muted small">{{ trans('dashboard/plans.select_features_for_plan') }}</p>
                    </div>

                    <form id="featuresForm">
                        @csrf
                        <input type="hidden" id="featuresPlanId" name="plan_id">

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40px;">
                                            <input type="checkbox" id="selectAllFeatures" class="form-check-input">
                                        </th>
                                        <th>{{ trans('dashboard/features.name') }}</th>
                                        <th style="width: 120px;">{{ trans('dashboard/features.type') }}</th>
                                        <th style="width: 120px;">{{ trans('dashboard/features.scope') }}</th>
                                        <th style="width: 130px;">{{ trans('dashboard/plans.limit') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="featuresTableBody">
                                    <!-- Dynamic content -->
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ trans('dashboard/general.cancel') }}
                </button>
                <button type="button" class="btn btn-primary" id="savePlanFeatures">
                    <span class="indicator-label">
                        <i class="ti ti-check me-1"></i>
                        {{ trans('dashboard/general.save') }}
                    </span>
                    <span class="indicator-progress d-none">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        {{ trans('dashboard/general.saving') }}
                    </span>
                </button>
            </div>

        </div>
    </div>
</div>

<style>
    #featuresTableBody tr.included {
        background-color: #f0fdf4;
        transition: background-color 0.2s;
    }

    #featuresTableBody tr:not(.included) {
        opacity: 0.6;
        transition: opacity 0.2s;
    }

    #featuresTableBody tr:hover {
        background-color: #f8fafc !important;
    }

    #featuresTableBody tr.included:hover {
        background-color: #dcfce7 !important;
    }

    .limit-input {
        width: 80px;
        display: inline-block;
        padding: 2px 6px;
        font-size: 13px;
        text-align: center;
    }

    .limit-input:disabled {
        background-color: #f1f5f9;
        cursor: not-allowed;
    }

    .feature-checkbox {
        cursor: pointer;
    }

    #selectAllFeatures {
        cursor: pointer;
    }
</style>