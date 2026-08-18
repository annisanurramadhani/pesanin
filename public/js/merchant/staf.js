// VALIDASI FORM HAPUS STAF
    document.querySelectorAll('.delete-staff-form').forEach((form) => {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const staffName = this.dataset.staffName;

            Swal.fire({
                icon: 'warning',
                title: 'Hapus Akun Staf?',
                text: `Apakah Anda yakin ingin menghapus akun "${staffName}"?`,
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#111827',
                background: '#ffffff',
                color: '#111827',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl px-5 py-2.5 font-bold',
                    cancelButton: 'rounded-xl px-5 py-2.5 font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
// END VALIDASI FORM HAPUS STAF
