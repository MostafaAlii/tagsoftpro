@extends('dashboard.layouts.master')
@section('css')
@endsection

@section('title')
{{ $title }}
@endsection

@section('content')
<div class="page-content">
    <div class="content-header">
        <h1 class="mb-0">{{ trans('dashboard/project_types.project_types') }}</h1>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">{{ trans('dashboard/header.main_dashboard') }}</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.projectTypes.index') }}">{{ trans('dashboard/project_types.project_types')
                    }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <span class="nav-icon">
                        <i class="ti ti-folder"></i>
                    </span>
                    {{ trans('dashboard/project_types.project_types') }}
                    <button data-pc-animate="3d-sign" type="button" class="btn btn-sm btn-light btn-active-primary"
                        data-bs-toggle="modal" data-bs-target="#createProjectTypeModal">
                        <i class="fa fa-plus"></i>
                        {{ trans('dashboard/project_types.create') }}
                    </button>

                    @php
                    $locales = array_keys(config('laravellocalization.supportedLocales'));
                    $currentLocale = app()->getLocale();
                    @endphp
                    @include('dashboard.admin.projectTypes.btn.create', compact('locales', 'currentLocale'))
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
        error:    "{{ trans('dashboard/general.error_occurred') }}",
        active:   "{{ trans('dashboard/general.active') }}",
        inactive: "{{ trans('dashboard/general.in_active') }}",
    };
    window.locales = @json(array_keys(config('laravellocalization.supportedLocales')));
    window.routes = {
        update: "{{ route('admin.projectTypes.update', ['projectType' => '__ID__']) }}",
        edit: "{{ route('admin.projectTypes.edit', ['projectType' => '__ID__']) }}",
        destroy: "{{ route('admin.projectTypes.destroy', ['projectType' => '__ID__']) }}",
    };
</script>
<script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/custom/utils/alert.js') }}"></script>
<script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/custom/admin/projectTypes/index.js') }}?v={{ time() }}">
</script>
@endpush