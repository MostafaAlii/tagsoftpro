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

    .tree-node,
    .available-item {
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

    .node-content {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .node-content i.drag-handle {
        cursor: grab;
        color: #b2b8c2;
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
    
    /* ─── تكبير وتوضيح مقبض السحب ─────────────────────────────── */
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
        <div class="col-md-4">
            <div class="card structure-panel">
                <div class="card-header">
                    <i class="ti ti-list-details me-2"></i>
                    {{ trans('dashboard/menus.available_items') }}
                </div>
                <div class="card-body">
                    <input type="text" id="searchAvailableItems" class="mb-3 form-control form-control-sm" placeholder="{{ trans('dashboard/general.search') }}...">
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

{{-- Confirm Remove Node Modal --}}
<div class="modal fade" id="confirmRemoveNodeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="ti ti-alert-triangle me-2"></i>
                    {{ trans('dashboard/general.confirm') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{ trans('dashboard/menus.confirm_remove_node') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ trans('dashboard/general.cancel') }}
                </button>
                <button type="button" class="btn btn-danger" id="confirmRemoveNodeBtn">
                    <span class="indicator-label">{{ trans('dashboard/general.delete') }}</span>
                    <span class="indicator-progress d-none">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        {{ trans('dashboard/general.loading') }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
    window.structureTranslations = {
        confirmRemove: "{{ trans('dashboard/menus.confirm_remove_node') }}",
        error: "{{ trans('dashboard/general.error_occurred') }}",
        saved: "{{ trans('dashboard/menus.structure_updated') }}",
        alreadyAdded: "{{ trans('dashboard/menus.item_already_added') }}",
    };

    window.structureRoutes = {
        addNode: "{{ route('admin.menus.structure.addNode', $menu->id) }}",
        saveTree: "{{ route('admin.menus.structure.saveTree', $menu->id) }}",
        removeNode: "{{ route('admin.menus.structure.removeNode', ['menuNode' => '__ID__']) }}",
    };
</script>
<script src="{{ asset('dashboard/assets/js/custom/utils/alert.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/custom/admin/menus/structure.js') }}?v={{ time() }}"></script>
@endpush