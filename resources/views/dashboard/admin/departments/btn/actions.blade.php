<div class="d-flex align-items-center justify-content-center gap-2">

    {{-- Edit Button --}}
    <button type="button" class="btn btn-sm btn-icon btn-light-warning btn-edit" data-id="{{ $department->id }}"
        title="{{ trans('dashboard/general.edit') }}">
        <i class="ti ti-edit fs-5"></i>
    </button>

    {{-- Delete Button --}}
    <button type="button" class="btn btn-sm btn-icon btn-light-danger btn-delete" data-id="{{ $department->id }}"
        title="{{ trans('dashboard/general.delete') }}">
        <i class="ti ti-trash fs-5"></i>
    </button>

</div>

@include('dashboard.admin.departments.btn.modals')