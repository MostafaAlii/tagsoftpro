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

    .dataTables_wrapper,
    .card-header,
    .card,
    .table-responsive {
        overflow: visible !important;
    }
</style>
@endpush

@section('title')
{{ $title }}
@endsection

@section('content')
<div class="page-content">
    <div class="content-header">
        <h1 class="mb-0">{{ trans('vendor::vendors.vendors') }}</h1>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">{{ trans('dashboard/header.main_dashboard') }}</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.vendors.index') }}">{{ trans('vendor::vendors.vendors') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <span class="nav-icon">
                        <i class="ti ti-building-store"></i>
                    </span>
                    {{ trans('vendor::vendors.vendors') }}
                    <div class="gap-2 d-flex">
                        <button data-pc-animate="3d-sign" type="button" class="btn btn-sm btn-light btn-active-primary"
                            data-bs-toggle="modal" data-bs-target="#createVendorModal">
                            <i class="fa fa-plus"></i>
                            {{ trans('vendor::vendors.create') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-danger btn-active-primary" id="toggleTrashed"
                            style="display: none;">
                            <i class="ti ti-trash me-1"></i>
                            <span id="trashedBtnText">{{ trans('vendor::vendors.show_trashed') }}</span>
                        </button>
                    </div>
                    @include('vendor::btn.create', compact('companies', 'departments'))
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

@include('vendor::btn.modals', ['companies' => $companies, 'departments' => $departments])

@push('js')
{!! $dataTable->scripts() !!}
<script>
    window.translations = {
        error: "{{ trans('dashboard/general.error_occurred') }}",
        restore: "{{ trans('vendor::vendors.restore') }}",
        restore_confirm: "{{ trans('vendor::vendors.restore_confirm') }}",
        force_delete: "{{ trans('vendor::vendors.force_delete') }}",
        force_delete_confirm: "{{ trans('vendor::vendors.force_delete_confirm') }}",
        show_active: "{{ trans('vendor::vendors.show_active') }}",
        show_trashed: "{{ trans('vendor::vendors.show_trashed') }}",
        bulk_select_at_least_one: "{{ trans('vendor::vendors.bulk_select_at_least_one') }}",
        bulk_status_confirm: "{{ trans('vendor::vendors.bulk_status_confirm') }}",
        bulk_delete_confirm: "{{ trans('vendor::vendors.bulk_delete_confirm') }}",
        bulk_change_status: "{{ trans('vendor::vendors.bulk_change_status') }}",
        delete_selected: "{{ trans('dashboard/general.delete_selected') }}",
        confirm: "{{ trans('dashboard/general.confirm') }}",
        delete: "{{ trans('dashboard/general.delete') }}",
        bulk_actions: "{{ trans('vendor::vendors.bulk_actions') }}",
        bulk_restore: "{{ trans('vendor::vendors.bulk_restore') }}",
        bulk_force_delete: "{{ trans('vendor::vendors.bulk_force_delete') }}",
        bulk_restore_confirm: "{{ trans('vendor::vendors.bulk_restore_confirm') }}",
        bulk_force_delete_confirm: "{{ trans('vendor::vendors.bulk_force_delete_confirm') }}",
    };

    window.routes = {
        index: "{{ route('admin.vendors.index') }}",
        edit: "{{ route('admin.vendors.edit', ['vendor' => '__ID__']) }}",
        update: "{{ route('admin.vendors.update', ['vendor' => '__ID__']) }}",
        destroy: "{{ route('admin.vendors.destroy', ['vendor' => '__ID__']) }}",
        restore: "{{ route('admin.vendors.restore', ['vendor' => '__ID__']) }}",
        forceDelete: "{{ route('admin.vendors.forceDelete', ['vendor' => '__ID__']) }}",
        hasTrashed: "{{ route('admin.vendors.hasTrashed') }}",
        bulkAction: "{{ route('admin.vendors.bulkAction') }}",
        toggleStatus: "{{ route('admin.vendors.toggleStatus', ['vendor' => '__ID__']) }}",
    };
    window.showTrashed = false;
</script>

<script src="{{ asset('dashboard/assets/js/custom/utils/alert.js') }}"></script>
<script src="{{ route('vendor.assets', 'index.js') }}?v={{ time() }}"></script>
<script src="{{ route('vendor.assets', 'bulk_actions.js') }}?v={{ time() }}"></script>
<script src="{{ route('vendor.assets', 'trashed_manager.js') }}?v={{ time() }}"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof window.Vendors !== 'undefined' && window.Vendors.init) {
            window.Vendors.init();
        }
        setTimeout(function() {
            if (typeof window.checkTrashed === 'function') {
                window.checkTrashed();
            }
        }, 500);
    });
</script>
@endpush