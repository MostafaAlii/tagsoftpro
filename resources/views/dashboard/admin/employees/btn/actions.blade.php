@if($employee->trashed())
{{-- Restore Button --}}
<button type="button" class="btn btn-sm btn-icon btn-light-success btn-restore" data-id="{{ $employee->id }}"
    data-route="{{ route('admin.employees.restore', $employee->id) }}"
    title="{{ trans('dashboard/employees.restore') }}">
    <i class="ti ti-refresh fs-5"></i>
</button>

{{-- Force Delete Button --}}
<button type="button" class="btn btn-sm btn-icon btn-light-danger btn-force-delete" data-id="{{ $employee->id }}"
    data-route="{{ route('admin.employees.forceDelete', $employee->id) }}"
    title="{{ trans('dashboard/employees.force_delete') }}">
    <i class="ti ti-trash-off fs-5"></i>
</button>
@else
{{-- Edit Button --}}
<button type="button" class="btn btn-sm btn-icon btn-light-warning btn-edit" data-id="{{ $employee->id }}"
    title="{{ trans('dashboard/general.edit') }}">
    <i class="ti ti-edit fs-5"></i>
</button>

{{-- Delete Button --}}
<button type="button" class="btn btn-sm btn-icon btn-light-danger btn-delete" data-id="{{ $employee->id }}"
    title="{{ trans('dashboard/general.delete') }}">
    <i class="ti ti-trash fs-5"></i>
</button>
@endif
@include('dashboard.admin.employees.btn.modals')
