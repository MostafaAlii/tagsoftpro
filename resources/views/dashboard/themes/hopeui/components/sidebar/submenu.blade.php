<li class="nav-item">
    <a class="nav-link" data-bs-toggle="collapse" href="#sidebar-{{ Str::slug($title) }}" role="button"
        aria-expanded="false">
        <i class="icon">
            @if(isset($icon))
            <i class="ti {{ $icon }}"></i>
            @endif
        </i>
        <span class="item-name">
            {{ $title }}
        </span>
        <i class="right-icon">
            <svg class="icon-18" width="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </i>
    </a>

    <ul class="sub-nav collapse {{ $open }}" id="sidebar-{{ Str::slug($title) }}" data-bs-parent="#sidebar-menu">
        @foreach($items as $item)
        <li class="nav-item">
            <a class="nav-link {{ $item['active'] }}" href="{{ $item['route'] }}">
                <i class="icon">
                    <svg class="icon-10" xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24"
                        fill="currentColor">
                        <circle cx="12" cy="12" r="8" fill="currentColor">
                        </circle>
                    </svg>
                </i>
                <i class="sidenav-mini-icon">
                    {{ mb_substr($item['title'],0,1) }}
                </i>
                <span class="item-name">
                    {{ $item['title'] }}
                </span>
            </a>
        </li>
        @endforeach
    </ul>
</li>
