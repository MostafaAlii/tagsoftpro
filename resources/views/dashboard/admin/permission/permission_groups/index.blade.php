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
        <h1 class="mb-0">{{ trans('dashboard/permission_groups.permission_groups') }}</h1>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">{{ trans('dashboard/header.main_dashboard') }}</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.permission_groups.index') }}">{{trans('dashboard/permission_groups.permission_groups') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <span class="nav-icon">
                        <i class="ti ti-lock"></i>
                    </span>
                    {{ trans('dashboard/permission_groups.permission_groups') }}
                    <div class="gap-2 d-flex">
                        <button data-pc-animate="3d-sign" type="button" class="btn btn-sm btn-light btn-active-primary" data-bs-toggle="modal" data-bs-target="#createPermissionGroupModal">
                            <i class="fa fa-plus"></i>
                            {{ trans('dashboard/permission_groups.create') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-danger btn-active-primary" id="toggleTrashed" style="display: none;">
                            <i class="ti ti-trash me-1"></i>
                            <span id="trashedBtnText">{{ trans('dashboard/permission_groups.show_trashed') }}</span>
                        </button>
                    </div>
                    @include('dashboard.admin.permission.permission_groups.btn.create', compact('companies', 'locales'))
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
        restore: "{{ trans('dashboard/permission_groups.restore') }}",
        restore_confirm: "{{ trans('dashboard/permission_groups.restore_confirm') }}",
        force_delete: "{{ trans('dashboard/permission_groups.force_delete') }}",
        force_delete_confirm: "{{ trans('dashboard/permission_groups.force_delete_confirm') }}",
        show_active: "{{ trans('dashboard/permission_groups.show_active') }}",
        show_trashed: "{{ trans('dashboard/permission_groups.show_trashed') }}",
        bulk_select_at_least_one: "{{ trans('dashboard/permission_groups.bulk_select_at_least_one') }}",
        bulk_status_confirm: "{{ trans('dashboard/permission_groups.bulk_status_confirm') }}",
        bulk_delete_confirm: "{{ trans('dashboard/permission_groups.bulk_delete_confirm') }}",
        bulk_change_status: "{{ trans('dashboard/permission_groups.bulk_change_status') }}",
        delete_selected: "{{ trans('dashboard/general.delete_selected') }}",
        confirm: "{{ trans('dashboard/general.confirm') }}",
        delete: "{{ trans('dashboard/general.delete') }}",
        bulk_actions: "{{ trans('dashboard/permission_groups.bulk_actions') }}",
        bulk_restore: "{{ trans('dashboard/permission_groups.bulk_restore') }}",
        bulk_force_delete: "{{ trans('dashboard/permission_groups.bulk_force_delete') }}",
        bulk_restore_confirm: "{{ trans('dashboard/permission_groups.bulk_restore_confirm') }}",
        bulk_force_delete_confirm: "{{ trans('dashboard/permission_groups.bulk_force_delete_confirm') }}",
    };

    window.routes = {
        index: "{{ route('admin.permission_groups.index') }}",
        edit: "{{ route('admin.permission_groups.edit', ['permission_group' => '__ID__']) }}",
        update: "{{ route('admin.permission_groups.update', ['permission_group' => '__ID__']) }}",
        destroy: "{{ route('admin.permission_groups.destroy', ['permission_group' => '__ID__']) }}",
        restore: "{{ route('admin.permission_groups.restore', ['permission_group' => '__ID__']) }}",
        forceDelete: "{{ route('admin.permission_groups.forceDelete', ['permission_group' => '__ID__']) }}",
        hasTrashed: "{{ route('admin.permission_groups.hasTrashed') }}",
        bulkAction: "{{ route('admin.permission_groups.bulkAction') }}",
    };
    window.showTrashed = false;
</script>

<script src="{{ asset('dashboard/assets/js/custom/utils/alert.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/custom/admin/permission/permission_groups/index.js') }}?v={{ time() }}"></script>
<script src="{{ asset('dashboard/assets/js/custom/admin/permission/permission_groups/bulk_actions.js') }}?v={{ time() }}"></script>
<script src="{{ asset('dashboard/assets/js/custom/admin/permission/permission_groups/trashed_manager.js') }}?v={{ time() }}">
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof window.PermissionGroups !== 'undefined' && window.PermissionGroups.init) {
            window.PermissionGroups.init();
        }
        setTimeout(function() {
            if (typeof window.checkTrashed === 'function') {
                window.checkTrashed();
            }
        }, 500);
    });
</script>
@endpush