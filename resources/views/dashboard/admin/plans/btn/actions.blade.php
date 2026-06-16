<div class="d-flex align-items-center justify-content-center gap-2">

    <button type="button" class="btn btn-sm btn-icon btn-light-primary btn-features" data-id="{{ $plan->id }}"
        title="{{ trans('dashboard/plans.manage_features') }}">
        <i class="ti ti-list-check fs-5"></i>
    </button>

    {{-- Edit Button --}}
    <button type="button" class="btn btn-sm btn-icon btn-light-warning btn-edit" data-id="{{ $plan->id }}"
        title="{{ trans('dashboard/general.edit') }}">
        <i class="ti ti-edit fs-5"></i>
    </button>

    {{-- Delete Button --}}
    <button type="button" class="btn btn-sm btn-icon btn-light-danger btn-delete" data-id="{{ $plan->id }}"
        title="{{ trans('dashboard/general.delete') }}">
        <i class="ti ti-trash fs-5"></i>
    </button>

</div>

@include('dashboard.admin.plans.btn.modals')