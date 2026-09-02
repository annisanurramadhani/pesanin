document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | ADD TO CART - AJAX
    |--------------------------------------------------------------------------
    */

    const forms = document.querySelectorAll('.add-to-cart-form');

    forms.forEach(form => {

        form.addEventListener('submit', async function (e) {

            e.preventDefault();

            const button = form.querySelector('.add-to-cart-btn');

            if (!button) {
                return;
            }

            const originalContent = button.innerHTML;

            // ================================================================
            // UPDATE COUNT LANGSUNG - OPTIMISTIC UI
            // ================================================================

            const cartCount = document.getElementById('cartCount');

            let previousCartCount = 0;

            if (cartCount) {
                previousCartCount = parseInt(cartCount.textContent, 10) || 0;

                updateCartCount(previousCartCount + 1);
            }


            // Loading
            button.disabled = true;

            button.innerHTML = `
                <i class="fa-solid fa-spinner fa-spin text-[10px]"></i>
                Menambahkan...
            `;

            try {

                const response = await fetch(form.action, {

                    method: 'POST',

                    headers: {
                        'X-CSRF-TOKEN': form.querySelector(
                            'input[name="_token"]'
                        ).value,

                        'Accept': 'application/json',

                        'X-Requested-With': 'XMLHttpRequest'
                    },

                    body: new FormData(form)

                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(
                        data.message || 'Gagal menambahkan menu.'
                    );
                }

                // Kembalikan tombol
                button.disabled = false;

                button.innerHTML = originalContent;

                // Update jumlah keranjang
                if (typeof data.cart_count !== 'undefined') {

                    updateCartCount(data.cart_count);

                }

                // Tampilkan notifikasi berhasil
                showSuccessNotification(
                    data.message || 'Menu berhasil ditambahkan ke keranjang.'
                );

            } catch (error) {

                console.error(error);

                // Kembalikan count jika AJAX gagal
                if (cartCount) {
                    updateCartCount(previousCartCount);
                }

                button.disabled = false;

                button.innerHTML = originalContent;

                showErrorNotification(
                    error.message || 'Terjadi kesalahan.'
                );

            }

        });

    });


    /*
    |--------------------------------------------------------------------------
    | SUCCESS NOTIFICATION
    |--------------------------------------------------------------------------
    */

    window.showSuccessNotification = function (message) {

        // Hapus notif lama kalau masih ada
        const oldNotification =
            document.getElementById('successNotification');

        if (oldNotification) {
            oldNotification.remove();
        }

        const notification = document.createElement('div');

        notification.id = 'successNotification';

        notification.className =
            'fixed left-1/2 top-5 z-50 w-[calc(100%-2rem)] max-w-md -translate-x-1/2';

        notification.innerHTML = `
            <div class="flex items-center gap-3 rounded-2xl border border-emerald-200/70 bg-emerald-50/90 px-4 py-3.5 shadow-lg shadow-emerald-900/10 backdrop-blur-md">

                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-100/90 text-emerald-600">
                    <i class="fa-solid fa-check text-sm"></i>
                </div>

                <div class="min-w-0 flex-1">

                    <p class="text-sm font-extrabold text-emerald-800">
                        Berhasil
                    </p>

                    <p class="mt-0.5 text-xs leading-5 text-emerald-700">
                        ${message}
                    </p>

                </div>

                <button
                    type="button"
                    onclick="closeSuccessNotification()"
                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-emerald-500 transition hover:bg-emerald-100 hover:text-emerald-700"
                    aria-label="Tutup notifikasi"
                >
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>

            </div>
        `;

        document.body.appendChild(notification);

        notification.classList.add('animate-notification-in');

        // Hilang setelah 2 detik
        setTimeout(() => {
            closeSuccessNotification();
        }, 2000);
    };


    /*
    |--------------------------------------------------------------------------
    | ERROR NOTIFICATION
    |--------------------------------------------------------------------------
    */

    window.showErrorNotification = function (message) {

        const notification =
            document.getElementById('successNotification');

        if (notification) {
            notification.remove();
        }

        const errorNotification = document.createElement('div');

        errorNotification.id = 'successNotification';

        errorNotification.className =
            'fixed left-1/2 top-5 z-50 w-[calc(100%-2rem)] max-w-md -translate-x-1/2';

        errorNotification.innerHTML = `
            <div class="flex items-center gap-3 rounded-2xl border border-red-200/70 bg-red-50/90 px-4 py-3.5 shadow-lg shadow-red-900/10 backdrop-blur-md">

                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-100/90 text-red-600">
                    <i class="fa-solid fa-exclamation text-sm"></i>
                </div>

                <div class="min-w-0 flex-1">

                    <p class="text-sm font-extrabold text-red-800">
                        Gagal
                    </p>

                    <p class="mt-0.5 text-xs leading-5 text-red-700">
                        ${message}
                    </p>

                </div>

                <button
                    type="button"
                    onclick="closeSuccessNotification()"
                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-red-500 transition hover:bg-red-100 hover:text-red-700"
                    aria-label="Tutup notifikasi"
                >
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>

            </div>
        `;

        document.body.appendChild(errorNotification);

        errorNotification.classList.add('animate-notification-in');

        setTimeout(() => {
            closeSuccessNotification();
        }, 2000);
    };


    /*
    |--------------------------------------------------------------------------
    | CLOSE NOTIFICATION
    |--------------------------------------------------------------------------
    */

    window.closeSuccessNotification = function () {

        const notification =
            document.getElementById('successNotification');

        if (!notification) {
            return;
        }

        notification.classList.remove(
            'animate-notification-in'
        );

        notification.classList.add(
            'animate-notification-out'
        );

        setTimeout(() => {

            if (notification) {
                notification.remove();
            }

        }, 300);

    };

        /*
    |--------------------------------------------------------------------------
    | CART COUNT
    |--------------------------------------------------------------------------
    */

    window.updateCartCount = function (count) {

        const cartCount = document.getElementById('cartCount');

        if (!cartCount) {
            return;
        }

        count = parseInt(count, 10) || 0;

        cartCount.textContent = count;

        if (count > 0) {

            cartCount.style.display = 'flex';

        } else {

            cartCount.style.display = 'none';

        }

    };
});