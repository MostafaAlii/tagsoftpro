@extends('dashboard.layouts.master')
@section('css')
@endsection

@section('title')
{{ $title }}
@endsection

@section('content')
<div class="page-content">
    <div class="content-header">
        <h1 class="mb-0">{{ trans('dashboard/plans.plans') }}</h1>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">{{ trans('dashboard/header.main_dashboard') }}</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.plans.index') }}">{{ trans('dashboard/plans.plans') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <span class="nav-icon">
                        <i class="ti ti-package"></i>
                    </span>
                    {{ trans('dashboard/plans.plans') }}
                    <button data-pc-animate="3d-sign" type="button" class="btn btn-sm btn-light btn-active-primary"
                        data-bs-toggle="modal" data-bs-target="#createPlanModal">
                        <i class="fa fa-plus"></i>
                        {{ trans('dashboard/plans.create') }}
                    </button>

                    @php
                    $locales = array_keys(config('laravellocalization.supportedLocales'));
                    $currentLocale = app()->getLocale();
                    @endphp
                    @include('dashboard.admin.plans.btn.create', compact('locales', 'currentLocale'))
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
        update: "{{ route('admin.plans.update', ['plan' => '__ID__']) }}",
        edit: "{{ route('admin.plans.edit', ['plan' => '__ID__']) }}",
        destroy : "{{ route('admin.plans.destroy', ['plan' => '__ID__']) }}",
        toggleBillingCycle: "{{ route('admin.plans.toggleBillingCycle', ['plan' => '__ID__']) }}",
        getFeatures: "{{ route('admin.plans.getFeatures', ['plan' => '__ID__']) }}",
        updateFeatures: "{{ route('admin.plans.updateFeatures', ['plan' => '__ID__']) }}",
    };
</script>
<script src="{{ asset('dashboard/themes/default/assets/js/custom/utils/alert.js') }}"></script>
<script src="{{ asset('dashboard/themes/default/assets/js/custom/admin/plans/index.js') }}?v={{ time() }}"></script>
@endpush