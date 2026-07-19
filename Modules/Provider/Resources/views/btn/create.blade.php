<div class="modal fade" id="createProviderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-plus me-2"></i>
                    {{ trans('provider::providers.create') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.providers.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @include('provider::btn.form', [
                    'provider' => null,
                    'zones' => $zones,
                    ])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ trans('provider::providers.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check me-1"></i>
                        {{ trans('provider::providers.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>