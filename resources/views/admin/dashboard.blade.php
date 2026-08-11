@extends('layouts.admin')

@section('header')

    <div>
        <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight">
            Super Admin Platform
        </h2>

        <p class="text-xs font-medium text-slate-500 mt-1">
            Kelola seluruh mitra kafe & merchant terdaftar di sistem PesanIn.
        </p>
    </div>

@endsection

@section('content')

    <div class="space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">

                <div>

                    <p class="text-xs font-bold text-slate-400 uppercase">
                        Total Merchant Kafe
                    </p>

                    <h3 class="text-3xl font-black text-slate-900 mt-1">
                        {{ \App\Models\Merchant::count() }}
                    </h3>

                </div>

                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-black text-xl">
                    <i class="fa-solid fa-store"></i>
                </div>

            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">

                <div>

                    <p class="text-xs font-bold text-slate-400 uppercase">
                        Total Pengguna Aktif
                    </p>

                    <h3 class="text-3xl font-black text-slate-900 mt-1">
                        {{ \App\Models\User::count() }}
                    </h3>

                </div>

                <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center font-black text-xl">
                    <i class="fa-solid fa-users"></i>
                </div>

            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">

                <div>

                    <p class="text-xs font-bold text-slate-400 uppercase">
                        Total Transaksi Platform
                    </p>

                    <h3 class="text-3xl font-black text-slate-900 mt-1">
                        {{ \App\Models\Order::count() }}
                    </h3>

                </div>

                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-black text-xl">
                    <i class="fa-solid fa-receipt"></i>
                </div>

            </div>

        </div>

    </div>

@endsection
