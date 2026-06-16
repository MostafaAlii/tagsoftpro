@extends('dashboard.layouts.master')
@section('title')
{{ $title }}
@endsection

@section('content')
<div class="page-content">
    <div class="content-header">
        <h1 class="mb-0">{{ trans('dashboard/features.features') }}</h1>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">{{ trans('dashboard/header.main_dashboard') }}</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.features.index') }}">{{ trans('dashboard/features.features') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <span class="nav-icon">
                        <i class="ti ti-flag"></i>
                    </span>
                    {{ trans('dashboard/features.features') }}
                    <button type="button" class="btn btn-sm btn-light btn-active-primary" data-bs-toggle="modal"
                        data-bs-target="#createFeatureModal">
                        <i class="fa fa-plus"></i>
                        {{ trans('dashboard/features.create') }}
                    </button>
                    <a href="{{route('admin.plans.index')}}" class="btn btn-sm btn-light btn-active-primary">
                        <i class="fa fa-plus"></i>
                        {{ trans('dashboard/plans.plans') }}
                    </a>

                    @php
                    $locales = array_keys(config('laravellocalization.supportedLocales'));
                    $currentLocale = app()->getLocale();
                    @endphp

                    @include('dashboard.admin.features.btn.create', compact('locales', 'currentLocale'))
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
            update:       "{{ route('admin.features.update',       ['feature' => '__ID__']) }}",
            edit:         "{{ route('admin.features.edit',         ['feature' => '__ID__']) }}",
            destroy:      "{{ route('admin.features.destroy',      ['feature' => '__ID__']) }}",
        };
</script>
<script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/custom/utils/alert.js') }}"></script>
<script src="{{ asset('dashboard/themes/'. $theme_code .'/assets/js/custom/admin/features/index.js') }}"></script>
@endpush