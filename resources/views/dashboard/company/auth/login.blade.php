@extends('dashboard.layouts.login')

@section('css')

@endsection

@section('title')
{{ $title }}
@endsection

@section('content')
<div class="m-0 authentication-inner row">
    <div class="p-0 d-none d-lg-block col-lg-7 col-xl-8 img-side">
        <img class="img-fluid" width="100%" src="{{ asset('dashboard/assets/images/auth/company.jpg') }}"
            alt="happy young woman sitting on the floor using laptop on gray wall">
    </div>
    <div class="p-4 d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-5">
        <form class="form w-100" method="POST" action="{{route('company.post.login')}}">
            @csrf
            <div class="mx-auto w-px-200">
                <h4 class="mb-2">{{ trans('dashboard/auth.company_auth_form_title') }}</h4>
                <p class="mb-4"></p>
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="mb-3 form-group">
                            <label class="form-label">{{ trans('dashboard/auth.email_address') }}</label>
                            <input type="email" class="form-control" type="email" name="email" autocomplete="off"
                                placeholder="{{ trans('dashboard/auth.email_address') }}">

                        </div>
                        <div class="mb-3 form-group">
                            <label class="form-label">{{ trans('dashboard/auth.password') }}</label>
                            <input type="password" name="password" class="form-control"
                                placeholder="{{ trans('dashboard/auth.password') }}">
                        </div>
                        <div class="mb-4 form-group">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="customswitch1">
                                <label class="form-check-label" for="customswitch1">{{
                                    trans('dashboard/auth.remember_me') }}</label>
                            </div>
                        </div>
                        <div class="mb-4 d-grid">
                            <button class="mt-2 btn btn-primary btn-block">
                                {{ trans('dashboard/auth.login') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')

@endsection