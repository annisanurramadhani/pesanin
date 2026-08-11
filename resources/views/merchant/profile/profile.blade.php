<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengaturan Profil Kafe') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            <div class="p-6 bg-white shadow-sm sm:rounded-2xl border border-gray-100">
                <form action="{{ route('merchant.profile-kafe.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Nama Kafe -->
                    <div>
                        <x-input-label for="name" :value="__('Nama Kafe / Resto')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $merchant->name ?? '')" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <!-- Nomor HP / WhatsApp -->
                    <div>
                        <x-input-label for="phone" :value="__('Nomor HP / WhatsApp')" />
                        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $merchant->phone ?? '')" placeholder="08123456789" />
                        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                    </div>

                    <!-- Alamat Kafe -->
                    <div>
                        <x-input-label for="address" :value="__('Alamat Lengkap Kafe')" />
                        <textarea id="address" name="address" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Jl. Veteran No. 10, Purwakarta">{{ old('address', $merchant->address ?? '') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('address')" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>