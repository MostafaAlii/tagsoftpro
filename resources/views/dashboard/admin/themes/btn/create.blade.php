<div class="modal fade" id="createThemeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('dashboard/themes.create') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.themes.store') }}" method="POST" id="createForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Basic Info --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/themes.name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/themes.code') }} <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/themes.description') }}</label>
                                <textarea name="description" class="form-control" rows="2"></textarea>
                            </div>
                        </div>

                        {{-- Paid Type --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/themes.paid_type') }} <span class="text-danger">*</span></label>
                                <select name="paid_type" class="form-select" id="create_paid_type" required>
                                    <option value="free">{{ trans('dashboard/themes.paid_type_free') }}</option>
                                    <option value="paid">{{ trans('dashboard/themes.paid_type_paid') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3" id="create_price_wrapper" style="display: none;">
                                <label class="form-label">{{ trans('dashboard/themes.price') }} <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control" step="0.01" min="0">
                            </div>
                        </div>

                        {{-- Company --}}
                        @ownerOnly
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('dashboard/themes.company') }}</label>
                                <select name="company_id" class="form-select">
                                    <option value="">{{ trans('dashboard/themes.select_company') }}</option>
                                    @foreach($companies ?? [] as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endOwnerOnly

                        {{-- ✅ Project Types with Status & Default --}}
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <strong>{{ trans('dashboard/themes.project_types_config') }}</strong>
                                </div>
                                <div class="card-body">
                                    @foreach($projectTypes as $type)
                                    <div class="row mb-3 align-items-center border-bottom pb-2">
                                        <div class="col-md-4">
                                            <strong>{{ $type->getTranslatedName() }}</strong>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check form-switch">
                                                <input type="hidden" name="project_types[{{ $type->id }}][is_active]" value="0">
                                                <input class="form-check-input" type="checkbox" 
                                                    name="project_types[{{ $type->id }}][is_active]" 
                                                    value="1" checked>
                                                <label class="form-check-label">{{ trans('dashboard/themes.active') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check form-switch">
                                                <input type="hidden" name="project_types[{{ $type->id }}][is_default]" value="0">
                                                <input class="form-check-input toggle-default-per-type" 
                                                    type="checkbox" 
                                                    name="project_types[{{ $type->id }}][is_default]" 
                                                    value="1"
                                                    data-type-id="{{ $type->id }}">
                                                <label class="form-check-label">{{ trans('dashboard/themes.default') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('dashboard/general.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ trans('dashboard/general.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// ✅ منع اختيار أكثر من Default لكل Project Type
document.querySelectorAll('.toggle-default-per-type').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const typeId = this.dataset.typeId;
        const allCheckboxes = document.querySelectorAll(`.toggle-default-per-type[data-type-id="${typeId}"]`);
        allCheckboxes.forEach(cb => {
            if (cb !== this) {
                cb.checked = false;
            }
        });
    });
});

// ✅ التحكم في ظهور حقل السعر
document.getElementById('create_paid_type')?.addEventListener('change', function() {
    const priceWrapper = document.getElementById('create_price_wrapper');
    priceWrapper.style.display = this.value === 'paid' ? 'block' : 'none';
});
</script>