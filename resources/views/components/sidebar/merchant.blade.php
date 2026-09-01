<aside class="w-64 bg-slate-900 text-slate-300 flex flex-col justify-between shrink-0 h-screen sticky top-0 border-r border-slate-800 select-none z-30">
    
    {{-- BAGIAN ATAS: Logo & Navigasi Menu --}}
    <div class="p-6 space-y-8 overflow-y-auto">
        
        {{-- Brand / Logo --}}
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center font-black text-xl shadow-lg shadow-amber-500/20">
                <i class="fa-solid fa-mug-hot"></i>
            </div>
            <div>
                <h1 class="font-black text-white text-lg leading-tight tracking-tight">Command Center</h1>
                <p class="text-[10px] font-bold text-amber-500 uppercase tracking-widest">PesanIn Dashboard</p>
            </div>
        </div>

        {{-- Group Navigation --}}
        <nav class="space-y-6">
            
            {{-- Menu Utama --}}
            <div class="space-y-1.5">
                <p class="px-3 text-[10px] font-extrabold uppercase tracking-widest text-slate-500 mb-2">Utama</p>
                
                <a href="{{ route('merchant.dashboard') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition duration-150 {{ request()->routeIs('merchant.dashboard') ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-chart-pie text-sm"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('merchant.orders.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition duration-150 {{ request()->routeIs('merchant.orders.*') ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-cart-shopping text-sm"></i>
                    <span>Riwayat Pesanan</span>
                </a>
            </div>

            {{-- Pengaturan Kafe --}}
            <div class="space-y-1.5">
                <p class="px-3 text-[10px] font-extrabold uppercase tracking-widest text-slate-500 mb-2">Pengaturan Kafe</p>

                <a href="{{ route('merchant.qr.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition duration-150 {{ request()->routeIs('merchant.qr.*') ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-qrcode text-sm"></i>
                    <span>Kelola QR Code</span>
                </a>

                <a href="{{ route('merchant.menu.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition duration-150 {{ request()->routeIs('merchant.menu.*') ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-utensils text-sm"></i>
                    <span>Kelola Menu</span>
                </a>

                <a href="{{ route('merchant.staff.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition duration-150 {{ request()->routeIs('merchant.staff.*') ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-users-gear text-sm"></i>
                    <span>Kelola Staf</span>
                </a>
            </div>

        </nav>
    </div>

    {{-- BAGIAN BAWAH (STICKY BOTTOM): User Profile & Logout --}}
    <div class="p-4 border-t border-slate-800/80 bg-slate-950/50">
        <div class="flex items-center justify-between p-2 rounded-xl bg-slate-800/50 border border-slate-800">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-500 border border-amber-500/20 flex items-center justify-center font-bold text-xs shrink-0">
                    <i class="fa-solid fa-user-gear"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'Owner PST' }}</p>
                    <p class="text-[10px] font-bold text-amber-500 uppercase tracking-wider">{{ auth()->user()->role ?? 'Owner' }}</p>
                </div>
            </div>

            {{-- Form Logout --}}
            <form action="{{ route('logout') }}" method="POST" class="shrink-0">
                @csrf
                <button type="submit" 
                        class="w-8 h-8 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 flex items-center justify-center transition"
                        title="Keluar / Logout">
                    <i class="fa-solid fa-right-from-bracket text-xs"></i>
                </button>
            </form>
        </div>
    </div>

</aside>