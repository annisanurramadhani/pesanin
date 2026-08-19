<nav x-data="{ open: false }" class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-8">
                <!-- Brand Logo Baru (Ganti Logo Laravel) -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('merchant.dashboard') }}" class="flex items-center gap-2.5 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 text-white flex items-center justify-center font-black text-xl shadow-md shadow-blue-200 group-hover:scale-105 transition duration-200">
                            ☕
                        </div>
                        <div class="flex flex-col">
                            <span class="font-black text-lg text-slate-800 tracking-tight leading-none group-hover:text-blue-600 transition">PesanIn<span class="text-blue-600">.</span></span>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 leading-none mt-1">Merchant Hub</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-2 sm:flex">
                    <a href="{{ route('merchant.dashboard') }}" class="px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('merchant.dashboard') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('merchant.qr.index') }}" class="px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('merchant.qr.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        Kelola QR Code
                    </a>
                    <a href="{{ route('merchant.menu.index') }}" class="px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('merchant.menu.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        Kelola Menu
                    </a>
                    <a href="{{ route('merchant.orders.index') }}" class="px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('merchant.orders.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        Riwayat Pesanan
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 border border-slate-200 text-sm font-medium rounded-xl text-slate-700 bg-white hover:bg-slate-50 hover:border-slate-300 focus:outline-none transition shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <div>{{ Auth::user()->name }}</div>
                            <svg class="fill-current h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile Saya') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();" class="text-rose-600 font-semibold">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-slate-400 hover:text-slate-500 hover:bg-slate-100 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-slate-100 bg-white">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <a href="{{ route('merchant.dashboard') }}" class="block px-3 py-2 rounded-lg text-base font-semibold {{ request()->routeIs('merchant.dashboard') ? 'bg-blue-50 text-blue-600' : 'text-slate-600' }}">Dashboard</a>
            <a href="{{ route('merchant.qr.index') }}" class="block px-3 py-2 rounded-lg text-base font-semibold {{ request()->routeIs('merchant.qr.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600' }}">Kelola QR Code</a>
            <a href="{{ route('merchant.menu.index') }}" class="block px-3 py-2 rounded-lg text-base font-semibold {{ request()->routeIs('merchant.menu.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600' }}">Kelola Menu</a>
            <a href="{{ route('merchant.orders.index') }}" class="block px-3 py-2 rounded-lg text-base font-semibold {{ request()->routeIs('merchant.orders.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600' }}">Kelola Pesanan</a>
        </div>
    </div>

    <x-nav-link :href="route('merchant.profile-kafe.edit')" :active="request()->routeIs('merchant.profile-kafe.edit')">
        {{ __('Profil Kafe') }}
    </x-nav-link>
</nav>