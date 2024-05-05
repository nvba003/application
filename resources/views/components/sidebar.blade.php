<aside id="sidebar" :class="{ 'w-80': !collapsed, 'w-10': collapsed }" class="bg-gray-800 text-white min-h-screen transition-width duration-300 ease-in-out relative"
    x-data="sidebarComponent({{ json_encode($menus) }})">
    <nav class="flex flex-col">
        <button @click="toggleSidebar" class="toggle-sidebar flex items-center justify-between px-2 py-2 bg-blue-500 text-white hover:bg-blue-600 focus:outline-none transition-colors duration-300 ease-in-out">
            <span x-show="!collapsed">Application</span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="bi bi-list w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h8M4 18h16" />
            </svg>
        </button>
        <div>
            <ul class="nav-list mt-2">
                @foreach ($menus as $menu)
                    <li class="menu-item relative"data-menu-id="{{ $menu->id }}" @mouseenter="enterMenu($event)" @mouseleave="leaveMenu($event)">
                        <button @click="setActiveMenu('{{ $menu->id }}')" :class="{ 'active-menu': isActiveMenu('{{ $menu->id }}') }"
                                class="menu-toggle flex justify-between items-center w-full px-2 py-2 text-left hover:bg-gray-700 focus:outline-none focus:bg-gray-700">
                            <span class="flex items-center">
                                {!! $menu->icon !!}
                                <span x-show="!collapsed && (activeMenu === '{{ $menu->id }}' || sidebarExpanded)" class="ml-2 menu-name">{{ $menu->name }}</span>
                            </span>
                        </button>
                        <div x-show="activeMenu === '{{ $menu->id }}' && !collapsed && sidebarExpanded" class="flex flex-col pl-4 bg-gray-800 w-full">
                            @foreach ($menu->children as $child)
                                <a href="#" @click="handleSubmenuClick('{{ $child->id }}', '{{ $menu->id }}', '{{ $child->url }}', $event)" 
                                   :class="{ 'active-submenu': isActiveSubmenu('{{ $child->id }}') }"
                                   class="block px-4 py-2 hover:bg-gray-600 text-white cursor-pointer submenu-item">{{ $child->name }}</a>
                            @endforeach
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </nav>

    <div x-show="showModal && collapsed" class="submenuModal fixed bg-gray-800 text-white pb-2"
        :style="'top: ' + modalTop + '; left: ' + modalLeft" 
        @mouseenter="mouseEnterModal" @mouseleave="mouseLeaveModal" x-transition>
        <ul>
            <li class="text-white bg-gray-600 font-bold p-2 mb-2 shadow" x-text="submenuTitle"></li>
            <template x-for="item in submenuItems" :key="item.id">
                <a href="#" :class="item.class" class="cursor-pointer text-white submenu-item" @click="handleSubmenuClick(item.id, item.menuId, item.url, $event)" x-text="item.name"></a>
            </template>
        </ul>
    </div>

</aside>

<script>
function sidebarComponent(menusData) {
    return {
        menus: menusData,
        collapsed: sessionStorage.getItem('sidebarCollapsed') === 'true' || false,
        activeMenu: sessionStorage.getItem('activeMenu') || null,
        sidebarExpanded: !this.collapsed,
        showModal: false,
        submenuTitle: '',
        submenuItems: [],
        modalTop: '0px',
        modalLeft: '0px',
        hoveringMenuItem: false,

        toggleSidebar() {
            this.collapsed = !this.collapsed;
            sessionStorage.setItem('sidebarCollapsed', this.collapsed);
            this.sidebarExpanded = !this.collapsed;
        },
        setActiveMenu(menuId) {
            this.activeMenu = this.activeMenu === menuId ? null : menuId;
            // sessionStorage.setItem('activeMenu', this.activeMenu);
        },
        isActiveMenu(menuId) {
            return this.activeMenu === menuId;
        },
        handleSubmenuClick(submenuId, menuId, url, event) {
            event.preventDefault();
            // console.log(menuId);
            sessionStorage.setItem('activeSubmenu', submenuId);
            sessionStorage.setItem('activeMenu', menuId);
            window.location.href = url;
        },
        isActiveSubmenu(submenuId) {
            return sessionStorage.getItem('activeSubmenu') === submenuId.toString();
        },
        enterMenu(event) {
            this.hoveringMenuItem = true;
            clearTimeout(this.modalTimeout);
            const menuId = parseInt(event.currentTarget.getAttribute('data-menu-id'), 10);
            const menu = this.menus.find(m => m.id === menuId);
            if (menu && this.collapsed) {
                this.submenuTitle = menu.name;
                this.submenuItems = menu.children.map(child => ({
                    id: child.id,
                    name: child.name,
                    url: child.url,
                    menuId: menu.id,
                    class: parseInt(sessionStorage.getItem('activeSubmenu'), 10) === child.id ? 'active-submenu-item' : 'submenu-item'
                }));
                const rect = event.currentTarget.getBoundingClientRect();
                this.modalTop = `${rect.top}px`;
                this.modalLeft = `${rect.right}px`;
                this.showModal = true;
            }
        },
        leaveMenu(event) {
            this.hoveringMenuItem = false;
            // Đặt timeout để cho phép chuyển đổi giữa các menu item
            this.modalTimeout = setTimeout(() => {
                if (!this.hoveringMenuItem) {
                    this.showModal = false;
                }
            }, 300);
        },

        mouseEnterModal() {
            this.hoveringMenuItem = true;
            clearTimeout(this.modalTimeout);
        },

        mouseLeaveModal() {
            this.hoveringMenuItem = false;
            this.modalTimeout = setTimeout(() => {
                if (!this.hoveringMenuItem) {
                    this.showModal = false;
                }
            }, 300);
        }

    };
}
</script>

<!-- <aside id="sidebar" class="sidebar">
    <button class="toggle-sidebar" id="toggle-sidebar" aria-label="Toggle Sidebar">
        <span class="button-text">Menu Application</span>
        <i class="fas fa-bars"></i>
    </button>

    <nav class="nav">
        <ul class="nav-list">
            @foreach ($menus as $menu)
                <li class="nav-item">
                    <button class="menu-toggle" aria-haspopup="true" aria-expanded="false">
                        <i class="{{ $menu->icon }}"></i>
                        {{ $menu->name }}
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    
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
</aside> -->
