@extends('dashboard.layouts.master')
@section('css')
<style>
    .bootstrap-switch .switch-group label {
        font-size: 12px !important;
        padding: 2px 6px !important;
    }
</style>
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
                    <a href="{{route('admin.mainSettings.index')}}">{{ trans('dashboard/sidebar.main_settings_sidebar_title') }}</a>
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
                        <form id="mainSettings" action="{{ route('admin.mainSettings.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <!-- Start General Settings -->
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label class="input-group-text text-dark">الاسم</label>
                                    <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name', $setting?->company_name) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="input-group-text text-dark">رقم الهاتف</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $setting?->phone) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="input-group-text text-dark">البريد الالكترونى</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $setting?->email) }}">
                                </div>
                            </div>
                            <!-- Start System Status -->
                            <div class="row">
                                <div class="form-group">
                                    <label class="form-label fw-bold">حالة النظام</label>
                                    {{-- hidden input to force inactive when unchecked --}}
                                    <input type="hidden" name="system_status" value="{{ \App\Enums\MainSetting\MainSettingSystemStatus::SYSTEM_STATUS_IN_ACTIVE->value }}">

                                    <input type="checkbox" name="system_status" data-toggle="switchbutton" data-size="sm"
                                        data-onlabel="{{ trans('dashboard/general.active') }}"
                                        data-offlabel="{{ trans('dashboard/general.in_active') }}" data-onstyle="success" data-offstyle="danger"
                                        value="{{ \App\Enums\MainSetting\MainSettingSystemStatus::SYSTEM_STATUS_ACTIVE->value }}" @checked(
                                        old( 'system_status' , $setting->system_status?->value
                                    ?? \App\Enums\MainSetting\MainSettingSystemStatus::SYSTEM_STATUS_IN_ACTIVE->value
                                    ) === \App\Enums\MainSetting\MainSettingSystemStatus::SYSTEM_STATUS_ACTIVE->value
                                    )>
                                </div>
                            </div>
                            <!-- End System Status -->
                            <!-- Start Logo & Favicon -->
                            <div class="container p-4 mt-2 bg-white rounded shadow">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="p-3 mb-3 text-center border rounded">
                                            <label for="logo" class="form-label fw-bold">الشعار (Logo)</label>
                                            <input class="form-control" type="file" name="logo" id="logoInput" accept="image/*">
                                            <img src="{{$logo}}" id="logoPreview" class="img-fluid" style="max-height: 60px;" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 mb-3 text-center border rounded">
                                            <label for="favicon" class="form-label fw-bold">favicon</label>
                                            <input class="form-control" type="file" name="favicon" id="faviconInput" accept="image/*">
                                            <img src="{{$favicon}}" id="faviconPreview" class="img-fluid" style="max-height: 60px;" />
                                        </div>
                                    </div>
                                    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="imageModalLabel">عرض الصورة</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="text-center modal-body">
                                                    <img id="popupImage" src="" class="rounded img-fluid" style="max-width: 100%; max-height: 80vh;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row">
                                    <div class="text-center col-md-12">
                                        <button type="submit" class="btn btn-success btn-lg">تحديث</button>
                                    </div>
                                </div>
                            </div>
                            <!-- End Logo & Favicon -->
                            <!-- End Absent Calculate -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Content -->
</div>
@endsection

@push('js')
<script>
    function previewImage(inputId, previewId) {
            let input = document.getElementById(inputId);
            let preview = document.getElementById(previewId);
            input.addEventListener("change", function() {
                let file = input.files[0];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = "block";
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.src = "";
                    preview.style.display = "none";
                }
            });
        }
    function openImageModal(src, title) {
        if (src) {
            let popupImage = document.getElementById("popupImage");
            let modalTitle = document.getElementById("imageModalLabel");
            popupImage.src = src;
            modalTitle.innerText = title;
            let imageModal = new bootstrap.Modal(document.getElementById("imageModal"));
            imageModal.show();
        }
    }
    previewImage("logoInput", "logoPreview");
    previewImage("faviconInput", "faviconPreview");
</script>
@endpush
