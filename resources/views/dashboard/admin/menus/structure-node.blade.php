@foreach($nodes as $node)
<li class="tree-node" data-node-id="{{ $node['id'] }}" data-menu-item-id="{{ $node['menu_item_id'] }}"
    data-item-icon="{{ $node['icon'] }}" data-item-type="{{ $node['type'] }}">
    <div class="node-content">
        @if($node['type'] === 'dropdown')
        <button type="button" class="btn btn-sm btn-icon toggle-children-btn">
            <i class="ti ti-chevron-down"></i>
        </button>
        @endif
        <i class="ti ti-grip-vertical drag-handle"></i>
        @if($node['icon'])
        <i class="{{ $node['icon'] }}"></i>
        @endif
        <span class="node-title">{{ $node['title'] }}</span>
        <span class="badge bg-light text-dark node-badge">{{ $node['type'] }}</span>
    </div>
    <button type="button" class="btn btn-sm btn-icon btn-light-danger btn-remove-node" data-node-id="{{ $node['id'] }}">
        <i class="ti ti-x fs-6"></i>
    </button>
</li>
<ul class="tree-list nested" data-parent-id="{{ $node['id'] }}">
    @if(!empty($node['children']))
    @include('dashboard.admin.menus.structure-node', ['nodes' => $node['children']])
    @endif
</ul>
@endforeach