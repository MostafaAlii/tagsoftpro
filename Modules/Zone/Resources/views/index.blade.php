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
        <h1 class="mb-0">{{ trans('zone::zones.zones') }}</h1>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">{{ trans('zone::zones.dashboard') }}</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.zones.index') }}">{{ trans('zone::zones.zones') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <span class="nav-icon">
                        <i class="ti ti-map-pin"></i>
                    </span>
                    {{ trans('zone::zones.zones') }}
                    <div class="gap-2 d-flex">
                        <button data-pc-animate="3d-sign" type="button" class="btn btn-sm btn-light btn-active-primary"
                            data-bs-toggle="modal" data-bs-target="#createZoneModal">
                            <i class="fa fa-plus"></i>
                            {{ trans('zone::zones.create') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-danger btn-active-primary" id="toggleTrashed"
                            style="display: none;">
                            <i class="ti ti-trash me-1"></i>
                            <span id="trashedBtnText">{{ trans('zone::zones.show_trashed') }}</span>
                        </button>
                    </div>
                    @include('zone::btn.create', compact('locales'))
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

@include('zone::btn.modals', compact('locales'))

@push('js')
{!! $dataTable->scripts() !!}
<script>
    window.translations = {
        error: "{{ trans('zone::zones.error_occurred') }}",
        restore: "{{ trans('zone::zones.restore') }}",
        restore_confirm: "{{ trans('zone::zones.restore_confirm') }}",
        force_delete: "{{ trans('zone::zones.force_delete') }}",
        force_delete_confirm: "{{ trans('zone::zones.force_delete_confirm') }}",
        show_active: "{{ trans('zone::zones.show_active') }}",
        show_trashed: "{{ trans('zone::zones.show_trashed') }}",
        bulk_select_at_least_one: "{{ trans('zone::zones.bulk_select_at_least_one') }}",
        bulk_status_confirm: "{{ trans('zone::zones.bulk_status_confirm') }}",
        bulk_delete_confirm: "{{ trans('zone::zones.bulk_delete_confirm') }}",
        bulk_change_status: "{{ trans('zone::zones.bulk_change_status') }}",
        delete_selected: "{{ trans('zone::zones.delete_selected') }}",
        confirm: "{{ trans('zone::zones.confirm') }}",
        delete: "{{ trans('zone::zones.delete') }}",
        bulk_actions: "{{ trans('zone::zones.bulk_actions') }}",
        bulk_restore: "{{ trans('zone::zones.bulk_restore') }}",
        bulk_force_delete: "{{ trans('zone::zones.bulk_force_delete') }}",
        bulk_restore_confirm: "{{ trans('zone::zones.bulk_restore_confirm') }}",
        bulk_force_delete_confirm: "{{ trans('zone::zones.bulk_force_delete_confirm') }}",
    };

    window.routes = {
        index: "{{ route('admin.zones.index') }}",
        edit: "{{ route('admin.zones.edit', ['zone' => '__ID__']) }}",
        update: "{{ route('admin.zones.update', ['zone' => '__ID__']) }}",
        destroy: "{{ route('admin.zones.destroy', ['zone' => '__ID__']) }}",
        restore: "{{ route('admin.zones.restore', ['zone' => '__ID__']) }}",
        forceDelete: "{{ route('admin.zones.forceDelete', ['zone' => '__ID__']) }}",
        hasTrashed: "{{ route('admin.zones.hasTrashed') }}",
        bulkAction: "{{ route('admin.zones.bulkAction') }}",
        toggleStatus: "{{ route('admin.zones.toggleStatus', ['zone' => '__ID__']) }}",
    };
    window.showTrashed = false;
</script>

<script src="{{ asset('dashboard/assets/js/custom/utils/alert.js') }}"></script>
<script src="{{ route('zone.assets', 'index.js') }}?v={{ time() }}"></script>
<script src="{{ route('zone.assets', 'bulk_actions.js') }}?v={{ time() }}"></script>
<script src="{{ route('zone.assets', 'trashed_manager.js') }}?v={{ time() }}"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof window.Zones !== 'undefined' && window.Zones.init) {
            window.Zones.init();
        }
        setTimeout(function() {
            if (typeof window.checkTrashed === 'function') {
                window.checkTrashed();
            }
        }, 500);
    });
</script>
@endpush