<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('success'))

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: @json(session('success')),
                confirmButtonText: 'OK',
                confirmButtonColor: '#f59e0b',
                background: '#ffffff',
                color: '#111827',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl px-5 py-2.5 font-bold'
                }
            });
        });
    </script>

@endif

@if (session('error'))

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: @json(session('error')),
                confirmButtonText: 'OK',
                confirmButtonColor: '#111827',
                background: '#ffffff',
                color: '#111827',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl px-5 py-2.5 font-bold'
                }
            });
        });
    </script>

@endif

@if ($errors->any())

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal!',
                text: @json($errors->first()),
                confirmButtonText: 'OK',
                confirmButtonColor: '#111827',
                background: '#ffffff',
                color: '#111827',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl px-5 py-2.5 font-bold'
                }
            });
        });
    </script>

@endif
