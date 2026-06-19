@extends('dashboard.layouts.master')
@push('css')
<style>
    .dt-button-collection {
        border-radius: 10px !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15) !important;
        border: 1px solid #e0e0e0 !important;
        min-width: 200px !important;
        z-index: 99999 !important;
        position: absolute !important;
        margin-top: 5px !important;
    }

    .dt-buttons {
        position: relative !important;
    }

    .card-header,
    .card-body {
        overflow: visible !important;
    }

    .table-responsive {
        overflow-x: auto !important;
    }

    /* عربي */
    [dir="rtl"] .dt-button-collection {
        right: 0 !important;
        left: auto !important;
    }

    /* انجليزي - سواء مفيش dir أو ltr */
    html:not([dir="rtl"]) .dt-button-collection {
        left: 0 !important;
        right: auto !important;
    }
</style>
@endpush

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
                        <button type="button" class="btn btn-sm btn-danger btn-active-primary" id="toggleTrashed"
                            style="display: none;">
                            <i class="ti ti-trash me-1"></i>
                            <span id="trashedBtnText">{{ trans('dashboard/employees.show_trashed') }}</span>
                        </button>
                    </div>

                    @include('dashboard.admin.employees.btn.create', compact('companies', 'departments'))
                </div>
                <div class="card-body">
                    <div class="table-responsive w-100">
                        <table class="table table-striped table-row-bordered gy-3 gs-3 table-hover align-middle fs-7">
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
        bulk_select_at_least_one: "{{ trans('dashboard/employees.bulk_select_at_least_one') }}",
        bulk_status_confirm: "{{ trans('dashboard/employees.bulk_status_confirm') }}",
        bulk_delete_confirm: "{{ trans('dashboard/employees.bulk_delete_confirm') }}",
        bulk_change_status: "{{ trans('dashboard/employees.bulk_change_status') }}",
        delete_selected: "{{ trans('dashboard/general.delete_selected') }}",
        confirm: "{{ trans('dashboard/general.confirm') }}",
        delete: "{{ trans('dashboard/general.delete') }}",
        bulk_actions: "{{ trans('dashboard/employees.bulk_actions') }}",
        bulk_restore: "{{ trans('dashboard/employees.bulk_restore') }}",
        bulk_force_delete: "{{ trans('dashboard/employees.bulk_force_delete') }}",
        bulk_restore_confirm: "{{ trans('dashboard/employees.bulk_restore_confirm') }}",
        bulk_force_delete_confirm: "{{ trans('dashboard/employees.bulk_force_delete_confirm') }}",
    };

    window.routes = {
        index: "{{ route('admin.employees.index') }}",
        edit: "{{ route('admin.employees.edit', ['employee' => '__ID__']) }}",
        update: "{{ route('admin.employees.update', ['employee' => '__ID__']) }}",
        destroy: "{{ route('admin.employees.destroy', ['employee' => '__ID__']) }}",
        restore: "{{ route('admin.employees.restore', ['employee' => '__ID__']) }}",
        forceDelete: "{{ route('admin.employees.forceDelete', ['employee' => '__ID__']) }}",
        hasTrashed: "{{ route('admin.employees.hasTrashed') }}",
        bulkAction: "{{ route('admin.employees.bulkAction') }}",
    };

    window.showTrashed = false;
</script>

<script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/custom/utils/alert.js') }}"></script>
<script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/custom/admin/employees/index.js') }}?v={{ time() }}">
</script>
<script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/custom/admin/employees/bulk_actions.js') }}?v={{ time() }}"></script>
<script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/custom/admin/employees/trashed_manager.js') }}?v={{ time() }}"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof window.Employees !== 'undefined' && window.Employees.init) {
            window.Employees.init();
        }
        setTimeout(function() {
            if (typeof window.checkTrashed === 'function') {
                window.checkTrashed();
            }
        }, 500);
    });
</script>
@endpush
