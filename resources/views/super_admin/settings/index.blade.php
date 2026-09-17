@extends('layouts.admin')


@section('header')
    <div>

        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
            Pengaturan Website
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Kelola informasi dan tampilan website PesanIn.
        </p>

    </div>
@endsection



@section('content')
    <div class="mx-auto max-w-5xl">

        <form method="POST" action="{{ route('super_admin.settings.update') }}" enctype="multipart/form-data">

            @csrf

            @method('PUT')





            <div class="space-y-6">





                {{-- INFORMASI WEBSITE --}}

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">


                    <div class="border-b border-slate-200 px-6 py-5">


                        <div class="flex items-center gap-3">


                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-500">

                                <i class="fa-solid fa-globe"></i>

                            </div>



                            <div>

                                <h2 class="font-extrabold text-slate-900">
                                    Informasi Website
                                </h2>


                                <p class="text-xs text-slate-400">
                                    Lengkapi identitas utama website PesanIn.
                                </p>


                            </div>


                        </div>


                    </div>



                    <div class="p-6">


                        <div class="grid gap-6 md:grid-cols-2">


                            {{-- Nama Website --}}

                            <div>

                                <label class="mb-2 block text-sm font-bold text-slate-700">
                                    Nama Website
                                </label>


                                <input type="text" name="website_name"
                                    value="{{ old('website_name', $setting->website_name) }}" placeholder="Contoh: PesanIn"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">


                            </div>



                            {{-- Tagline --}}

                            <div>

                                <label class="mb-2 block text-sm font-bold text-slate-700">
                                    Tagline
                                </label>


                                <input type="text" name="tagline" value="{{ old('tagline', $setting->tagline) }}"
                                    placeholder="Contoh: Solusi digital untuk bisnis Anda"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">


                            </div>





                            {{-- Logo Website --}}

                            <div>


                                <label class="mb-2 block text-sm font-bold text-slate-700">
                                    Logo Website
                                </label>



                                <div class="space-y-3">


                                    {{-- Preview Logo --}}

                                    <div
                                        class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-slate-50">


                                        <img id="logo-preview"
                                            src="{{ $setting->logo ? asset('storage/' . $setting->logo) : '' }}"
                                            class="{{ $setting->logo ? '' : 'hidden' }} h-full w-full object-contain">


                                        @if (!$setting->logo)
                                            <i id="logo-placeholder" class="fa-solid fa-image text-xl text-slate-400">
                                            </i>
                                        @endif


                                    </div>





                                    {{-- Upload Logo --}}

                                    <input type="file" name="logo" accept="image/*"
                                        onchange="previewImage(this,'logo-preview','logo-placeholder')"
                                        class="block w-full text-sm text-slate-500

                                                file:mr-4

                                                file:rounded-xl

                                                file:border-0

                                                file:bg-amber-500

                                                file:px-4

                                                file:py-2

                                                file:text-sm

                                                file:font-bold

                                                file:text-slate-950

                                                hover:file:bg-amber-400
                                                ">



                                </div>



                                <p class="mt-2 text-xs text-slate-400">
                                    Format PNG/JPG/WEBP maksimal 2MB.
                                </p>


                            </div>





                            {{-- Favicon --}}

                            <div>


                                <label class="mb-2 block text-sm font-bold text-slate-700">
                                    Favicon
                                </label>



                                <div class="space-y-3">


                                    {{-- Preview --}}

                                    <div
                                        class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-slate-50">


                                        <img id="favicon-preview"
                                            src="{{ $setting->favicon ? asset('storage/' . $setting->favicon) : '' }}"
                                            class="{{ $setting->favicon ? '' : 'hidden' }} h-full w-full object-contain">


                                        @if (!$setting->favicon)
                                            <i id="favicon-placeholder" class="fa-solid fa-globe text-xl text-slate-400">
                                            </i>
                                        @endif


                                    </div>




                                    {{-- Input --}}

                                    <input type="file" name="favicon" accept="image/*"
                                        onchange="previewImage(this,'favicon-preview','favicon-placeholder')"
                                        class="block w-full text-sm text-slate-500
                                                file:mr-4 
                                                file:rounded-xl 
                                                file:border-0 
                                                file:bg-amber-500 
                                                file:px-4 
                                                file:py-2 
                                                file:text-sm 
                                                file:font-bold 
                                                file:text-slate-950

                                                hover:file:bg-amber-400
                                                ">
                                </div>



                                <p class="mt-2 text-xs text-slate-400">
                                    Disarankan ukuran 32x32 atau 64x64 px.
                                </p>


                            </div>

                        </div>


                    </div>


                </div>







                {{-- HERO --}}

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">


                    <div class="border-b border-slate-200 px-6 py-5">


                        <div class="flex items-center gap-3">


                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-500">

                                <i class="fa-solid fa-house"></i>

                            </div>


                            <div>

                                <h2 class="font-extrabold text-slate-900">
                                    Hero Landing Page
                                </h2>


                                <p class="text-xs text-slate-400">
                                    Konten utama halaman depan website.
                                </p>


                            </div>


                        </div>


                    </div>



                    <div class="space-y-6 p-6">


                        <div>

                            <label class="mb-2 block text-sm font-bold text-slate-700">
                                Badge Hero
                            </label>


                            <input type="text" name="hero_badge" value="{{ old('hero_badge', $setting->hero_badge) }}"
                                placeholder="Contoh: Solusi Digital untuk Bisnis Kuliner"
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">


                        </div>


                        {{-- HERO CONTENT --}}
                        <div>


                            <x-rich-text-editor name="hero_content" label="Hero Content" :value="old('hero_content', $setting->hero_content)"
                                placeholder="Masukkan judul utama website..." />


                        </div>


                        {{-- HERO DESCRIPTION --}}
                        <div>

                            <x-rich-text-editor name="hero_description" label="Hero Description" :value="old('hero_description', $setting->hero_description)"
                                placeholder="Masukkan deskripsi website..." />


                        </div>



                    </div>


                </div>







                {{-- CTA --}}

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">


                    <div class="border-b border-slate-200 px-6 py-5">


                        <div class="flex items-center gap-3">


                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-500">

                                <i class="fa-solid fa-bullhorn"></i>

                            </div>


                            <div>

                                <h2 class="font-extrabold text-slate-900">
                                    CTA Section
                                </h2>


                                <p class="text-xs text-slate-400">
                                    Bagian ajakan pengguna pada website.
                                </p>


                            </div>


                        </div>


                    </div>



                    <div class="space-y-6 p-6">

                        {{-- CTA CONTENT --}}
                        <div>

                            <x-rich-text-editor name="cta_content" label="CTA Content" :value="old('cta_content', $setting->cta_content)"
                                placeholder="Contoh: Siap Membuat Bisnis Anda Lebih Mudah?" />


                        </div>

                        {{-- CTA DESCRIPTION --}}
                        <div>

                            <x-rich-text-editor name="cta_description" label="CTA Description" :value="old('cta_description', $setting->cta_description)"
                                placeholder="Deskripsi CTA..." />


                        </div>



                        <div>

                            <label class="mb-2 block text-sm font-bold text-slate-700">
                                Button Text
                            </label>


                            <input type="text" name="cta_button_text"
                                value="{{ old('cta_button_text', $setting->cta_button_text) }}"
                                placeholder="Contoh: Mulai Sekarang"
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">


                        </div>


                    </div>


                </div>







                {{-- FOOTER --}}

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">


                    <div class="border-b border-slate-200 px-6 py-5">


                        <div class="flex items-center gap-3">


                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-500">

                                <i class="fa-solid fa-link"></i>

                            </div>


                            <div>

                                <h2 class="font-extrabold text-slate-900">
                                    Footer & Social Media
                                </h2>


                                <p class="text-xs text-slate-400">
                                    Informasi kontak website.
                                </p>


                            </div>


                        </div>


                    </div>



                    <div class="grid gap-6 p-6 md:grid-cols-2">


                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700">
                                Footer Text
                            </label>

                            <input type="text" name="footer_text"
                                value="{{ old('footer_text', $setting->footer_text) }}"
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">

                        </div>



                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700">
                                Email
                            </label>

                            <input type="email" name="footer_email"
                                value="{{ old('footer_email', $setting->footer_email) }}"
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">

                        </div>



                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700">
                                WhatsApp
                            </label>

                            <input type="text" name="footer_whatsapp"
                                value="{{ old('footer_whatsapp', $setting->footer_whatsapp) }}"
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">

                        </div>



                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700">
                                Instagram
                            </label>

                            <input type="url" name="instagram_url"
                                value="{{ old('instagram_url', $setting->instagram_url) }}"
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">

                        </div>


                    </div>


                </div>






                <div class="flex justify-end">


                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-6 py-3 text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400">

                        <i class="fa-solid fa-save"></i>

                        Simpan Pengaturan

                    </button>


                </div>




            </div>


        </form>


    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/super_admin/settings.js') }}"></script>
@endpush
