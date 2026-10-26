
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.kyc-bank-confirm-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const isApprove = form.dataset.action === 'approve';
            const merchant = form.dataset.merchant || 'merchant ini';

            Swal.fire({
                title: isApprove ? 'Setujui Rekening?' : 'Tolak Rekening?',
                text: isApprove
                    ? `Setujui rekening ${merchant}? Pastikan data rekening telah diperiksa.`
                    : `Tolak pengajuan rekening ${merchant}? Merchant perlu mengajukan kembali.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: isApprove ? 'Ya, Setujui' : 'Ya, Tolak',
                cancelButtonText: 'Batal',
                confirmButtonColor: isApprove ? '#16a34a' : '#dc2626',
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
