@php
    $isSuperAdmin = auth()->check() && auth()->user()->role && auth()->user()->role->role_name === 'Super Admin';
@endphp

{{-- NAVBAR --}}
<nav class="fixed top-0 z-50 w-full bg-white/90 backdrop-blur border-b border-gray-200 h-20 shadow-sm dark:bg-gray-900/90 dark:border-gray-700">
    <div class="px-3 py-3 lg:px-5 lg:pl-3 h-full">
        <div class="flex items-center justify-between">
            {{-- Left Side Navbar --}}
            <div class="flex items-center justify-start rtl:justify-end">
                <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar"
                    type="button"
                    class="inline-flex items-center p-2 text-sm text-gray-600 rounded-xl sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-red-200 dark:text-gray-300 dark:hover:bg-gray-800 dark:focus:ring-red-800">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" fill-rule="evenodd"
                            d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                        </path>
                    </svg>
                </button>
                <div class="flex ms-2 md:me-24 items-center">
                    <img src="{{ asset('img/logo BPS.png') }}" class="h-8 me-3" alt="Logo" />
                    <div class="hidden sm:block">
                        <p class="text-xl font-bold">e-SAKIP</p>
                        <span class="self-center text-sm font-medium whitespace-nowrap">Badan Pusat Statistik</span>
                    </div>
                    <div class="block sm:hidden">
                        <p class="text-lg font-bold">e-SAKIP</p>
                    </div>
                </div>
            </div>
            {{-- Right Side Navbar --}}
            <div class="flex items-center">
                <div class="flex items-center ms-3">
                    <div class="flex items-center">
                        <span class="material-symbols-outlined text-gray-600 text-2xl mr-3 dark:text-gray-300">dark_mode</span>
                        <label class="inline-flex items-center cursor-pointer mr-3">
                            <input type="checkbox" id="darkModeToggle" class="sr-only peer">
                            <div
                                class="relative w-11 h-6 bg-gray-200 rounded-full peer-focus:ring-red-300 dark:peer-focus:ring-red-800 dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-red-600 dark:peer-checked:bg-red-600">
                            </div>
                        </label>
                        <span class="material-symbols-outlined text-gray-600 text-2xl mr-6 dark:text-gray-300">light_mode</span>
                        <button type="button" id="profile-button"
                            class="flex text-sm bg-white rounded-full ring-1 ring-gray-200 hover:ring-gray-300 focus:outline-none focus:ring-4 focus:ring-red-200 dark:bg-gray-800 dark:ring-gray-700 dark:hover:ring-gray-600 dark:focus:ring-red-800 mr-3">
                            <span class="sr-only">Open user menu</span>
                            @auth
                                <img class="w-10 h-10 rounded-full object-cover"
                                    src="{{ auth()->user()->profile_picture ? asset('storage/profile_pictures/' . auth()->user()->profile_picture) : asset('img/default-avatar.svg') }}" 
                                    alt="user photo">
                            @else
                                <img class="w-10 h-10 rounded-full object-cover"
                                    src="{{ asset('img/default-avatar.svg') }}" alt="user photo">
                            @endauth
                        </button>
                    </div>
                    <div
                        class="hidden opacity-0 scale-95 transition transform duration-150 ease-out bg-white shadow-lg w-64 sm:w-80 absolute top-20 right-4 mt-2 divide-y divide-gray-100 rounded-2xl z-50 ring-1 ring-gray-200 flex flex-col items-center dark:bg-gray-800 dark:divide-gray-700 dark:ring-gray-700"
                        id="dropdown-user">
                        <div class="px-4 text-center flex flex-col items-center">
                            @auth
                                <img class="mt-4 w-12 h-12 rounded-full object-cover"
                                    src="{{ auth()->user()->profile_picture ? asset('storage/profile_pictures/' . auth()->user()->profile_picture) : asset('img/default-avatar.svg') }}" 
                                    alt="user photo">
                                <p class="text-base font-bold py-3 text-red-700 dark:text-red-300">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-600 -mt-2 mb-3 dark:text-gray-300">{{ auth()->user()->getRoleDisplayName() }}</p>
                            @else
                                <img class="mt-4 w-12 h-12 rounded-full object-cover"
                                    src="{{ asset('img/default-avatar.svg') }}" alt="user photo">
                                <p class="text-base font-bold py-3 text-red-700 dark:text-red-300">Guest User</p>
                            @endauth
                        </div>
                        <div class="bg-gray-50 rounded-xl w-5/6 mb-4 ring-1 ring-gray-100 dark:bg-gray-900 dark:ring-gray-700">
                            <ul class="py-1">
                                <li class="flex ml-3 items-center">
                                    <span class="text-gray-600 material-symbols-outlined dark:text-gray-300">person</span>
                                    <a href="{{ route('manajemen.profil') }}" class="block px-4 py-2 text-sm text-gray-700 hover:text-red-700 dark:text-gray-200 dark:hover:text-red-300"
                                        role="menuitem">Profil</a>
                                </li>

                                @auth
                                    @if ($isSuperAdmin)
                                        <li class="flex ml-3 items-center">
                                            <span class="text-gray-600 material-symbols-outlined dark:text-gray-300">manage_accounts</span>
                                            <a href="{{ route('manajemen.pengguna') }}"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:text-red-700 dark:text-gray-200 dark:hover:text-red-300"
                                                role="menuitem">Manajemen Pengguna</a>
                                        </li>
                                    @endif
                                @endauth

                                <li class="flex ml-3 items-center">
                                    <span class="text-gray-600 material-symbols-outlined dark:text-gray-300">logout</span>
                                    <form method="POST" action="{{ route('logout') }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:text-red-700 dark:text-gray-200 dark:hover:text-red-300"
                                            role="menuitem">Keluar</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the profile button and dropdown
            const profileButton = document.getElementById('profile-button');
            const dropdownUser = document.getElementById('dropdown-user');

            let closeTimer = null;

            function openDropdown() {
                if (closeTimer) {
                    clearTimeout(closeTimer);
                    closeTimer = null;
                }
                dropdownUser.classList.remove('hidden', 'opacity-0', 'scale-95');
                dropdownUser.classList.add('opacity-100', 'scale-100');
            }

            function closeDropdown() {
                dropdownUser.classList.remove('opacity-100', 'scale-100');
                dropdownUser.classList.add('opacity-0', 'scale-95');
                closeTimer = setTimeout(() => dropdownUser.classList.add('hidden'), 140);
            }

            function isOpen() {
                return !dropdownUser.classList.contains('hidden');
            }

            // Toggle dropdown visibility when profile is clicked
            profileButton.addEventListener('click', function(event) {
                event.stopPropagation(); // Prevent event from propagating to document

                if (isOpen()) closeDropdown();
                else openDropdown();
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (!dropdownUser.contains(event.target) && !profileButton.contains(event.target)) {
                    closeDropdown();
                }
            });
        });
    </script>
@endpush
