{{-- ============================= --}}
{{-- EDIT MODAL --}}
{{-- ============================= --}}
<div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-labelledby="editEmployeeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="editEmployeeModalLabel">
                    <i class="ti ti-edit me-2"></i>
                    {{ trans('dashboard/employees.edit') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                {{-- Loader --}}
                <div id="editModalLoader" class="py-5 text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                {{-- Form --}}
                <form id="editEmployeeForm" class="d-none" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="editEmployeeId">
                    {{-- Avatar --}}
                    <div class="col-md-12">
                        <div class="p-3 text-center border rounded">
                            <label for="editEmployeeInput" class="form-label fw-bold">{{ trans('dashboard/employees.avatar') }}</label>
                            <input class="form-control" type="file" name="employee" id="editEmployeeInput" accept="image/*">
                            <div class="mt-2">
                                <img id="editEmployeePreview" src="" alt=""
                                    style="display: none; width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 2px solid #e0e0e0; cursor: pointer;"
                                    onclick="window.openImageModal(this.src, '{{ trans('dashboard/employees.avatar') }}')">
                                <span id="editEmployeePlaceholder" class="text-muted">{{ trans('dashboard/employees.no_avatar') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        {{-- Name --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/employees.name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="edit_name">
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/employees.email') }} <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" id="edit_email">
                            </div>
                        </div>

                        {{-- Phone --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/employees.phone') }}</label>
                                <input type="text" class="form-control" name="phone" id="edit_phone">
                            </div>
                        </div>

                        {{-- Password --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/employees.password') }}</label>
                                <input type="password" class="form-control" name="password" id="edit_password"
                                    placeholder="اتركه فارغاً إذا لم تريد التغيير" minlength="8">
                                <small class="text-muted">{{ trans('dashboard/employees.leave_blank_to_keep') }}</small>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/employees.status') }} <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="status" id="edit_status">
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
                                <select class="form-select" name="type" id="edit_type">
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
                                <input type="date" class="form-control" name="date" id="edit_date">
                            </div>
                        </div>

                        {{-- Department --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/employees.department') }}</label>
                                <select class="form-select" name="department_id" id="edit_department_id">
                                    <option value="">{{ trans('dashboard/employees.select_department') }}</option>
                                    @foreach(\App\Models\Department::active()->with('translations')->get() as $department)
                                    <option value="{{ $department->id }}">{{ $department->getTranslatedName() }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @ownerOnly
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/employees.company') }}</label>
                                <select class="form-select" name="company_id" id="edit_company_id">
                                    <option value="">-- {{ trans('dashboard/general.select') }} --</option>
                                    @foreach(\App\Models\Company::whereStatus('active')->get(['id','name']) as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endOwnerOnly
                    </div>
                </form>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ trans('dashboard/general.cancel') }}
                </button>
                <button type="button" class="btn btn-primary" id="saveEditEmployee">
                    <span class="indicator-label">
                        <i class="ti ti-check me-1"></i>
                        {{ trans('dashboard/general.save') }}
                    </span>
                    <span class="indicator-progress d-none">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        {{ trans('dashboard/general.loading') }}
                    </span>
                </button>
            </div>

        </div>
    </div>
</div>

{{-- ============================= --}}
{{-- DELETE MODAL --}}
{{-- ============================= --}}
<div class="modal fade" id="deleteEmployeeModal" tabindex="-1" aria-labelledby="deleteEmployeeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="pb-0 border-0 modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="px-4 pb-2 text-center modal-body">
                <div class="mb-3">
                    <span
                        class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center"
                        style="width:64px;height:64px;">
                        <i class="ti ti-trash fs-2"></i>
                    </span>
                </div>
                <h5 class="mb-1 fw-bold">{{ trans('dashboard/general.delete_confirm_title') }}</h5>
                <p class="mb-0 text-muted">{{ trans('dashboard/general.delete_confirm_text') }}</p>
            </div>

            <div class="pt-2 border-0 modal-footer justify-content-center">
                <input type="hidden" id="deleteEmployeeId">
                <button type="button" class="px-4 btn btn-secondary" data-bs-dismiss="modal">
                    {{ trans('dashboard/general.cancel') }}
                </button>
                <button type="button" class="px-4 btn btn-danger" id="confirmDeleteEmployee">
                    <span class="indicator-label">
                        <i class="ti ti-trash me-1"></i>
                        {{ trans('dashboard/general.delete') }}
                    </span>
                    <span class="indicator-progress d-none">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        {{ trans('dashboard/general.loading') }}
                    </span>
                </button>
            </div>

        </div>
    </div>
</div>


{{-- ============================= --}}
{{-- CONFIRM ACTION MODAL --}}
{{-- ============================= --}}
<div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmActionTitle">تأكيد الإجراء</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="confirmActionBody">
                هل أنت متأكد من هذا الإجراء؟
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-danger" id="confirmActionBtn">
                    <span class="indicator-label">تأكيد</span>
                    <span class="indicator-progress d-none">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        جاري...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================= --}}
{{-- BULK ACTION MODAL --}}
{{-- ============================= --}}
<div class="modal fade" id="bulkActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkActionModalTitle">تأكيد الإجراء</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="bulkActionMessage">هل أنت متأكد من هذا الإجراء؟</p>
                <div id="bulkStatusDropdown" style="display: none;">
                    <label class="mb-2 fw-bold">{{ trans('dashboard/employees.select_status') }}</label>
                    <select class="form-select" id="bulkStatusSelect">
                        <option value="active">{{ trans('dashboard/employees.status_active') }}</option>
                        <option value="inactive">{{ trans('dashboard/employees.status_inactive') }}</option>
                        <option value="on_leave">{{ trans('dashboard/employees.status_on_leave') }}</option>
                        <option value="terminated">{{ trans('dashboard/employees.status_terminated') }}</option>
                    </select>
                </div>
                <div class="mt-3">
                    <label class="fw-bold">{{ trans('dashboard/employees.selected_employees') }}</label>
                    <div id="bulkActionEmployeeList" class="mt-2" style="max-height: 200px; overflow-y: auto;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ trans('dashboard/general.cancel') }}
                </button>
                <button type="button" class="btn btn-danger" id="confirmBulkAction">
                    <span class="indicator-label">{{ trans('dashboard/general.confirm') }}</span>
                    <span class="indicator-progress d-none">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        {{ trans('dashboard/general.loading') }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
