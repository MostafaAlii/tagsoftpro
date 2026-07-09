<div class="d-flex align-items-center justify-content-center gap-2">

    {{-- Edit Button --}}
    <button type="button" class="btn btn-sm btn-icon btn-light-warning btn-edit" data-id="{{ $theme->id }}"
        title="{{ trans('dashboard/general.edit') }}">
        <i class="ti ti-edit fs-5"></i>
    </button>

    {{-- Delete Button --}}
    {{-- ✅ التحقق المباشر من وجود Default --}}
    @if(!$theme->defaultProjectTypes()->exists())
    <button type="button" class="btn btn-sm btn-icon btn-light-danger btn-delete" data-id="{{ $theme->id }}"
        title="{{ trans('dashboard/general.delete') }}">
        <i class="ti ti-trash fs-5"></i>
    </button>
    @endif

</div>

@include('dashboard.admin.themes.btn.modals')