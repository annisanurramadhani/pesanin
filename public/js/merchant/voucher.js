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

// ==========================================
// DYNAMIC VOUCHER USAGE & STATUS
// ==========================================

function loadVoucherData() {
    fetch('/merchant/voucher/data', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Gagal mengambil data voucher.');
            }

            return response.json();
        })
        .then(data => {

            if (!data.vouchers) {
                return;
            }

            data.vouchers.forEach(voucher => {

                const row = document.querySelector(
                    `[data-voucher-id="${voucher.id}"]`
                );

                if (!row) {
                    return;
                }

                // ==============================
                // UPDATE PENGGUNAAN
                // ==============================

                const usageElement = row.querySelector(
                    '[data-voucher-usage]'
                );

                if (usageElement) {

                    if (voucher.usage_limit !== null) {

                        usageElement.innerHTML = `
                            <span class="font-semibold text-slate-700">
                                ${voucher.used_count}
                            </span>

                            <span class="text-slate-400">
                                / ${voucher.usage_limit}
                            </span>
                        `;

                    } else {

                        usageElement.innerHTML = `
                            <span class="font-semibold text-slate-700">
                                ${voucher.used_count}
                            </span>

                            <span class="text-xs text-slate-400 ml-1">
                                Tidak terbatas
                            </span>
                        `;

                    }
                }


                // ==============================
                // UPDATE STATUS
                // ==============================

                const statusElement = row.querySelector(
                    '[data-voucher-status]'
                );

                if (statusElement) {

                    if (voucher.status === 'active') {

                        statusElement.innerHTML = `
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-black uppercase tracking-wide">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Aktif
                            </span>
                        `;

                    } else {

                        statusElement.innerHTML = `
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 border border-slate-200 text-[10px] font-black uppercase tracking-wide">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                Nonaktif
                            </span>
                        `;

                    }
                }

            });

        })
        .catch(error => {
            console.error('Voucher dynamic update error:', error);
        });
}


// Jalankan pertama kali saat halaman dibuka
loadVoucherData();

// Cek perubahan setiap 1 detik
setInterval(loadVoucherData, 1000);