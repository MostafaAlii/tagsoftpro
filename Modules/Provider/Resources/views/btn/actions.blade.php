@if($provider->trashed())
<button type="button" class="btn btn-sm btn-icon btn-light-success btn-restore" data-id="{{ $provider->id }}"
    data-route="{{ route('admin.providers.restore', $provider->id) }}"
    title="{{ trans('provider::providers.restore') }}">
    <i class="ti ti-refresh fs-5"></i>
</button>

<button type="button" class="btn btn-sm btn-icon btn-light-danger btn-force-delete" data-id="{{ $provider->id }}"
    data-route="{{ route('admin.providers.forceDelete', $provider->id) }}"
    title="{{ trans('provider::providers.force_delete') }}">
    <i class="ti ti-trash-off fs-5"></i>
</button>
@else
<button type="button" class="btn btn-sm btn-icon btn-light-warning btn-edit" data-id="{{ $provider->id }}"
    title="{{ trans('provider::providers.edit') }}">
    <i class="ti ti-edit fs-5"></i>
</button>

<button type="button" class="btn btn-sm btn-icon btn-light-danger btn-delete" data-id="{{ $provider->id }}"
    title="{{ trans('provider::providers.delete') }}">
    <i class="ti ti-trash fs-5"></i>
</button>
@endif