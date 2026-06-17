<div class="modal fade" id="createEmployeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('dashboard/employees.create') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.employees.store') }}" method="POST" id="createForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Name --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/employees.name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/employees.email') }} <span
                                        class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>

                        {{-- Phone --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/employees.phone') }}</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                        </div>

                        {{-- Password --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/employees.password') }} <span
                                        class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" required minlength="8">
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/employees.status') }} <span
                                        class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="active">{{ trans('dashboard/employees.status_active') }}</option>
                                    <option value="inactive">{{ trans('dashboard/employees.status_inactive') }}</option>
                                    <option value="on_leave">{{ trans('dashboard/employees.status_on_leave') }}</option>
                                    <option value="terminated">{{ trans('dashboard/employees.status_terminated') }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        {{-- Type --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/employees.type') }} <span
                                        class="text-danger">*</span></label>
                                <select name="type" class="form-select" required>
                                    <option value="full_time">{{ trans('dashboard/employees.type_full_time') }}</option>
                                    <option value="part_time">{{ trans('dashboard/employees.type_part_time') }}</option>
                                    <option value="contractor">{{ trans('dashboard/employees.type_contractor') }}
                                    </option>
                                    <option value="intern">{{ trans('dashboard/employees.type_intern') }}</option>
                                    <option value="remote">{{ trans('dashboard/employees.type_remote') }}</option>
                                </select>
                            </div>
                        </div>

                        {{-- Date --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/employees.date') }}</label>
                                <input type="date" name="date" class="form-control">
                            </div>
                        </div>

                        {{-- Department --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/employees.department') }}</label>
                                <select name="department_id" class="form-select">
                                    <option value="">{{ trans('dashboard/employees.select_department') }}</option>
                                    @foreach($departments ?? [] as $department)
                                    <option value="{{ $department->id }}">{{ $department->getTranslatedName() }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @ownerOnly
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/employees.company') }}</label>
                                <select name="company_id" class="form-select">
                                    <option value="">{{ trans('dashboard/employees.select_company') }}</option>
                                    @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endOwnerOnly
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{
                        trans('dashboard/general.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ trans('dashboard/general.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
