<aside id="sidebar" class="sidebar">
    <nav class="nav">
        <button class="toggle-sidebar">
            <span class="button-text">Menu Application</span> <!-- Text sẽ được ẩn khi sidebar thu nhỏ -->
            <i class="fas fa-bars"></i> <!-- Icon menu từ FontAwesome -->
        </button>

        <ul class="nav-list">
            @foreach ($menus as $menu)
                <li class="nav-item">
                    <button class="menu-toggle">  
                        <i class="{{ $menu->icon }}"></i> <!-- Sử dụng giá trị từ cột 'icon' -->
                        {{ $menu->name }}
                        <i class="fas fa-chevron-down"></i> <!-- Ví dụ sử dụng FontAwesome -->
                    </button>
                    @if ($menu->children->isNotEmpty())
                        <ul class="sub-menu" style="display: none;">
                            @foreach ($menu->children as $child)
                                <li class="nav-item">
                                    <a href="{{ $child->url }}">{{ $child->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </nav>
</aside>
