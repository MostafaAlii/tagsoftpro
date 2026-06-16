@extends('dashboard.layouts.master')
@section('css')
@endsection

@section('title')
{{ $title }}
@endsection
@section('content')
<div class="page-content">
    <div class="content-header">
        <h1 class="mb-0">{{trans('dashboard/sidebar.client_page_title') }}</h1>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('admin.dashboard')}}">{{trans('dashboard/header.main_dashboard') }}</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('admin.clients.index')}}">{{
                    trans('dashboard/sidebar.client_sidebar_title') }}</a>
            </li>
        </ul>
    </div>
    <!-- Start Content -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <span class="nav-icon">
                        <i class="ti ti-award"></i>
                    </span>
                    {{ trans('dashboard/sidebar.client_sidebar_title') }}
                    <button data-pc-animate="3d-sign" type="button" class="btn btn-sm btn-light btn-active-primary"
                        data-bs-toggle="modal" data-bs-target="#createClientModal">
                        <i class="fa fa-plus"></i>
                        {{trans('dashboard/client.add_new_client')}}
                    </button>
                    @include('dashboard.admin.clients.btn.create')
                </div>
                <div class="card-body">
                    <!--begin::Table container-->
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table table-striped table-row-bordered gy-5 gs-7">
                            {!! $dataTable->table() !!}
                        </table>
                        <!--end::Table-->
                    </div>
                    <!--end::Table container-->
                </div>
            </div>
        </div>
    </div>
    <!-- End Content -->
</div>
@endsection

@push('js')
{!! $dataTable->scripts() !!}
<script>window.translations = {error: "{{ trans('dashboard/general.error_occurred') }}",};</script>
<script src="{{ asset('dashboard/themes/default/assets/js/custom/utils/alert.js') }}"></script>
<script src="{{ asset('dashboard/themes/default/assets/js/custom/admin/clients/index.js') }}"></script>
@endpush