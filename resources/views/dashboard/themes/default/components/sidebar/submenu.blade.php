<li class="nav-item nav-hasmenu {{ $open }}">
    <a href="#!" class="nav-link">
        <span class="nav-icon">
            <i class="ti {{ $icon }}"></i>
        </span>
        <span class="nav-text">
            {{ $title }}
        </span>
        <span class="nav-arrow">
            <i data-feather="chevron-right"></i>
        </span>
    </a>

    <ul class="nav-submenu">
        @foreach($items as $item)
        <li class="nav-item">
            <a href="{{ $item['route'] }}" class="nav-link {{ $item['active'] }}">
                {{ $item['title'] }}
            </a>
        </li>
        @endforeach
    </ul>
</li>
