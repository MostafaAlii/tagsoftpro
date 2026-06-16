<button type="button" class="badge badge-primary text-primary fs-6 border-0 bg-transparent" data-bs-toggle="modal"
    data-bs-target="#companiesModal{{ $client->id }}">
    <i class="fa fa-building"></i>
    {{ $client->companies_count }} {{ trans('dashboard/client.companies') }}
</button>

<div class="modal fade" id="companiesModal{{ $client->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">
                    <i class="fa fa-building me-2"></i>
                    {{ trans('dashboard/client.companies') }} - {{ $client->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">{{ trans('dashboard/company.name') }}</th>
                            <th class="text-center">{{ trans('dashboard/company.email') }}</th>
                            <th class="text-center">{{ trans('dashboard/company.phone') }}</th>
                            <th class="text-center">{{ trans('dashboard/general.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($client->companies as $company)
                        <tr id="company-row-{{ $company->id }}" class="{{ \App\Enums\Company\CompanyStatus::rowClass($company->status->value ?? $company->status) }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $company->name }}</td>
                            <td>{{ $company->email ?? '-' }}</td>
                            <td>{{ $company->phone ?? '-' }}</td>
                            <td>
                                <select name="status" class="form-select form-select-sm company-status-select"
                                    data-route="{{ route('admin.clients.companies.updateStatus', ['client' => $client->id, 'company' => $company->id]) }}"
                                    data-row-id="company-row-{{ $company->id }}">
                                    @foreach(\App\Enums\Company\CompanyStatus::cases() as $status)
                                        <option value="{{ $status->value }}"
                                            {{ $company->status === $status ? 'selected' : '' }}>
                                            {{ trans('dashboard/general.' . strtolower($status->name)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ trans('dashboard/general.closed') }}
                </button>
            </div>
        </div>
    </div>
</div>