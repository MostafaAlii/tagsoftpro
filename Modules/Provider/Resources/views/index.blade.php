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
        <h1 class="mb-0">{{ trans('provider::providers.providers') }}</h1>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">{{ trans('provider::providers.dashboard') }}</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.providers.index') }}">{{ trans('provider::providers.providers') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <span class="nav-icon">
                        <i class="ti ti-building"></i>
                    </span>
                    {{ trans('provider::providers.providers') }}
                    <div class="gap-2 d-flex">
                        <button data-pc-animate="3d-sign" type="button" class="btn btn-sm btn-light btn-active-primary"
                            data-bs-toggle="modal" data-bs-target="#createProviderModal">
                            <i class="fa fa-plus"></i>
                            {{ trans('provider::providers.create') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-danger btn-active-primary" id="toggleTrashed"
                            style="display: none;">
                            <i class="ti ti-trash me-1"></i>
                            <span id="trashedBtnText">{{ trans('provider::providers.show_trashed') }}</span>
                        </button>
                    </div>
                    @include('provider::btn.create', compact('zones'))
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

@include('provider::btn.modals', compact('zones'))

@push('js')
{!! $dataTable->scripts() !!}
<script>
    window.translations = {
        error: "{{ trans('provider::providers.error_occurred') }}",
        restore: "{{ trans('provider::providers.restore') }}",
        restore_confirm: "{{ trans('provider::providers.restore_confirm') }}",
        force_delete: "{{ trans('provider::providers.force_delete') }}",
        force_delete_confirm: "{{ trans('provider::providers.force_delete_confirm') }}",
        show_active: "{{ trans('provider::providers.show_active') }}",
        show_trashed: "{{ trans('provider::providers.show_trashed') }}",
        bulk_select_at_least_one: "{{ trans('provider::providers.bulk_select_at_least_one') }}",
        bulk_status_confirm: "{{ trans('provider::providers.bulk_status_confirm') }}",
        bulk_delete_confirm: "{{ trans('provider::providers.bulk_delete_confirm') }}",
        bulk_change_status: "{{ trans('provider::providers.bulk_change_status') }}",
        delete_selected: "{{ trans('provider::providers.delete_selected') }}",
        confirm: "{{ trans('provider::providers.confirm') }}",
        delete: "{{ trans('provider::providers.delete') }}",
        bulk_actions: "{{ trans('provider::providers.bulk_actions') }}",
        bulk_restore: "{{ trans('provider::providers.bulk_restore') }}",
        bulk_force_delete: "{{ trans('provider::providers.bulk_force_delete') }}",
        bulk_restore_confirm: "{{ trans('provider::providers.bulk_restore_confirm') }}",
        bulk_force_delete_confirm: "{{ trans('provider::providers.bulk_force_delete_confirm') }}",
    };

    window.routes = {
        index: "{{ route('admin.providers.index') }}",
        edit: "{{ route('admin.providers.edit', ['provider' => '__ID__']) }}",
        update: "{{ route('admin.providers.update', ['provider' => '__ID__']) }}",
        destroy: "{{ route('admin.providers.destroy', ['provider' => '__ID__']) }}",
        restore: "{{ route('admin.providers.restore', ['provider' => '__ID__']) }}",
        forceDelete: "{{ route('admin.providers.forceDelete', ['provider' => '__ID__']) }}",
        hasTrashed: "{{ route('admin.providers.hasTrashed') }}",
        bulkAction: "{{ route('admin.providers.bulkAction') }}",
        toggleStatus: "{{ route('admin.providers.toggleStatus', ['provider' => '__ID__']) }}",
    };
    window.showTrashed = false;
</script>

<script src="{{ asset('dashboard/assets/js/custom/utils/alert.js') }}"></script>
<script src="{{ route('provider.assets', 'index.js') }}?v={{ time() }}"></script>
<script src="{{ route('provider.assets', 'bulk_actions.js') }}?v={{ time() }}"></script>
<script src="{{ route('provider.assets', 'trashed_manager.js') }}?v={{ time() }}"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof window.Providers !== 'undefined' && window.Providers.init) {
            window.Providers.init();
        }
        setTimeout(function() {
            if (typeof window.checkTrashed === 'function') {
                window.checkTrashed();
            }
        }, 500);
    });
</script>
@endpush