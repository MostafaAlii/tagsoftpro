@extends('dashboard.layouts.master')
@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@foreach($iconCssUrls ?? [] as $cssUrl)
<link rel="stylesheet" href="{{ $cssUrl }}">
@endforeach
<style>
    .dt-button-collection {
        border-radius: 10px !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15) !important;
        border: 1px solid #e0e0e0 !important;
        min-width: 200px !important;
        z-index: 99999 !important;
        position: absolute !important;
        margin-top: 5px !important;
    }

    .dt-buttons {
        position: relative !important;
    }

    .dataTables_wrapper,
    .card-header,
    .card,
    .table-responsive {
        overflow: visible !important;
    }

    /* ─── Select2 Icon Picker - Custom Styling ────────────────────── */

    /* الـ input الأساسي */
    .select2-container--default .select2-selection--single {
        height: 42px !important;
        border-radius: 8px !important;
        border: 1.5px solid #dfe3e8 !important;
        background-color: #fff !important;
        transition: all 0.2s ease-in-out !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
    }

    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #6c5ce7 !important;
        box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.12) !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 39px !important;
        padding-left: 14px !important;
        padding-right: 36px !important;
        font-size: 14px !important;
        color: #2d3436 !important;
        display: flex !important;
        align-items: center !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #9aa1ab !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
        width: 32px !important;
        right: 4px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #8891a1 transparent transparent transparent !important;
        border-width: 6px 5px 0 5px !important;
        margin-left: -5px !important;
        margin-top: -2px !important;
        transition: transform 0.2s ease !important;
    }

    .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
        transform: rotate(180deg) !important;
    }

    /* زر الـ Clear (X) */
    .select2-container--default .select2-selection--single .select2-selection__clear {
        color: #b2b8c2 !important;
        font-size: 18px !important;
        margin-right: 6px !important;
        transition: color 0.15s ease !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__clear:hover {
        color: #e74c3c !important;
    }

    /* ─── الـ Dropdown نفسه ────────────────────────────────────────── */
    .select2-dropdown {
        z-index: 999999 !important;
        border: 1px solid #e2e5ea !important;
        border-radius: 10px !important;
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.14) !important;
        overflow: hidden !important;
        margin-top: 4px !important;
    }

    /* مربع البحث جوه الـ dropdown */
    .select2-search--dropdown {
        padding: 10px !important;
        background-color: #f8f9fb !important;
        border-bottom: 1px solid #eceef1 !important;
    }

    .select2-search--dropdown .select2-search__field {
        border: 1.5px solid #dfe3e8 !important;
        border-radius: 6px !important;
        padding: 8px 12px !important;
        font-size: 13.5px !important;
        outline: none !important;
        transition: border-color 0.2s ease !important;
    }

    .select2-search--dropdown .select2-search__field:focus {
        border-color: #6c5ce7 !important;
    }

    /* قايمة النتايج */
    .select2-results {
        max-height: 320px !important;
    }

    .select2-results__options {
        max-height: 320px !important;
        overflow-y: auto !important;
        padding: 6px !important;
    }

    /* Scrollbar احترافي */
    .select2-results__options::-webkit-scrollbar {
        width: 7px !important;
    }

    .select2-results__options::-webkit-scrollbar-track {
        background: transparent !important;
    }

    .select2-results__options::-webkit-scrollbar-thumb {
        background-color: #d4d7dd !important;
        border-radius: 10px !important;
    }

    .select2-results__options::-webkit-scrollbar-thumb:hover {
        background-color: #b2b8c2 !important;
    }

    /* كل عنصر في القايمة */
    .select2-results__option {
        padding: 10px 14px !important;
        font-size: 14px !important;
        color: #2d3436 !important;
        border-radius: 8px !important;
        margin-bottom: 2px !important;
        transition: background-color 0.12s ease !important;
        display: flex !important;
        align-items: center !important;
    }

    .select2-results__option[aria-selected="true"] {
        background-color: #f0eefe !important;
        color: #6c5ce7 !important;
        font-weight: 600 !important;
    }

    .select2-results__option--highlighted {
        background-color: #6c5ce7 !important;
        color: #fff !important;
    }

    .select2-results__option--highlighted[aria-selected="true"] {
        background-color: #6c5ce7 !important;
        color: #fff !important;
    }

    /* أيقونة داخل كل عنصر */
    .select2-results__option i {
        margin-left: 10px !important;
        margin-right: 0 !important;
        font-size: 19px !important;
        width: 28px !important;
        text-align: center !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
        color: #6c5ce7 !important;
        transition: color 0.12s ease !important;
    }

    .select2-results__option--highlighted i,
    .select2-results__option[aria-selected="true"] i {
        color: inherit !important;
    }

    /* رسالة "مفيش نتايج" */
    .select2-results__message {
        padding: 16px !important;
        text-align: center !important;
        color: #9aa1ab !important;
        font-size: 13.5px !important;
    }

    /* الأيقونة اللي جوه الـ selection الحالي (بعد الاختيار) */
    .select2-selection__rendered i {
        margin-left: 8px !important;
        margin-right: 0 !important;
        font-size: 17px !important;
        color: #6c5ce7 !important;
        flex-shrink: 0 !important;
    }

    /* ─── Select2 inside Input Group Fix ────────────────────────────── */
    .input-group > .select2-container--default {
        flex: 1 1 auto !important;
        width: 1% !important;
    }

    .input-group > .select2-container--default .select2-selection--single {
        height: 42px !important;
    }

    /* شكل الحواف عشان يبان متلاصق صح مع الـ input-group-text */
    .input-group > .select2-container--default:not(:first-child) .select2-selection--single {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }

    .input-group > .select2-container--default:first-child .select2-selection--single {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }

    /* لو الصفحة RTL والـ input-group-text ظاهر يمين */
    .input-group.icon-picker-group {
        display: flex !important;
        flex-wrap: nowrap !important;
        align-items: stretch !important;
    }

    .input-group.icon-picker-group .select2-container {
        flex: 1 1 auto !important;
        width: 1% !important;
        min-width: 0 !important;
    }
</style>
@endpush

@section('title')
{{ $title }}
@endsection

@section('content')
<div class="page-content">
    <div class="content-header">
        <h1 class="mb-0">{{ trans('dashboard/menu_items.menu_items') }}</h1>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">{{ trans('dashboard/header.main_dashboard') }}</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.menu_items.index') }}">{{ trans('dashboard/menu_items.menu_items') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <span class="nav-icon">
                        <i class="ti ti-list"></i>
                    </span>
                    {{ trans('dashboard/menu_items.menu_items') }}
                    <div class="gap-2 d-flex">
                        <button data-pc-animate="3d-sign" type="button" class="btn btn-sm btn-light btn-active-primary"
                            data-bs-toggle="modal" data-bs-target="#createMenuItemModal">
                            <i class="fa fa-plus"></i>
                            {{ trans('dashboard/menu_items.create') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-danger btn-active-primary" id="toggleTrashed"
                            style="display: none;">
                            <i class="ti ti-trash me-1"></i>
                            <span id="trashedBtnText">{{ trans('dashboard/menu_items.show_trashed') }}</span>
                        </button>
                    </div>
                    @include('dashboard.admin.menu_items.btn.create', compact('companies', 'locales', 'icons'))
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
@include('dashboard.admin.menu_items.btn.modals', compact('companies', 'locales'))
@push('js')
{!! $dataTable->scripts() !!}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    window.translations = {
        error: "{{ trans('dashboard/general.error_occurred') }}",
        restore: "{{ trans('dashboard/menu_items.restore') }}",
        restore_confirm: "{{ trans('dashboard/menu_items.restore_confirm') }}",
        force_delete: "{{ trans('dashboard/menu_items.force_delete') }}",
        force_delete_confirm: "{{ trans('dashboard/menu_items.force_delete_confirm') }}",
        show_active: "{{ trans('dashboard/menu_items.show_active') }}",
        show_trashed: "{{ trans('dashboard/menu_items.show_trashed') }}",
        bulk_select_at_least_one: "{{ trans('dashboard/menu_items.bulk_select_at_least_one') }}",
        bulk_status_confirm: "{{ trans('dashboard/menu_items.bulk_status_confirm') }}",
        bulk_delete_confirm: "{{ trans('dashboard/menu_items.bulk_delete_confirm') }}",
        bulk_change_status: "{{ trans('dashboard/menu_items.bulk_change_status') }}",
        delete_selected: "{{ trans('dashboard/general.delete_selected') }}",
        confirm: "{{ trans('dashboard/general.confirm') }}",
        delete: "{{ trans('dashboard/general.delete') }}",
        bulk_actions: "{{ trans('dashboard/menu_items.bulk_actions') }}",
        bulk_restore: "{{ trans('dashboard/menu_items.bulk_restore') }}",
        bulk_force_delete: "{{ trans('dashboard/menu_items.bulk_force_delete') }}",
        bulk_restore_confirm: "{{ trans('dashboard/menu_items.bulk_restore_confirm') }}",
        bulk_force_delete_confirm: "{{ trans('dashboard/menu_items.bulk_force_delete_confirm') }}",
    };

    window.routes = {
        index: "{{ route('admin.menu_items.index') }}",
        edit: "{{ route('admin.menu_items.edit', ['menu_item' => '__ID__']) }}",
        update: "{{ route('admin.menu_items.update', ['menu_item' => '__ID__']) }}",
        destroy: "{{ route('admin.menu_items.destroy', ['menu_item' => '__ID__']) }}",
        restore: "{{ route('admin.menu_items.restore', ['menu_item' => '__ID__'], false) }}",
        forceDelete: "{{ route('admin.menu_items.forceDelete', ['menu_item' => '__ID__'], false) }}",
        hasTrashed: "{{ route('admin.menu_items.hasTrashed') }}",
        bulkAction: "{{ route('admin.menu_items.bulkAction') }}",
    };
    window.showTrashed = false;
</script>
<script src="{{ asset('dashboard/assets/js/custom/utils/alert.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/custom/admin/menu_items/index.js') }}?v={{ time() }}"></script>
<script src="{{ asset('dashboard/assets/js/custom/admin/menu_items/bulk_actions.js') }}?v={{ time() }}"></script>
<script src="{{ asset('dashboard/assets/js/custom/admin/menu_items/trashed_manager.js') }}?v={{ time() }}"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof window.MenuItems !== 'undefined' && window.MenuItems.init) {
            window.MenuItems.init();
        }
        setTimeout(function() {
            if (typeof window.checkTrashed === 'function') {
                window.checkTrashed();
            }
        }, 500);
    });
    document.addEventListener('shown.bs.modal', function(e) {
        setTimeout(() => {
            const selects = e.target.querySelectorAll('.icon-picker-select');
            selects.forEach((select) => {
                if (!select.dataset.select2Initialized) {
                    initIconPicker();
                }
                $(select).select2('dropdown').css('z-index', 999999);
            });
        }, 100);
    });
</script>
@endpush