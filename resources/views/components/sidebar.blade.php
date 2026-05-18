<aside id="logo-sidebar"
    class="fixed top-20 left-0 z-40 w-56 h-[calc(100vh-4rem)] transition-transform -translate-x-full bg-white sm:translate-x-0 border-r border-gray-100"
    aria-label="Sidebar">
    <div class="h-full px-3 py-2 mt-3 overflow-y-auto bg-white">
        <ul class="space-y-1 font-medium">
            <li>
                <a href="{{ route('dashboard') }}"
                    class="flex items-center px-3 py-2 text-gray-600 rounded-lg transition-all duration-200 hover:bg-gray-50 group sidebar-menu-item"
                    data-menu="dashboard">
                    <i class="fa-solid fa-house text-red-600 w-5 text-center"></i>
                    <span class="ms-3 text-sm font-medium">Dashboard</span>
                </a>
            </li>

            <li class="pt-4 pb-1">
                <p class="px-3 text-xs font-medium text-gray-400 uppercase">SAKIP</p>
            </li>

            <li>
                <a href="#"
                    class="flex items-center justify-between px-3 py-2 text-gray-600 rounded-lg transition-all duration-200 hover:bg-gray-50 sidebar-dropdown-toggle sidebar-menu-item"
                    data-menu="perencanaan-kinerja">
                    <div class="flex items-center">
                        <i class="fa-solid fa-lightbulb text-gray-500 w-5 text-center"></i>
                        <span class="ms-3 text-sm">Perencanaan Kinerja</span>
                    </div>
                    <i
                        class="fa-solid fa-chevron-down text-gray-400 text-xs sidebar-dropdown-icon transition-transform duration-300"></i>
                </a>
                <ul class="py-1 pl-8 space-y-0.5 text-sm sidebar-dropdown hidden">
                    <li>
                        <a href="{{ route('manajemen.renstra') }}"
                            class="flex items-center px-3 py-1.5 text-gray-500 rounded-md transition-colors hover:text-red-600 sidebar-submenu-item"
                            data-submenu="manajemen-renstra">Manajemen Renstra</a>
                    </li>
                    <li>
                        <a href="{{ route('manajemen.rkt') }}"
                            class="flex items-center px-3 py-1.5 text-gray-500 rounded-md transition-colors hover:text-red-600 sidebar-submenu-item"
                            data-submenu="manajemen-rkt">Manajemen RKT</a>
                    </li>
                    <li>
                        <a href="{{ route('manajemen.pk') }}"
                            class="flex items-center px-3 py-1.5 text-gray-500 rounded-md transition-colors hover:text-red-600 sidebar-submenu-item"
                            data-submenu="manajemen-pk">Manajemen PK</a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="#"
                    class="flex items-center justify-between px-3 py-2 text-gray-600 rounded-lg transition-all duration-200 hover:bg-gray-50 sidebar-dropdown-toggle sidebar-menu-item"
                    data-menu="pengukuran-kinerja">
                    <div class="flex items-center">
                        <i class="fa-solid fa-scale-balanced text-gray-500 w-5 text-center"></i>
                        <span class="ms-3 text-sm">Pengukuran Kinerja</span>
                    </div>
                    <i
                        class="fa-solid fa-chevron-down text-gray-400 text-xs sidebar-dropdown-icon transition-transform duration-300"></i>
                </a>
                <ul class="py-1 pl-8 space-y-0.5 text-sm sidebar-dropdown hidden">
                    <li>
                        <a href="{{ route('sk.tim.sakip') }}"
                            class="flex items-center px-3 py-1.5 text-gray-500 rounded-md transition-colors hover:text-red-600 sidebar-submenu-item"
                            data-submenu="sk-tim-sakip">SK Tim SAKIP</a>
                    </li>
                    <li>
                        <a href="{{ route('fra.index') }}"
                            class="flex items-center px-3 py-1.5 text-gray-500 rounded-md transition-colors hover:text-red-600 sidebar-submenu-item"
                            data-submenu="form-rencana-aksi">Form Rencana Aksi</a>
                    </li>
                    @if(Auth::check() && Auth::user()->isSuperAdmin())
                    <li>
                        <a href="{{ route('skp') }}"
                            class="flex items-center px-3 py-1.5 text-gray-500 rounded-md transition-colors hover:text-red-600 sidebar-submenu-item"
                            data-submenu="skp-super-admin">SKP</a>
                    </li>
                    @endif
                    <li>
                        <a href="{{ route('unggah.skp') }}"
                            class="flex items-center px-3 py-1.5 text-gray-500 rounded-md transition-colors hover:text-red-600 sidebar-submenu-item"
                            data-submenu="unggah-skp">Unggah SKP</a>
                    </li>
                    <li>
                        <a href="{{ route('reward.punishment') }}"
                            class="flex items-center px-3 py-1.5 text-gray-500 rounded-md transition-colors hover:text-red-600 sidebar-submenu-item"
                            data-submenu="reward-punishment">Reward & Punishment</a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="#"
                    class="flex items-center justify-between px-3 py-2 text-gray-600 rounded-lg transition-all duration-200 hover:bg-gray-50 sidebar-dropdown-toggle sidebar-menu-item"
                    data-menu="pelaporan-kinerja">
                    <div class="flex items-center">
                        <i class="fa-solid fa-book-open-reader text-gray-500 w-5 text-center"></i>
                        <span class="ms-3 text-sm">Pelaporan Kinerja</span>
                    </div>
                    <i
                        class="fa-solid fa-chevron-down text-gray-400 text-xs sidebar-dropdown-icon transition-transform duration-300"></i>
                </a>
                <ul class="py-1 pl-8 space-y-0.5 text-sm sidebar-dropdown hidden">
                    <li>
                        <a href="{{ route('manajemen.lakin') }}"
                            class="flex items-center px-3 py-1.5 text-gray-500 rounded-md transition-colors hover:text-red-600 sidebar-submenu-item"
                            data-submenu="lakin">LAKIN</a>
                    </li>
                    <li>
                        <a href="{{ route('generate.link') }}"
                            class="flex items-center px-3 py-1.5 text-gray-500 rounded-md transition-colors hover:text-red-600 sidebar-submenu-item"
                            data-submenu="generate-link">Generate Permindok</a>
                    </li>
                </ul>
            </li>

            {{-- <li class="pt-4 pb-1">
                <p class="px-3 text-xs font-medium text-gray-400 uppercase">Settings</p>
            </li>

            @if(Auth::user()->canManageUsers())
            <li>
                <a href="{{ Auth::user()->isSuperAdmin() ? route('manajemen.pe') : route('manajemen.pengguna.admin') }}"
                    class="flex items-center px-3 py-2 text-gray-600 rounded-lg transition-all duration-200 hover:bg-gray-50 sidebar-menu-item"
                    data-menu="user-settings">
                    <i class="fa-solid fa-user-gear text-gray-500 w-5 text-center"></i>
                    <span class="ms-3 text-sm">User Management</span>
                </a>
            </li>
            @endif --}}
        </ul>
    </div>
</aside>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get all required elements
            const dropdownToggles = document.querySelectorAll('.sidebar-dropdown-toggle');
            const menuItems = document.querySelectorAll('.sidebar-menu-item');
            const submenuItems = document.querySelectorAll('.sidebar-submenu-item');

            // Function to reset all menu appearances
            function resetMenuAppearance() {
                menuItems.forEach(menuItem => {
                    menuItem.classList.remove('bg-red-50', 'text-red-600');
                    const icon = menuItem.querySelector('i:not(.sidebar-dropdown-icon)');
                    if (icon && !menuItem.getAttribute('data-menu') === 'dashboard') {
                        icon.classList.remove('text-red-600');
                        icon.classList.add('text-gray-500');
                    }

                    const dropdownIcon = menuItem.querySelector('.sidebar-dropdown-icon');
                    if (dropdownIcon) {
                        dropdownIcon.classList.remove('rotate-180', 'text-red-600');
                        dropdownIcon.classList.add('text-gray-400');
                    }
                });

                submenuItems.forEach(submenuItem => {
                    submenuItem.classList.remove('text-red-600', 'font-medium');
                });
            }

            // Function to activate menu item
            function activateMenuItem(menuItem) {
                menuItem.classList.add('bg-red-50', 'text-red-600');

                // Change icon color
                const icon = menuItem.querySelector('i:not(.sidebar-dropdown-icon)');
                if (icon) {
                    icon.classList.remove('text-gray-500');
                    icon.classList.add('text-red-600');
                }

                // Rotate dropdown icon if present
                const dropdownIcon = menuItem.querySelector('.sidebar-dropdown-icon');
                if (dropdownIcon) {
                    dropdownIcon.classList.add('rotate-180', 'text-red-600');
                    dropdownIcon.classList.remove('text-gray-400');
                }
            }

            // Function to activate submenu item
            function activateSubmenuItem(submenuItem) {
                submenuItem.classList.add('text-red-600', 'font-medium');
            }

            // Event listener for dropdown toggles
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function(event) {
                    event.preventDefault();

                    // Toggle dropdown visibility
                    const dropdownMenu = this.nextElementSibling;
                    if (dropdownMenu && dropdownMenu.classList.contains('sidebar-dropdown')) {
                        dropdownMenu.classList.toggle('hidden');

                        // Toggle icon rotation
                        const icon = this.querySelector('.sidebar-dropdown-icon');
                        if (icon) {
                            icon.classList.toggle('rotate-180');

                            if (!dropdownMenu.classList.contains('hidden')) {
                                icon.classList.add('text-red-600');
                                icon.classList.remove('text-gray-400');
                            } else {
                                icon.classList.remove('text-red-600');
                                icon.classList.add('text-gray-400');
                            }
                        }

                        // Save dropdown states
                        saveDropdownStates();
                    }

                    // Reset and set active appearance
                    resetMenuAppearance();
                    activateMenuItem(this);
                });
            });

            // Event handler for main menu items (non-dropdowns)
            menuItems.forEach(item => {
                if (!item.classList.contains('sidebar-dropdown-toggle')) {
                    item.addEventListener('click', function() {
                        resetMenuAppearance();
                        activateMenuItem(this);
                    });
                }
            });

            // Event handler for submenu items
            submenuItems.forEach(submenuItem => {
                submenuItem.addEventListener('click', function() {
                    resetMenuAppearance();
                    activateSubmenuItem(this);

                    // Also activate the parent dropdown
                    const parentDropdown = this.closest('.sidebar-dropdown').previousElementSibling;
                    if (parentDropdown) {
                        activateMenuItem(parentDropdown);
                    }
                });
            });

            // Function to activate menu based on current URL
            function activateMenuByUrl() {
                const currentPath = window.location.pathname;

                // Check for Dashboard
                if (currentPath === '/' || currentPath.includes('/dashboard')) {
                    const dashboardMenuItem = document.querySelector('[data-menu="dashboard"]');
                    if (dashboardMenuItem) {
                        resetMenuAppearance();
                        activateMenuItem(dashboardMenuItem);
                    }
                    return;
                }

                // Check for submenu matches
                let matchFound = false;

                // Check submenu paths using data attributes
                submenuItems.forEach(submenuItem => {
                    const submenuId = submenuItem.getAttribute('data-submenu');

                    if (submenuId && currentPath.includes(submenuId.replace(/-/g, '/'))) {
                        resetMenuAppearance();
                        activateSubmenuItem(submenuItem);

                        // Activate parent dropdown
                        const parentDropdown = submenuItem.closest('.sidebar-dropdown')
                            .previousElementSibling;
                        if (parentDropdown) {
                            activateMenuItem(parentDropdown);

                            // Show dropdown
                            const dropdownMenu = submenuItem.closest('.sidebar-dropdown');
                            dropdownMenu.classList.remove('hidden');
                        }

                        matchFound = true;
                    }
                });

                // If no submenu match, check main menu items
                if (!matchFound) {
                    menuItems.forEach(menuItem => {
                        const menuIdentifier = menuItem.getAttribute('data-menu');
                        if (menuIdentifier && currentPath.includes(menuIdentifier)) {
                            resetMenuAppearance();
                            activateMenuItem(menuItem);

                            // If it's a dropdown, expand it
                            if (menuItem.classList.contains('sidebar-dropdown-toggle')) {
                                const dropdownMenu = menuItem.nextElementSibling;
                                if (dropdownMenu) {
                                    dropdownMenu.classList.remove('hidden');
                                }
                            }
                        }
                    });
                }
            }

            // Save dropdown states to localStorage
            function saveDropdownStates() {
                const states = {};
                dropdownToggles.forEach((toggle, index) => {
                    const dropdownMenu = toggle.nextElementSibling;
                    if (dropdownMenu) {
                        states[`dropdown_${index}`] = !dropdownMenu.classList.contains('hidden');
                    }
                });
                localStorage.setItem('sidebarDropdownStates', JSON.stringify(states));
            }

            // Load dropdown states from localStorage
            function loadDropdownStates() {
                const savedStates = localStorage.getItem('sidebarDropdownStates');
                if (savedStates) {
                    const states = JSON.parse(savedStates);
                    dropdownToggles.forEach((toggle, index) => {
                        const dropdownMenu = toggle.nextElementSibling;
                        if (dropdownMenu && states[`dropdown_${index}`]) {
                            dropdownMenu.classList.remove('hidden');

                            // Rotate icon
                            const icon = toggle.querySelector('.sidebar-dropdown-icon');
                            if (icon) {
                                icon.classList.add('rotate-180', 'text-red-600');
                                icon.classList.remove('text-gray-400');
                            }
                        }
                    });
                }
            }

            // Initialize active menu based on URL
            activateMenuByUrl();

            // Load saved dropdown states
            loadDropdownStates();
        });
    </script>
@endpush
