@extends('layouts.app')

@section('body')

    <div class="min-h-screen flex">

        @include('components.sidebar.merchant')

        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

            {{-- Header --}}
            <header class="bg-white border-b border-slate-200/80 px-8 py-5 sticky top-0 z-10 shadow-sm">

                <div>

                    <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight">
                        Profil Akun
                    </h2>

                    <p class="text-xs font-medium text-slate-500 mt-1">
                        Kelola informasi profil, password, dan keamanan akun kamu.
                    </p>

                </div>

            </header>


            {{-- Content --}}
            <main class="flex-1 p-8">

                <div class="max-w-5xl mx-auto space-y-6">

                    {{-- Informasi Profil --}}
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm">

                        <div class="px-6 py-5 border-b border-slate-100">

                            <h3 class="font-extrabold text-slate-900">
                                Informasi Profil
                            </h3>

                            <p class="text-xs text-slate-400 mt-1">
                                Perbarui nama dan alamat email akun kamu.
                            </p>

                        </div>

                        <div class="p-6">

                            @include('profile.partials.update-profile-information-form')

                        </div>

                    </div>


                    {{-- Password --}}
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm">

                        <div class="px-6 py-5 border-b border-slate-100">

                            <h3 class="font-extrabold text-slate-900">
                                Ubah Password
                            </h3>

                            <p class="text-xs text-slate-400 mt-1">
                                Gunakan password yang kuat untuk menjaga keamanan akun.
                            </p>

                        </div>

                        <div class="p-6">

                            @include('profile.partials.update-password-form')

                        </div>

                    </div>


                    {{-- Hapus Akun --}}
                    <div class="bg-white rounded-2xl border border-rose-200/80 shadow-sm">

                        <div class="px-6 py-5 border-b border-rose-100">

                            <h3 class="font-extrabold text-rose-700">
                                Hapus Akun
                            </h3>

                            <p class="text-xs text-slate-400 mt-1">
                                Tindakan ini bersifat permanen. Pastikan kamu benar-benar ingin menghapus akun.
                            </p>

                        </div>

                        <div class="p-6">

                            @include('profile.partials.delete-user-form')

                        </div>

                    </div>

                </div>

            </main>

        </div>

    </div>

@endsection
