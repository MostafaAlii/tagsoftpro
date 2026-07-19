@if($zone->trashed())
<button type="button" class="btn btn-sm btn-icon btn-light-success btn-restore" data-id="{{ $zone->id }}"
    data-route="{{ route('admin.zones.restore', $zone->id) }}" title="{{ trans('zone::zones.restore') }}">
    <i class="ti ti-refresh fs-5"></i>
</button>

<button type="button" class="btn btn-sm btn-icon btn-light-danger btn-force-delete" data-id="{{ $zone->id }}"
    data-route="{{ route('admin.zones.forceDelete', $zone->id) }}" title="{{ trans('zone::zones.force_delete') }}">
    <i class="ti ti-trash-off fs-5"></i>
</button>
@else
<button type="button" class="btn btn-sm btn-icon btn-light-warning btn-edit" data-id="{{ $zone->id }}"
    title="{{ trans('zone::zones.edit') }}">
    <i class="ti ti-edit fs-5"></i>
</button>

<button type="button" class="btn btn-sm btn-icon btn-light-danger btn-delete" data-id="{{ $zone->id }}"
    title="{{ trans('zone::zones.delete') }}">
    <i class="ti ti-trash fs-5"></i>
</button>
@endif