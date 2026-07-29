@extends('dashboard.layouts.master')

@push('css')
<style>
    .structure-panel {
        min-height: 500px;
    }

    .available-items-list,
    .tree-list {
        list-style: none;
        padding-left: 0;
        margin: 0;
        min-height: 60px;
    }

    .tree-list.nested {
        padding-right: 28px;
        margin-top: 6px;
        border-right: 2px dashed #dfe3e8;
    }

    .tree-node {
        background: #fff;
        border: 1px solid #e2e5ea;
        border-radius: 8px;
        padding: 8px 12px;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: grab;
        transition: box-shadow 0.15s ease, border-color 0.15s ease;
    }

    .tree-node:hover,
    .available-item:hover {
        border-color: #6c5ce7;
        box-shadow: 0 2px 8px rgba(108, 92, 231, 0.1);
    }

    .tree-node.sortable-ghost,
    .available-item.sortable-ghost {
        opacity: 0.4;
        background: #f0eefe;
    }

    .tree-node.sortable-chosen {
        box-shadow: 0 4px 14px rgba(108, 92, 231, 0.2);
    }

    .tree-node .node-content {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .node-content i.drag-handle {
        cursor: grab;
        color: #6c5ce7;
        font-size: 20px;
        padding: 4px 6px;
        border-radius: 6px;
        background: #f0eefe;
        flex-shrink: 0;
    }

    .node-content i.drag-handle:hover {
        background: #e4dffc;
    }

    .node-content i.drag-handle:active {
        cursor: grabbing;
    }

    .node-badge {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 20px;
    }

    .empty-tree-placeholder {
        border: 2px dashed #dfe3e8;
        border-radius: 10px;
        padding: 30px;
        text-align: center;
        color: #9aa1ab;
    }

    /* ─── منع تحديد النص أثناء السحب ────────────────────────── */
    .tree-node,
    .available-item,
    .node-content,
    .node-content span,
    .node-content i {
        user-select: none !important;
        -webkit-user-select: none !important;
        -moz-user-select: none !important;
        -ms-user-select: none !important;
    }

    body.dragging-active {
        user-select: none !important;
        -webkit-user-select: none !important;
        cursor: grabbing !important;
    }

    body.dragging-active * {
        cursor: grabbing !important;
    }
</style>
@endpush

@section('title')
{{ trans('dashboard/menus.manage_structure') }} - {{ $menu->getTranslatedName() }}
@endsection

@section('content')
<div class="page-content">
    <div class="content-header">
        <h1 class="mb-0">
            {{ trans('dashboard/menus.manage_structure') }}: {{ $menu->getTranslatedName() }}
        </h1>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">{{ trans('dashboard/header.main_dashboard') }}</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.menus.index') }}">{{ trans('dashboard/menus.menus') }}</a>
            </li>
            <li class="breadcrumb-item">{{ trans('dashboard/menus.manage_structure') }}</li>
        </ul>
    </div>

    <div class="row g-4">
        {{-- ─── العناصر المتاحة ─────────────────────────────── --}}
        {{--<div class="col-md-4">
            <div class="card structure-panel">
                <div class="card-header">
                    <i class="ti ti-list-details me-2"></i>
                    {{ trans('dashboard/menus.available_items') }}
                </div>
                <div class="card-body">
                    <input type="text" id="searchAvailableItems" class="mb-3 form-control form-control-sm"
                        placeholder="{{ trans('dashboard/general.search') }}...">
                    <ul id="availableItemsList" class="available-items-list">
                        @forelse($availableItems as $item)
                        <li class="available-item" data-item-id="{{ $item->id }}"
                            data-item-type="{{ $item->type->value ?? $item->type }}">
                            <div class="node-content">
                                <i class="ti ti-grip-vertical drag-handle"></i>
                                @if($item->icon)
                                <i class="{{ $item->icon }}"></i>
                                @endif
                                <span>{{ $item->getTranslatedTitle() }}</span>
                            </div>
                            <span class="badge bg-light text-dark node-badge">{{ $item->type->value ?? $item->type
                                }}</span>
                        </li>
                        @empty
                        <li class="py-4 text-center text-muted">
                            {{ trans('dashboard/menus.no_available_items') }}
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>--}}
        {{-- ─── العناصر المتاحة ─────────────────────────────── --}}
        <div class="col-md-4">
            <div class="card structure-panel" id="itemsPanel" data-menu-id="{{ $menu->id }}">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span>
                        <i class="ti ti-list-details me-2"></i>
                        <span id="panelTitle">{{ trans('dashboard/menu_items.menu_items') }}</span>
                    </span>
                    <div class="gap-2 d-flex">
                        <button type="button" class="btn btn-sm btn-light-primary" id="btnCreatePanelItem">
                            <i class="ti ti-plus me-1"></i>
                            {{ trans('dashboard/menu_items.create') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-light-danger" id="btnTogglePanelTrashed"
                            style="display:none;">
                            <i class="ti ti-trash me-1"></i>
                            <span id="panelTrashedBtnText">{{ trans('dashboard/menu_items.show_trashed') }}</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <input type="text" id="searchAvailableItems" class="mb-3 form-control form-control-sm"
                        placeholder="{{ trans('dashboard/general.search') }}...">

                    {{-- ─── شريط الإجراءات الجماعية ─────────────────────── --}}
                    <div id="panelBulkBar"
                        class="p-2 mb-2 bg-light border rounded d-none align-items-center justify-content-between">
                        <span class="small">
                            <span id="panelSelectedCount">0</span> {{ trans('dashboard/menu_items.selected') }}
                        </span>
                        <div class="gap-2 d-flex">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnBulkChangeStatus">
                                <i class="ti ti-exchange me-1"></i>{{ trans('dashboard/menu_items.bulk_change_status')
                                }}
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="btnBulkDelete">
                                <i class="ti ti-trash me-1"></i>{{ trans('dashboard/general.delete_selected') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success d-none" id="btnBulkRestore">
                                <i class="ti ti-refresh me-1"></i>{{ trans('dashboard/menu_items.bulk_restore') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger d-none" id="btnBulkForceDelete">
                                <i class="ti ti-trash-off me-1"></i>{{ trans('dashboard/menu_items.bulk_force_delete')
                                }}
                            </button>
                        </div>
                    </div>

                    <ul id="availableItemsList" class="available-items-list"></ul>

                    <div id="panelLoader" class="py-3 text-center">
                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                    </div>

                    <div id="panelEmptyMsg" class="py-4 text-center text-muted d-none">
                        {{ trans('dashboard/menus.no_available_items') }}
                    </div>

                    <div class="mt-2 text-center">
                        <button type="button" class="btn btn-sm btn-light-primary d-none" id="btnLoadMore">
                            {{ trans('dashboard/general.load_more') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── شجرة القائمة ─────────────────────────────────── --}}
        <div class="col-md-8">
            <div class="card structure-panel">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>
                        <i class="ti ti-sitemap me-2"></i>
                        {{ trans('dashboard/menus.structure_tree') }}
                    </span>
                    <button type="button" class="btn btn-sm btn-primary" id="saveTreeBtn">
                        <span class="indicator-label">
                            <i class="ti ti-device-floppy me-1"></i>
                            {{ trans('dashboard/general.save') }}
                        </span>
                        <span class="indicator-progress d-none">
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            {{ trans('dashboard/general.loading') }}
                        </span>
                    </button>
                </div>
                <div class="card-body">
                    <div id="emptyTreePlaceholder" class="empty-tree-placeholder {{ !empty($tree) ? 'd-none' : '' }}">
                        {{ trans('dashboard/menus.drag_items_here') }}
                    </div>
                    <ul id="treeRoot" class="tree-list" data-parent-id="">
                        @include('dashboard.admin.menus.structure-node', ['nodes' => $tree])
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@include('dashboard.admin.menu_items.panel.item-modal', compact('companies', 'locales', 'icons'))
@include('dashboard.admin.menu_items.panel.confirm-modals')
{{-- Confirm Remove Node Modal --}}
@push('js')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    window.structureTranslations = {
        confirmRemove: "{{ trans('dashboard/menus.confirm_remove_node') }}",
        error: "{{ trans('dashboard/general.error_occurred') }}",
        saved: "{{ trans('dashboard/menus.structure_updated') }}",
        alreadyAdded: "{{ trans('dashboard/menus.item_already_added') }}",
    };

    window.panelTranslations = {
        error: "{{ trans('dashboard/general.error_occurred') }}",
        select_icon: "{{ trans('dashboard/menu_items.select_icon') }}",
        confirm_delete: "{{ trans('dashboard/menu_items.delete_confirm') }}",
        confirm_restore: "{{ trans('dashboard/menu_items.restore_confirm') }}",
        confirm_force_delete: "{{ trans('dashboard/menu_items.force_delete_confirm') }}",
        confirm_bulk_delete: "{{ trans('dashboard/menu_items.bulk_delete_confirm') }}",
        confirm_bulk_restore: "{{ trans('dashboard/menu_items.bulk_restore_confirm') }}",
        confirm_bulk_force_delete: "{{ trans('dashboard/menu_items.bulk_force_delete_confirm') }}",
        select_at_least_one: "{{ trans('dashboard/menu_items.bulk_select_at_least_one') }}",
        no_items: "{{ trans('dashboard/menus.no_available_items') }}",
        created: "{{ trans('dashboard/menu_items.created_successfully') }}",
        updated: "{{ trans('dashboard/menu_items.updated_successfully') }}",

        // ─── جديد: نصوص كانت متحطة غلط جوه ملف JS ───────────────
        menu_items_title: "{{ trans('dashboard/menu_items.menu_items') }}",
        show_trashed_label: "{{ trans('dashboard/menu_items.show_trashed') }}",
        show_active_label: "{{ trans('dashboard/menu_items.show_active') }}",
        restore_label: "{{ trans('dashboard/menu_items.restore') }}",
        force_delete_label: "{{ trans('dashboard/menu_items.force_delete') }}",
        delete_label: "{{ trans('dashboard/general.delete') }}",
        create_label: "{{ trans('dashboard/menu_items.create') }}",
        edit_label: "{{ trans('dashboard/menu_items.edit') }}",
        bulk_restore_label: "{{ trans('dashboard/menu_items.bulk_restore') }}",
        bulk_force_delete_label: "{{ trans('dashboard/menu_items.bulk_force_delete') }}",
        delete_selected_label: "{{ trans('dashboard/general.delete_selected') }}",
        status_label: "{{ trans('dashboard/menu_items.status') }}",
        is_owner_only_label: "{{ trans('dashboard/menu_items.is_owner_only') }}",
    };

    window.structureRoutes = {
        addNode: "{{ route('admin.menus.structure.addNode', $menu->id) }}",
        saveTree: "{{ route('admin.menus.structure.saveTree', $menu->id) }}",
        removeNode: "{{ route('admin.menus.structure.removeNode', ['menuNode' => '__ID__']) }}",
    };

    window.panelRoutes = {
        list: "{{ route('admin.menu_items.list') }}",
        store: "{{ route('admin.menu_items.store') }}",
        edit: "{{ route('admin.menu_items.edit', ['menu_item' => '__ID__']) }}",
        update: "{{ route('admin.menu_items.update', ['menu_item' => '__ID__']) }}",
        destroy: "{{ route('admin.menu_items.destroy', ['menu_item' => '__ID__']) }}",
        restore: "{{ route('admin.menu_items.restore', ['menu_item' => '__ID__'], false) }}",
        forceDelete: "{{ route('admin.menu_items.forceDelete', ['menu_item' => '__ID__'], false) }}",
        toggleStatus: "{{ route('admin.menu_items.toggleStatus', ['menu_item' => '__ID__'], false) }}",
        toggleOwner: "{{ route('admin.menu_items.toggleOwnerOnly', ['menu_item' => '__ID__'], false) }}",
        hasTrashed: "{{ route('admin.menu_items.hasTrashed') }}",
        bulkAction: "{{ route('admin.menu_items.bulkAction') }}",
    };

    window.panelMenuId = {{ $menu->id }};
</script>

<script src="{{ asset('dashboard/assets/js/custom/utils/alert.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/custom/admin/menus/structure.js') }}?v={{ time() }}"></script>
<script src="{{ asset('dashboard/assets/js/custom/admin/menu_items/items.js') }}?v={{ time() }}"></script>
<script src="{{ asset('dashboard/assets/js/custom/admin/menu_items/item_trashed.js') }}?v={{ time() }}"></script>
<script src="{{ asset('dashboard/assets/js/custom/admin/menu_items/item_switcher.js') }}?v={{ time() }}"></script>
@endpush
