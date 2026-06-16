@extends('dashboard.layouts.master')
@section('css')

@endsection

@section('title')
{{ $title }}
@endsection
@section('content')
<div class="page-content">
    <div class="content-header">
        <h1 class="mb-0">{{trans('dashboard/sidebar.main_settings_page_title') }}</h1>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('admin.dashboard')}}">{{trans('dashboard/header.main_dashboard') }}</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('admin.mainSettings.index')}}">{{
                    trans('dashboard/sidebar.main_settings_sidebar_title') }}</a>
            </li>
        </ul>
    </div>
    <!-- Start Content -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <span class="nav-icon">
                        <i class="ti ti-layout-2"></i>
                    </span>
                    {{ trans('dashboard/sidebar.main_settings_sidebar_title') }}
                </div>
                <div class="card-body">
                    <form>
                        <h1>x</h1>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Content -->
</div>
@endsection

@section('js')

@endsection
