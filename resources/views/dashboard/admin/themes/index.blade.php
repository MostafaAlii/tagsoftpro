@extends('dashboard.layouts.master')
@section('css')
@endsection

@section('title')
{{ $title }}
@endsection

@section('content')
<div class="page-content">
    <div class="content-header">
        <h1 class="mb-0">{{ trans('dashboard/themes.themes') }}</h1>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">{{ trans('dashboard/header.main_dashboard') }}</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.themes.index') }}">{{ trans('dashboard/themes.themes') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <span class="nav-icon">
                        <i class="ti ti-palette"></i>
                    </span>
                    {{ trans('dashboard/themes.themes') }}
                    <button data-pc-animate="3d-sign" type="button" class="btn btn-sm btn-light btn-active-primary"
                        data-bs-toggle="modal" data-bs-target="#createThemeModal">
                        <i class="fa fa-plus"></i>
                        {{ trans('dashboard/themes.create') }}
                    </button>

                    @include('dashboard.admin.themes.btn.create', compact('companies'))
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
        active: "{{ trans('dashboard/general.active') }}",
        inactive: "{{ trans('dashboard/general.in_active') }}",
        default: "{{ trans('dashboard/themes.default') }}",
        normal: "{{ trans('dashboard/themes.normal') }}",
        already_default: "{{ trans('dashboard/themes.already_default') }}",
        default_updated: "{{ trans('dashboard/themes.default_updated') }}",
        default_status_for: "{{ trans('dashboard/themes.default_status_for') }}",
        active_status_for: "{{ trans('dashboard/themes.active_status_for') }}",
    };
    window.routes = {
        edit: "{{ route('admin.themes.edit', ['theme' => '__ID__']) }}",
        update: "{{ route('admin.themes.update', ['theme' => '__ID__']) }}",
        destroy: "{{ route('admin.themes.destroy', ['theme' => '__ID__']) }}",
        statuses: "{{ route('admin.themes.statuses', ['theme' => '__ID__']) }}",
        toggleDefault: "{{ route('admin.themes.toggleDefault', ['theme' => '__ID__', 'projectType' => '__TYPE_ID__']) }}",
        toggleStatus: "{{ route('admin.themes.toggleStatus', ['theme' => '__ID__', 'projectType' => '__TYPE_ID__']) }}",
        bulkUpdate: "{{ route('admin.themes.bulkUpdate', ['theme' => '__ID__']) }}",
    };
</script>
<script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/custom/utils/alert.js') }}"></script>
<script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/custom/admin/themes/index.js') }}?v={{ time() }}"></script>
@endpush