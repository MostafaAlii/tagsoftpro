@extends('dashboard.layouts.master')
@section('css')
@endsection

@section('title')
{{ $title }}
@endsection

@section('content')
<div class="page-content">
    <div class="content-header">
        <h1 class="mb-0">{{ trans('dashboard/employees.employees') }}</h1>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">{{ trans('dashboard/header.main_dashboard') }}</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.employees.index') }}">{{ trans('dashboard/employees.employees') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <span class="nav-icon">
                        <i class="ti ti-users"></i>
                    </span>
                    {{ trans('dashboard/employees.employees') }}
                    <div class="gap-2 d-flex">

                        <button data-pc-animate="3d-sign" type="button" class="btn btn-sm btn-light btn-active-primary"
                            data-bs-toggle="modal" data-bs-target="#createEmployeeModal">
                            <i class="fa fa-plus"></i>
                            {{ trans('dashboard/employees.create') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-danger btn-active-primary" id="toggleTrashed" style="display: none;">
                            <i class="ti ti-trash me-1"></i>
                            <span id="trashedBtnText">{{ trans('dashboard/employees.show_trashed') }}</span>
                        </button>
                    </div>

                    @include('dashboard.admin.employees.btn.create', compact('companies', 'departments'))
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-row-bordered gy-5 gs-7">
                            {!! $dataTable->table() !!}
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
{!! $dataTable->scripts() !!}
<script>
    window.translations = {
        error: "{{ trans('dashboard/general.error_occurred') }}",
        restore: "{{ trans('dashboard/employees.restore') }}",
        restore_confirm: "{{ trans('dashboard/employees.restore_confirm') }}",
        force_delete: "{{ trans('dashboard/employees.force_delete') }}",
        force_delete_confirm: "{{ trans('dashboard/employees.force_delete_confirm') }}",
        show_active: "{{ trans('dashboard/employees.show_active') }}",
        show_trashed: "{{ trans('dashboard/employees.show_trashed') }}",
    };
    window.routes = {
        index: "{{ route('admin.employees.index') }}",
        edit: "{{ route('admin.employees.edit', ['employee' => '__ID__']) }}",
        update: "{{ route('admin.employees.update', ['employee' => '__ID__']) }}",
        destroy: "{{ route('admin.employees.destroy', ['employee' => '__ID__']) }}",
        restore: "{{ route('admin.employees.restore', ['employee' => '__ID__']) }}",
        forceDelete: "{{ route('admin.employees.forceDelete', ['employee' => '__ID__']) }}",
        hasTrashed: "{{ route('admin.employees.hasTrashed') }}",
    };
    window.showTrashed = false;
    document.getElementById('toggleTrashed')?.addEventListener('click', function() {
        showTrashed = !showTrashed;
        const btnText = document.getElementById('trashedBtnText');
        const table = window.LaravelDataTables['employees_datatable'];
        if (showTrashed) {
            btnText.textContent = window.translations.show_active;
            table.ajax.url(window.routes.index + '?show_trashed=true').load();
        } else {
            btnText.textContent = window.translations.show_trashed;
            table.ajax.url(window.routes.index + '?show_trashed=false').load();
        }
    });

    // ─── Confirm Modal ──────────────────────────────────────────────────────────
    let confirmCallback = null;
    const showConfirmModal = (title, message, confirmText = 'تأكيد', callback) => {
        document.getElementById('confirmActionTitle').textContent = title;
        document.getElementById('confirmActionBody').textContent = message;
        const confirmBtn = document.getElementById('confirmActionBtn');
        confirmBtn.querySelector('.indicator-label').textContent = confirmText;
        confirmBtn.classList.remove('btn-success', 'btn-danger', 'btn-primary');
        confirmBtn.classList.add('btn-danger');
        confirmCallback = callback;
        const modal = new bootstrap.Modal(document.getElementById('confirmActionModal'));
        modal.show();
    };
    // ─── Handle Confirm Button Click ───────────────────────────────────────────
    document.addEventListener('click', async function(e) {
        const confirmBtn = e.target.closest('#confirmActionBtn');
        if (!confirmBtn) return;
        if (typeof confirmCallback === 'function') {
            setBtnLoading(confirmBtn, true);
            await confirmCallback(confirmBtn);
            setBtnLoading(confirmBtn, false);
        }
        const modal = bootstrap.Modal.getInstance(document.getElementById('confirmActionModal'));
            if (modal) modal.hide();
            confirmCallback = null;
    });
</script>
<script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/custom/utils/alert.js') }}"></script>
<script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/custom/admin/employees/index.js') }}?v={{ time() }}"></script>
@endpush
