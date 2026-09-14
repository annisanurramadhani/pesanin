// ==========================================================================
// VALIDASI FORM HAPUS STAF
// ==========================================================================

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


// ==========================================================================
// SWEETALERT LIMIT SUBSCRIPTION
// ==========================================================================

document.addEventListener('DOMContentLoaded', function () {

    // Data limit dikirim dari StaffController
    const limitData = window.limitReachedData;

    // Kalau tidak ada limit, jangan lakukan apa-apa
    if (!limitData) {
        return;
    }

    let resourceName = 'fitur';

    switch (limitData.type) {
        case 'staff':
            resourceName = 'karyawan';
            break;

        case 'menu':
            resourceName = 'menu';
            break;

        case 'qr':
            resourceName = 'QR Code';
            break;
    }

    Swal.fire({
        icon: 'warning',

        title: limitData.title,

        html: `
            <div class="text-sm text-slate-600 leading-6">
                ${limitData.message}
            </div>

            <div
                class="mt-5 px-4 py-3 rounded-xl border border-amber-200 bg-amber-50 text-amber-700 text-sm font-semibold"
            >
                <i class="fa-solid fa-crown mr-1"></i>
                Upgrade langganan untuk membuat lebih banyak ${resourceName}.
            </div>
        `,

        showCancelButton: true,

        confirmButtonText:
            '<i class="fa-solid fa-crown mr-1"></i> Upgrade Langganan',

        cancelButtonText: 'Nanti',

        reverseButtons: true,

        buttonsStyling: false,

        customClass: {
            popup: 'rounded-2xl',

            confirmButton:
                'bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl px-5 py-3 font-extrabold mx-1',

            cancelButton:
                'bg-slate-700 hover:bg-slate-600 text-white rounded-xl px-5 py-3 font-extrabold mx-1'
        }
    }).then((result) => {

        if (result.isConfirmed) {

            // Route subscription akan kita isi setelah dicek
            window.location.href = '/subscription/';

        }

    });
});
