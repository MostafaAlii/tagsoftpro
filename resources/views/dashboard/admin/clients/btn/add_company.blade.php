<div class="d-flex flex-column align-items-center gap-1">
    <span class="text-muted small">{{ trans('dashboard/client.no_companies') }}</span>
    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
        data-bs-target="#addCompanyModal{{ $client->id }}">
        <i class="fa fa-plus"></i> {{ trans('dashboard/client.add_company') }}
    </button>
</div>

<div class="modal fade" id="addCompanyModal{{ $client->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.clients.companies.store', $client->id) }}" method="POST">
            @csrf
            <input type="hidden" name="client_id" value="{{ $client->id }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('dashboard/client.add_company') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">{{ trans('dashboard/company.name') }}</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ trans('dashboard/company.email') }}</label>
                        <input type="email" name="email" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ trans('dashboard/company.phone') }}</label>
                        <input type="text" name="phone" class="form-control">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{
                        trans('dashboard/general.closed') }}</button>
                    <button type="submit" class="btn btn-primary">{{ trans('dashboard/general.save') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>