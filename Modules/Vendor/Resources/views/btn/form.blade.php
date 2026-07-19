<div class="row g-3">
    {{-- Name --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('vendor::vendors.name') }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" id="edit_name"
                value="{{ old('name', $vendor->name ?? '') }}"
                placeholder="{{ trans('vendor::vendors.name_placeholder') }}" required>
        </div>
    </div>

    {{-- Email --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('vendor::vendors.email') }} <span class="text-danger">*</span></label>
            <input type="email" class="form-control" name="email" id="edit_email"
                value="{{ old('email', $vendor->email ?? '') }}"
                placeholder="{{ trans('vendor::vendors.email_placeholder') }}" required>
        </div>
    </div>

    {{-- Phone --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('vendor::vendors.phone') }}</label>
            <input type="text" class="form-control" name="phone" id="edit_phone"
                value="{{ old('phone', $vendor->phone ?? '') }}"
                placeholder="{{ trans('vendor::vendors.phone_placeholder') }}">
        </div>
    </div>

    {{-- Password --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('vendor::vendors.password') }}
                @if(!isset($vendor)) <span class="text-danger">*</span> @endif
            </label>
            <input type="password" class="form-control" name="password" id="edit_password"
                placeholder="{{ isset($vendor) ? trans('vendor::vendors.password_leave_blank') : trans('dashboard/vendors.password_placeholder') }}"
                @if(!isset($vendor)) required @endif>
            @if(isset($vendor))
            <small class="text-muted">{{ trans('vendor::vendors.password_leave_blank') }}</small>
            @endif
        </div>
    </div>

    {{-- Status --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('vendor::vendors.status') }} <span class="text-danger">*</span></label>
            <select class="form-select" name="status" id="edit_status" required>
                <option value="active" {{ old('status', $vendor->status ?? '') == 'active' ? 'selected' : '' }}>
                    {{ trans('vendor::vendors.status_active') }}
                </option>
                <option value="inactive" {{ old('status', $vendor->status ?? '') == 'inactive' ? 'selected' : '' }}>
                    {{ trans('vendor::vendors.status_inactive') }}
                </option>
                <option value="pending" {{ old('status', $vendor->status ?? '') == 'pending' ? 'selected' : '' }}>
                    {{ trans('vendor::vendors.status_pending') }}
                </option>
                <option value="suspended" {{ old('status', $vendor->status ?? '') == 'suspended' ? 'selected' : '' }}>
                    {{ trans('vendor::vendors.status_suspended') }}
                </option>
            </select>
        </div>
    </div>

    {{-- Type --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('vendor::vendors.type') }}</label>
            <select class="form-select" name="type" id="edit_type">
                <option value="">{{ trans('dashboard/general.select') }}</option>
                <option value="individual" {{ old('type', $vendor->type ?? '') == 'individual' ? 'selected' : '' }}>
                    {{ trans('vendor::vendors.type_individual') }}
                </option>
                <option value="company" {{ old('type', $vendor->type ?? '') == 'company' ? 'selected' : '' }}>
                    {{ trans('dashboard/vendors.type_company') }}
                </option>
                <option value="government" {{ old('type', $vendor->type ?? '') == 'government' ? 'selected' : '' }}>
                    {{ trans('vendor::vendors.type_government') }}
                </option>
            </select>
        </div>
    </div>

    {{-- Date --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('vendor::vendors.date') }}</label>
            <input type="date" class="form-control" name="date" id="edit_date"
                value="{{ old('date', isset($vendor) && $vendor->date ? $vendor->date->format('Y-m-d') : '') }}">
        </div>
    </div>

    {{-- Company --}}
    @ownerOnly
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('vendor::vendors.company') }}</label>
            <select class="form-select" name="company_id" id="edit_company_id">
                <option value="">{{ trans('dashboard/general.select') }}</option>
                @foreach($companies ?? [] as $company)
                <option value="{{ $company->id }}" {{ old('company_id', $vendor->company_id ?? '') == $company->id ?
                    'selected' : '' }}>
                    {{ $company->name }}
                </option>
                @endforeach
            </select>
        </div>
    </div>
    @endOwnerOnly

    {{-- Department --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('vendor::vendors.department') }}</label>
            <select class="form-select" name="department_id" id="edit_department_id">
                <option value="">{{ trans('dashboard/general.select') }}</option>
                @foreach($departments ?? [] as $department)
                <option value="{{ $department->id }}" {{ old('department_id', $vendor->department_id ?? '') ==
                    $department->id ? 'selected' : '' }}>
                    {{ $department->name }}
                </option>
                @endforeach
            </select>
        </div>
    </div>
</div>