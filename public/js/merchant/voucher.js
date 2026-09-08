function confirmDeleteVoucher(id) {
    Swal.fire({
        title: 'Hapus Voucher?',
        text: 'Voucher ini akan dihapus dan tidak dapat digunakan lagi.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {

            const form = document.createElement('form');

            form.method = 'POST';
            form.action = `/merchant/voucher/${id}`;

            const csrf = document.createElement('input');

            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content');

            const method = document.createElement('input');

            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';

            form.appendChild(csrf);
            form.appendChild(method);

            document.body.appendChild(form);

            form.submit();
        }
    });
}