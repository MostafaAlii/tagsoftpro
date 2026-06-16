@extends('dashboard.layouts.master')
@section('css')
@endsection

@section('title')
{{ $title }}
@endsection

@section('content')
<div class="page-content">
    <div class="content-header">
        <h1 class="mb-0">{{ trans('dashboard/projects.projects') }}</h1>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">{{ trans('dashboard/header.main_dashboard') }}</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.projects.index') }}">{{ trans('dashboard/projects.projects') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <span class="nav-icon">
                        <i class="ti ti-folder-plus"></i>
                    </span>
                    {{ trans('dashboard/projects.projects') }}
                    <button data-pc-animate="3d-sign" type="button" class="btn btn-sm btn-light btn-active-primary"
                        data-bs-toggle="modal" data-bs-target="#createProjectModal">
                        <i class="fa fa-plus"></i>
                        {{ trans('dashboard/projects.create') }}
                    </button>

                    @php
                    $locales = array_keys(config('laravellocalization.supportedLocales'));
                    $currentLocale = app()->getLocale();
                    @endphp
                    @include('dashboard.admin.projects.btn.create', compact('locales', 'currentLocale'))
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
    };
    window.locales = @json(array_keys(config('laravellocalization.supportedLocales')));
    window.routes = {
        edit: "{{ route('admin.projects.edit', ['project' => '__ID__']) }}",
        update: "{{ route('admin.projects.update', ['project' => '__ID__']) }}",
        destroy: "{{ route('admin.projects.destroy', ['project' => '__ID__']) }}",
    };
</script>
<script src="{{ asset('dashboard/themes/default/assets/js/custom/utils/alert.js') }}"></script>
<script src="{{ asset('dashboard/themes/default/assets/js/custom/admin/projects/index.js') }}?v={{ time() }}"></script>
@endpush