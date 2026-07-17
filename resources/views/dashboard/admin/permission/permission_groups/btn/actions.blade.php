@if($permissionGroup->trashed())
{{-- Restore Button --}}
<button type="button" class="btn btn-sm btn-icon btn-light-success btn-restore"
    data-id="{{ $permissionGroup->id ?? 0 }}"
    data-route="{{ $permissionGroup->id ? route('admin.permission_groups.restore', $permissionGroup->id) : '#' }}"
    title="{{ trans('dashboard/permission_groups.restore') }}">
    <i class="ti ti-refresh fs-5"></i>
</button>

{{-- Force Delete Button --}}
<button type="button" class="btn btn-sm btn-icon btn-light-danger btn-force-delete"
    data-id="{{ $permissionGroup->id ?? 0 }}"
    data-route="{{ $permissionGroup->id ? route('admin.permission_groups.forceDelete', $permissionGroup->id) : '#' }}"
    title="{{ trans('dashboard/permission_groups.force_delete') }}">
    <i class="ti ti-trash-off fs-5"></i>
</button>
@else
{{-- Edit Button --}}
<button type="button" class="btn btn-sm btn-icon btn-light-warning btn-edit" data-id="{{ $permissionGroup->id ?? 0 }}"
    title="{{ trans('dashboard/general.edit') }}">
    <i class="ti ti-edit fs-5"></i>
</button>

{{-- Delete Button --}}
<button type="button" class="btn btn-sm btn-icon btn-light-danger btn-delete" data-id="{{ $permissionGroup->id ?? 0 }}"
    title="{{ trans('dashboard/general.delete') }}">
    <i class="ti ti-trash fs-5"></i>
</button>
@endif
@include('dashboard.admin.permission.permission_groups.btn.modals')