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
                    <button data-pc-animate="3d-sign" type="button" class="btn btn-sm btn-light btn-active-primary"
                        data-bs-toggle="modal" data-bs-target="#createEmployeeModal">
                        <i class="fa fa-plus"></i>
                        {{ trans('dashboard/employees.create') }}
                    </button>

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
    };
    window.routes = {
        edit: "{{ route('admin.employees.edit', ['employee' => '__ID__']) }}",
        update: "{{ route('admin.employees.update', ['employee' => '__ID__']) }}",
        destroy: "{{ route('admin.employees.destroy', ['employee' => '__ID__']) }}",
    };
</script>
<script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/custom/utils/alert.js') }}"></script>
<script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/custom/admin/employees/index.js') }}?v={{ time() }}"></script>
@endpush
