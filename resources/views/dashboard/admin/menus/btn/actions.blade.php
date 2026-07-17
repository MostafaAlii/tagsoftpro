@if($menu->trashed())
<button type="button" class="btn btn-sm btn-icon btn-light-success btn-restore"
        data-id="{{ $menu->id }}"
        data-route="{{ route('admin.menus.restore', $menu->id) }}"
        title="{{ trans('dashboard/menus.restore') }}">
    <i class="ti ti-refresh fs-5"></i>
</button>

<button type="button" class="btn btn-sm btn-icon btn-light-danger btn-force-delete"
        data-id="{{ $menu->id }}"
        data-route="{{ route('admin.menus.forceDelete', $menu->id) }}"
        title="{{ trans('dashboard/menus.force_delete') }}">
    <i class="ti ti-trash-off fs-5"></i>
</button>
@else
<button type="button" class="btn btn-sm btn-icon btn-light-warning btn-edit"
        data-id="{{ $menu->id }}"
        title="{{ trans('dashboard/general.edit') }}">
    <i class="ti ti-edit fs-5"></i>
</button>

<button type="button" class="btn btn-sm btn-icon btn-light-danger btn-delete"
        data-id="{{ $menu->id }}"
        title="{{ trans('dashboard/general.delete') }}">
    <i class="ti ti-trash fs-5"></i>
</button>
@endif
