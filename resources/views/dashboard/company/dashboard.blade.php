@extends('dashboard.layouts.master')
@section('css')

@endsection

@section('title')
{{ $title }}
@endsection
@section('content')
<div class="page-content">
    <div class="content-header">
        <h1 class="mb-0">{{ucfirst(get_user_data()?->name) . ' ' . trans('dashboard/header.main_dashboard') }}</h1>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body bg-primary rounded-3">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-12">
                                    <div class="d-lg-flex justify-content-between align-items-center ">
                                        <div class="d-md-flex align-items-center">
                                            <img src="{{ asset('dashboard/assets/images/user/avatar-2.jpg') }}"
                                                alt="Image" class="rounded-circle avatar avatar-xl">
                                            <div class="mt-3 ms-md-4">
                                                <h2 class="mb-1 text-white fw-600">
                                                    {{ucfirst(get_user_data()?->name)}}
                                                </h2>
                                                <p class="text-white">
                                                    {{get_user_data()?->type}}
                                                    <br>
                                                    {{ get_user_data()?->phone }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')

@endsection