<aside
    class="w-64 bg-[#111827] text-slate-300 flex flex-col justify-between shrink-0 min-h-screen border-r border-slate-800 shadow-2xl">

    <div>

        <div class="px-6 py-6 flex items-center gap-3 border-b border-slate-800/80">

            <div
                class="w-9 h-9 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center font-black text-lg shadow-lg shadow-amber-500/30">
                <i class="fa-solid fa-mug-hot"></i>
            </div>

            <div>

                <h1 class="font-bold text-white text-base tracking-wide">
                    Command Center
                </h1>

                <p class="text-[10px] text-slate-400 tracking-wider uppercase font-semibold">
                    PesanIn Dashboard
                </p>

            </div>

        </div>

        <nav class="px-4 py-6 space-y-2">

            <a href="{{ route('super_admin.dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
                {{ request()->routeIs('super_admin.dashboard')
                    ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
                    : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-chart-pie w-5"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('super_admin.merchants.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
                {{ request()->routeIs('super_admin.merchants.*')
                    ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
                    : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-store w-5"></i>
                <span>Kelola Merchant</span>
            </a>
            <a href="{{ route('super_admin.packages.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
        {{ request()->routeIs('super_admin.packages.*')
            ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
            : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-box-open w-5"></i>
                <span>Kelola Paket</span>
            </a>

            <a href="{{ route('super_admin.subscriptions.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
        {{ request()->routeIs('super_admin.subscriptions.*')
            ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
            : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-credit-card w-5"></i>
                <span>Kelola Langganan</span>
            </a>


        </nav>

    </div>

    <div class="p-4 border-t border-slate-800/80">

        <div class="flex items-center justify-between bg-slate-900/80 p-3 rounded-xl border border-slate-800">

            <div class="flex items-center gap-3 overflow-hidden">

                <div
                    class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-500 flex items-center justify-center font-bold text-xs shrink-0">
                    <i class="fa-solid fa-user"></i>
                </div>

                <div class="truncate">

                    <p class="text-xs font-bold text-white truncate">
                        {{ Auth::user()->name }}
                    </p>

                    <span
                        class="inline-block text-[9px] font-black uppercase tracking-wider text-amber-400 bg-amber-500/10 px-1.5 py-0.5 rounded border border-amber-500/20">
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
