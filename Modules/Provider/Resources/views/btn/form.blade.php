<div class="row g-3">
    {{-- Name --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('provider::providers.name') }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" id="edit_name"
                value="{{ old('name', $provider->name ?? '') }}"
                placeholder="{{ trans('provider::providers.name_placeholder') }}" required>
        </div>
    </div>

    {{-- Email --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('provider::providers.email') }} <span
                    class="text-danger">*</span></label>
            <input type="email" class="form-control" name="email" id="edit_email"
                value="{{ old('email', $provider->email ?? '') }}"
                placeholder="{{ trans('provider::providers.email_placeholder') }}" required>
        </div>
    </div>

    {{-- Phone --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('provider::providers.phone') }}</label>
            <input type="text" class="form-control" name="phone" id="edit_phone"
                value="{{ old('phone', $provider->phone ?? '') }}"
                placeholder="{{ trans('provider::providers.phone_placeholder') }}">
        </div>
    </div>

    {{-- Password --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('provider::providers.password') }}
                @if(!isset($provider)) <span class="text-danger">*</span> @endif
            </label>
            <input type="password" class="form-control" name="password" id="edit_password"
                placeholder="{{ isset($provider) ? trans('provider::providers.password_leave_blank') : trans('provider::providers.password_placeholder') }}"
                @if(!isset($provider)) required @endif>
            @if(isset($provider))
            <small class="text-muted">{{ trans('provider::providers.password_leave_blank') }}</small>
            @endif
        </div>
    </div>

    {{-- Status --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('provider::providers.status') }} <span
                    class="text-danger">*</span></label>
            <select class="form-select" name="status" id="edit_status" required>
                <option value="active" {{ old('status', $provider->status ?? '') == 'active' ? 'selected' : '' }}>
                    {{ trans('provider::providers.status_active') }}
                </option>
                <option value="inactive" {{ old('status', $provider->status ?? '') == 'inactive' ? 'selected' : '' }}>
                    {{ trans('provider::providers.status_inactive') }}
                </option>
                <option value="pending" {{ old('status', $provider->status ?? '') == 'pending' ? 'selected' : '' }}>
                    {{ trans('provider::providers.status_pending') }}
                </option>
                <option value="suspended" {{ old('status', $provider->status ?? '') == 'suspended' ? 'selected' : '' }}>
                    {{ trans('provider::providers.status_suspended') }}
                </option>
            </select>
        </div>
    </div>

    {{-- Date --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('provider::providers.date') }}</label>
            <input type="date" class="form-control" name="date" id="edit_date"
                value="{{ old('date', isset($provider) && $provider->date ? $provider->date->format('Y-m-d') : '') }}">
        </div>
    </div>

    {{-- Address --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('provider::providers.address') }}</label>
            <input type="text" class="form-control" name="address" id="edit_address"
                value="{{ old('address', $provider->address ?? '') }}"
                placeholder="{{ trans('provider::providers.address_placeholder') }}">
        </div>
    </div>

    {{-- Website --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">{{ trans('provider::providers.website') }}</label>
            <input type="url" class="form-control" name="website" id="edit_website"
                value="{{ old('website', $provider->website ?? '') }}"
                placeholder="{{ trans('provider::providers.website_placeholder') }}">
        </div>
    </div>

    {{-- Zone --}}
    <div class="col-md-12">
        <div class="mb-3">
            <label class="form-label">{{ trans('provider::providers.zone') }}</label>
            <select class="form-select" name="zone_id" id="edit_zone_id">
                <option value="">{{ trans('provider::providers.select_zone') }}</option>
                @foreach($zones ?? [] as $zone)
                <option value="{{ $zone->id }}" {{ old('zone_id', $provider->zone_id ?? '') == $zone->id ? 'selected' :
                    '' }}>
                    {{ $zone->name }}
                </option>
                @endforeach
            </select>
        </div>
    </div>
</div>