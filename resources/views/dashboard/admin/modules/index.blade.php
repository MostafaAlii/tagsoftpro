@extends('dashboard.layouts.master')
@section('css')
@endsection

@section('title')
{{ $title }}
@endsection

@section('content')
<div class="page-content">
    <div class="content-header">
        <h1 class="mb-0">{{ trans('dashboard/modules.modules') }}</h1>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">{{ trans('dashboard/header.main_dashboard') }}</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.modules.index') }}">{{ trans('dashboard/modules.modules') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <span class="nav-icon">
                        <i class="ti ti-cubes"></i>
                    </span>
                    {{ trans('dashboard/modules.modules') }}
                    <button data-pc-animate="3d-sign" type="button" class="btn btn-sm btn-light btn-active-primary"
                        data-bs-toggle="modal" data-bs-target="#createModuleModal">
                        <i class="fa fa-plus"></i>
                        {{ trans('dashboard/modules.create') }}
                    </button>

                    @php
                    $locales = array_keys(config('laravellocalization.supportedLocales'));
                    $currentLocale = app()->getLocale();
                    @endphp
                    @include('dashboard.admin.modules.btn.create', compact('locales', 'currentLocale'))
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
        project_types_for: "{{ trans('dashboard/modules.project_types_for') }}",
        no_project_types: "{{ trans('dashboard/modules.no_project_types') }}",
        error_occurred: "{{ trans('dashboard/general.error_occurred') }}",
    };
    window.locales = @json(array_keys(config('laravellocalization.supportedLocales')));
    window.routes = {
        update: "{{ route('admin.modules.update', ['module' => '__ID__']) }}",
        edit: "{{ route('admin.modules.edit', ['module' => '__ID__']) }}",
        destroy: "{{ route('admin.modules.destroy', ['module' => '__ID__']) }}",
    };
</script>
<script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/custom/utils/alert.js') }}"></script>
<script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/custom/admin/modules/index.js') }}?v={{ time() }}"></script>
@endpush