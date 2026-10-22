
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.approve-identity-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            Swal.fire({
                title: 'Setujui Identitas?',
                text: `Apakah kamu yakin ingin menyetujui identitas ${form.dataset.merchant || 'merchant ini'}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                reverseButtons: true,
                background: '#ffffff',
                color: '#111827',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl px-5 py-2.5 font-bold',
                    cancelButton: 'rounded-xl px-5 py-2.5 font-bold'
                }
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
