@if($menuItem->trashed())
<button type="button" class="btn btn-sm btn-icon btn-light-success btn-restore" data-id="{{ $menuItem->id }}"
    data-route="{{ route('admin.menu_items.restore', $menuItem->id) }}"
    title="{{ trans('dashboard/menu_items.restore') }}">
    <i class="ti ti-refresh fs-5"></i>
</button>

<button type="button" class="btn btn-sm btn-icon btn-light-danger btn-force-delete" data-id="{{ $menuItem->id }}"
    data-route="{{ route('admin.menu_items.forceDelete', $menuItem->id) }}"
    title="{{ trans('dashboard/menu_items.force_delete') }}">
    <i class="ti ti-trash-off fs-5"></i>
</button>
@else
<button type="button" class="btn btn-sm btn-icon btn-light-warning btn-edit" data-id="{{ $menuItem->id }}"
    title="{{ trans('dashboard/general.edit') }}">
    <i class="ti ti-edit fs-5"></i>
</button>

<button type="button" class="btn btn-sm btn-icon btn-light-danger btn-delete" data-id="{{ $menuItem->id }}"
    title="{{ trans('dashboard/general.delete') }}">
    <i class="ti ti-trash fs-5"></i>
</button>
@endif
