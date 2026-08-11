<x-app-layout>

    <div class="space-y-6">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
                    Kelola Merchant
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Kelola seluruh merchant yang terdaftar di platform PesanIn.
                </p>
            </div>

            <a href="{{ route('admin.merchants.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-5 py-3 text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400">
                <i class="fa-solid fa-plus"></i>
                Tambah Merchant
            </a>

        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="overflow-x-auto">

                <table class="w-full text-left">

                    <thead class="border-b border-slate-200 bg-slate-50">

                        <tr>

                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                Merchant
                            </th>

                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                No. HP
                            </th>

                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                Status
                            </th>

                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                Langganan
                            </th>

                            <th
                                class="px-6 py-4 text-center text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                Aksi
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @forelse ($merchants as $merchant)

                            <tr class="transition hover:bg-slate-50/70">

                                <td class="px-6 py-5">

                                    <div>

                                        <p class="font-bold text-slate-900">
                                            {{ $merchant->name }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $merchant->slug }}
                                        </p>

                                    </div>

                                </td>

                                <td class="px-6 py-5 text-sm text-slate-600">
                                    {{ $merchant->phone ?? '-' }}
                                </td>

                                <td class="px-6 py-5">

                                    @if ($merchant->is_active)
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-600">

                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>

                                            Aktif

                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-600">

                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>

                                            Nonaktif

                                        </span>
                                    @endif

                                </td>

                                <td class="px-6 py-5 text-sm">

                                    @if ($merchant->subscription_expires_at)
                                        @php
                                            $expired = $merchant->subscription_expires_at->isPast();
                                        @endphp

                                        <span class="{{ $expired ? 'text-rose-600' : 'text-slate-600' }} font-semibold">
                                            {{ $merchant->subscription_expires_at->format('d M Y') }}
                                        </span>

                                        @if ($expired)
                                            <span class="ml-1 text-xs font-bold text-rose-500">
                                                Expired
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-slate-400">
                                            Belum ditentukan
                                        </span>
                                    @endif

                                </td>

                                <td class="px-6 py-5">

                                    <div class="flex items-center justify-center gap-2">

                                        <a href="{{ route('admin.merchants.edit', [
                                            'encryptedId' => \Illuminate\Support\Facades\Crypt::encryptString((string) $merchant->id),
                                        ]) }}"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 transition hover:bg-blue-100"
                                            title="Edit">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </a>

                                        <form
                                            action="{{ route('admin.merchants.destroy', [
                                                'encryptedId' => \Illuminate\Support\Facades\Crypt::encryptString((string) $merchant->id),
                                            ]) }}"
                                            method="POST" class="delete-merchant-form">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-rose-50 text-rose-600 transition hover:bg-rose-100"
                                                title="Hapus">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5" class="px-6 py-16 text-center">

                                    <div
                                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                        <i class="fa-solid fa-store text-xl"></i>
                                    </div>

                                    <h3 class="mt-4 text-sm font-extrabold text-slate-700">
                                        Belum ada merchant
                                    </h3>

                                    <p class="mt-1 text-xs text-slate-400">
                                        Tambahkan merchant pertama untuk mulai menggunakan PesanIn.
                                    </p>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            @if ($merchants->hasPages())
                <div class="border-t border-slate-200 px-6 py-4">
                    {{ $merchants->links() }}
                </div>
            @endif

        </div>

    </div>

    @push('scripts')

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: @json(session('success')),
                    confirmButtonColor: '#f59e0b',
                    confirmButtonText: 'OK'
                });
            </script>
        @endif

        @if ($errors->any())
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    html: @json(implode('<br>', $errors->all())),
                    confirmButtonColor: '#111827',
                    confirmButtonText: 'OK'
                });
            </script>
        @endif

        <script>
            document.querySelectorAll('.delete-merchant-form').forEach(form => {

                form.addEventListener('submit', function(event) {

                    event.preventDefault();

                    Swal.fire({
                        title: 'Hapus Merchant?',
                        text: 'Data merchant yang dihapus tidak dapat dikembalikan.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#e11d48',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {

                        if (result.isConfirmed) {
                            form.submit();
                        }

                    });

                });

            });
        </script>

    @endpush

</x-app-layout>
