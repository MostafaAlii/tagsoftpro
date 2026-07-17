<li class="nav-item">
    <a class="nav-link {{ $active }}" href="{{ $route }}">
        <i class="icon">
            @if(isset($icon))
            <i class="ti {{ $icon }}"></i>
            @endif
        </i>
        <span class="item-name">
            {{ $title }}
        </span>
    </a>
</li>
