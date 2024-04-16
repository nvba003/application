<aside id="sidebar" class="sidebar">
    <!-- Button để toggle sidebar, thêm aria-expanded cho truy cập tốt hơn -->
    <button class="toggle-sidebar" onclick="toggleSidebar()" aria-label="Toggle Sidebar">
        <span class="button-text">Menu Application</span>
        <i class="fas fa-bars"></i>
    </button>

    <!-- Navigation list được định nghĩa rõ ràng -->
    <nav class="nav">
        <ul class="nav-list">
            @foreach ($menus as $menu)
                <li class="nav-item">
                    <!-- Button cho mỗi menu item để dễ dàng thêm các sự kiện click -->
                    <button class="menu-toggle" aria-haspopup="true" aria-expanded="false">
                        <i class="{{ $menu->icon }}"></i>
                        {{ $menu->name }}
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <!-- Sub-menu, sử dụng aria-labelledby cho truy cập tốt hơn -->
                    @if ($menu->children->isNotEmpty())
                        <ul class="sub-menu" style="display: none;">
                            @foreach ($menu->children as $child)
                                <li class="nav-sub-item">
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
