<div class="modal fade" id="createZoneModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-plus me-2"></i>
                    {{ trans('zone::zones.create') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.zones.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @include('zone::btn.form', [
                    'zone' => null,
                    'locales' => $locales,
                    ])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ trans('zone::zones.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check me-1"></i>
                        {{ trans('zone::zones.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>