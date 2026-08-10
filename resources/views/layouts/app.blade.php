<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Command Center - PesanIn</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts & FontAwesome Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F3F4F8] text-slate-800 antialiased font-sans">

    <div class="min-h-screen flex">
        
        <!-- SIDEBAR KIRI -->
        <aside class="w-64 bg-[#111827] text-slate-300 flex flex-col justify-between shrink-0 min-h-screen border-r border-slate-800 shadow-2xl">
            <div>
                <!-- Header Brand / Command Center -->
                <div class="px-6 py-6 flex items-center gap-3 border-b border-slate-800/80">
                    <div class="w-9 h-9 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center font-black text-lg shadow-lg shadow-amber-500/30">
                        <i class="fa-solid fa-mug-hot"></i>
                    </div>
                    <div>
                        <h1 class="font-bold text-white text-base tracking-wide flex items-center gap-1">
                            Command Center
                        </h1>
                        <p class="text-[10px] text-slate-400 tracking-wider uppercase font-semibold">PesanIn Dashboard</p>
                    </div>
                </div>

                <!-- Menu Navigasi Berdasarkan 4 Role -->
                <nav class="px-4 py-6 space-y-2">

                    <!-- 1. KHUSUS SUPER ADMIN -->
                    @if(Auth::user()->role === 'super_admin')
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <i class="fa-solid fa-user-shield w-5"></i>
                            <span>Kelola Merchant</span>
                        </a>
                    @endif

                    <!-- 2. DASHBOARD (Bisa diakses Owner & Kasir) -->
                    @if(in_array(Auth::user()->role, ['owner', 'kasir']))
                        <a href="{{ route('merchant.dashboard') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200 {{ request()->routeIs('merchant.dashboard') ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <i class="fa-solid fa-chart-pie w-5"></i>
                            <span>Dashboard</span>
                        </a>
                    @endif

                    <!-- 3. KELOLA PESANAN (Bisa diakses Owner, Kasir, & Dapur) -->
                    @if(in_array(Auth::user()->role, ['owner', 'kasir', 'dapur']))
                        <a href="{{ route('merchant.orders.index') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200 {{ request()->routeIs('merchant.orders.*') ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <i class="fa-solid fa-cart-shopping w-5"></i>
                            <span>Kelola Pesanan</span>
                        </a>
                    @endif

                    <!-- 4. KHUSUS OWNER (Kelola QR & Menu) -->
                    @if(Auth::user()->role === 'owner')
                        <div class="pt-4 pb-1">
                            <p class="px-4 text-[10px] font-black uppercase tracking-wider text-slate-500">Pengaturan Kafe</p>
                        </div>

                        <a href="{{ route('merchant.qr.index') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200 {{ request()->routeIs('merchant.qr.*') ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <i class="fa-solid fa-qrcode w-5"></i>
                            <span>Kelola QR Code</span>
                        </a>

                        <a href="{{ route('merchant.menu.index') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200 {{ request()->routeIs('merchant.menu.*') ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <i class="fa-solid fa-utensils w-5"></i>
                            <span>Kelola Menu</span>
                        </a>

                        <!-- MENU KELOLA STAF BARU -->
                        <a href="{{ route('merchant.staff.index') }}" 
                        class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200 {{ request()->routeIs('merchant.staff.*') ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <i class="fa-solid fa-users-gear w-5"></i>
                            <span>Kelola Staf Kafe</span>
                        </a>
                    @endif

                </nav>
            </div>

            <!-- Profile User & Logout Bottom Sidebar (Dilengkapi Label Badge Role) -->
            <div class="p-4 border-t border-slate-800/80">
                <div class="flex items-center justify-between bg-slate-900/80 p-3 rounded-xl border border-slate-800">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-500 flex items-center justify-center font-bold text-xs shrink-0">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div class="truncate">
                            <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</p>
                            <span class="inline-block text-[9px] font-black uppercase tracking-wider text-amber-400 bg-amber-500/10 px-1.5 py-0.5 rounded border border-amber-500/20">
                                {{ str_replace('_', ' ', Auth::user()->role) }}
                            </span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-400 transition" title="Logout">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- KONTEN UTAMA HALAMAN -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            @if (isset($header))
                <header class="bg-white border-b border-slate-200/80 px-8 py-5 sticky top-0 z-10 shadow-sm">
                    {{ $header }}
                </header>
            @endif

            <main class="flex-1 p-8">
                {{ $slot }}
            </main>
        </div>

    </div>

</body>
</html>