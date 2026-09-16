// =========================================================
// MERCHANT ORDERS JAVASCRIPT
// =========================================================

(function () {

    // =========================================================
    // FORMAT RUPIAH
    // =========================================================

    window.formatRupiah = function (value) {
        return 'Rp ' + Number(value).toLocaleString('id-ID');
    };


    // =========================================================
    // MODAL PEMBAYARAN CASH
    // =========================================================

    let cashPaymentTotal = 0;

    window.openCashPaymentModal = function (
        encryptedId,
        total,
        orderNumber
    ) {
        cashPaymentTotal = Number(total);

        const modal = document.getElementById('cashPaymentModal');
        const form = document.getElementById('cashPaymentForm');
        const input = document.getElementById('cashReceivedInput');

        if (!modal || !form || !input) {
            return;
        }

        form.action =
            '/merchant/orders/' +
            encryptedId +
            '/payment';

        const orderElement =
            document.getElementById('cashPaymentOrder');

        if (orderElement) {
            orderElement.textContent = '#' + orderNumber;
        }

        const totalElement =
            document.getElementById('cashPaymentTotal');

        if (totalElement) {
            totalElement.textContent =
                formatRupiah(cashPaymentTotal);
        }

        input.value = '';

        const hiddenInput =
            document.getElementById('cashReceivedHidden');

        if (hiddenInput) {
            hiddenInput.value = '';
        }

        const changeElement =
            document.getElementById('cashPaymentChange');

        if (changeElement) {
            changeElement.textContent = 'Rp 0';
        }

        const errorElement =
            document.getElementById('cashPaymentError');

        if (errorElement) {
            errorElement.textContent = '';
            errorElement.classList.add('hidden');
        }

        const submitButton =
            document.getElementById('cashPaymentSubmit');

        if (submitButton) {
            submitButton.disabled = true;
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        setTimeout(function () {
            input.focus();
        }, 100);
    };


    window.closeCashPaymentModal = function () {
        const modal =
            document.getElementById('cashPaymentModal');

        if (!modal) {
            return;
        }

        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };


    // =========================================================
    // HITUNG KEMBALIAN CASH
    // =========================================================

    const cashReceivedInput =
        document.getElementById('cashReceivedInput');

    if (cashReceivedInput) {

        cashReceivedInput.addEventListener(
            'input',
            function () {

                const received =
                    Number(this.value) || 0;

                const change =
                    received - cashPaymentTotal;

                const changeElement =
                    document.getElementById(
                        'cashPaymentChange'
                    );

                const errorElement =
                    document.getElementById(
                        'cashPaymentError'
                    );

                const submitButton =
                    document.getElementById(
                        'cashPaymentSubmit'
                    );

                const hiddenInput =
                    document.getElementById(
                        'cashReceivedHidden'
                    );

                if (hiddenInput) {
                    hiddenInput.value = received;
                }

                if (received <= 0) {

                    if (changeElement) {
                        changeElement.textContent = 'Rp 0';
                    }

                    if (errorElement) {
                        errorElement.textContent = '';
                        errorElement.classList.add('hidden');
                    }

                    if (submitButton) {
                        submitButton.disabled = true;
                    }

                    return;
                }

                if (received < cashPaymentTotal) {

                    if (changeElement) {
                        changeElement.textContent = 'Rp 0';
                    }

                    if (errorElement) {
                        errorElement.textContent =
                            'Uang pelanggan kurang ' +
                            formatRupiah(
                                cashPaymentTotal - received
                            );

                        errorElement.classList.remove('hidden');
                    }

                    if (submitButton) {
                        submitButton.disabled = true;
                    }

                    return;
                }

                if (errorElement) {
                    errorElement.textContent = '';
                    errorElement.classList.add('hidden');
                }

                if (changeElement) {
                    changeElement.textContent =
                        formatRupiah(change);
                }

                if (submitButton) {
                    submitButton.disabled = false;
                }
            }
        );
    }


    // =========================================================
    // KLIK DI LUAR MODAL CASH
    // =========================================================

    const cashPaymentModal =
        document.getElementById('cashPaymentModal');

    if (cashPaymentModal) {

        cashPaymentModal.addEventListener(
            'click',
            function (event) {

                if (event.target === this) {
                    closeCashPaymentModal();
                }

            }
        );
    }


    // =========================================================
    // FILTER PESANAN
    // =========================================================

    window.switchFilterMode = function (mode) {

        document
            .getElementById('inputDayWrapper')
            ?.classList.add('hidden');

        document
            .getElementById('inputMonthWrapper')
            ?.classList.add('hidden');

        document
            .getElementById('inputYearWrapper')
            ?.classList.add('hidden');

        if (mode === 'day') {

            document
                .getElementById('inputDayWrapper')
                ?.classList.remove('hidden');

        } else if (mode === 'month') {

            document
                .getElementById('inputMonthWrapper')
                ?.classList.remove('hidden');

        } else if (mode === 'year') {

            document
                .getElementById('inputYearWrapper')
                ?.classList.remove('hidden');
        }
    };


    // =========================================================
    // CANCEL MAKANAN
    // =========================================================

    document.addEventListener(
        'submit',
        function (event) {

            const form =
                event.target.closest('.cancel-food-form');

            if (!form) {
                return;
            }

            event.preventDefault();

            if (typeof Swal === 'undefined') {
                form.submit();
                return;
            }

            Swal.fire({

                icon: 'warning',

                title: 'Cancel Makanan?',

                text: 'Pesanan ini akan dibatalkan.',

                showCancelButton: true,

                confirmButtonText: 'Ya, Cancel',

                cancelButtonText: 'Batal',

                confirmButtonColor: '#e11d48',

                cancelButtonColor: '#64748b',

                background: '#ffffff',

                color: '#111827',

                customClass: {
                    popup: 'rounded-2xl',

                    confirmButton:
                        'rounded-xl px-5 py-2.5 font-bold',

                    cancelButton:
                        'rounded-xl px-5 py-2.5 font-bold'
                }

            }).then(function (result) {

                if (result.isConfirmed) {
                    form.submit();
                }

            });
        }
    );


    // =========================================================
    // MODAL DETAIL STATUS PESANAN
    // =========================================================

    window.openOrderStatusModal = function (
        orderNumber,
        orderId
    ) {

        const row =
            document.querySelector(
                `tr[data-order-id="${orderId}"]`
            );

        if (!row) {
            console.error(
                'Baris order tidak ditemukan:',
                orderId
            );
            return;
        }

        let units = [];

        try {

            units = JSON.parse(
                row.dataset.orderUnits || '[]'
            );

        } catch (error) {

            console.error(
                'Gagal membaca data unit:',
                error
            );

            return;
        }

        const modal =
            document.getElementById(
                'orderStatusModal'
            );

        const orderNumberElement =
            document.getElementById(
                'orderStatusOrderNumber'
            );

        const list =
            document.getElementById(
                'orderStatusUnitList'
            );

        const summary =
            document.getElementById(
                'orderStatusSummary'
            );

        if (
            !modal ||
            !orderNumberElement ||
            !list ||
            !summary
        ) {

            console.error(
                'Element modal status tidak ditemukan.'
            );

            return;
        }

        orderNumberElement.textContent =
            '#' + orderNumber;

        list.innerHTML = '';

        let completed = 0;
        let cancelled = 0;
        let processing = 0;
        let pending = 0;

        units.forEach(function (unit) {

            let statusText = '';
            let statusClass = '';
            let icon = '';

            if (unit.status === 'completed') {

                completed++;

                statusText = 'Selesai';

                statusClass =
                    'bg-emerald-100 text-emerald-700';

                icon =
                    '<i class="fa-solid fa-check mr-1"></i>';

            } else if (unit.status === 'cancelled') {

                cancelled++;

                statusText = 'Bahan Habis';

                statusClass =
                    'bg-rose-100 text-rose-700';

                icon =
                    '<i class="fa-solid fa-box-open mr-1"></i>';

            } else if (unit.status === 'processing') {

                processing++;

                statusText = 'Diproses';

                statusClass =
                    'bg-blue-100 text-blue-700';

                icon =
                    '<i class="fa-solid fa-fire mr-1"></i>';

            } else {

                pending++;

                statusText = 'Menunggu';

                statusClass =
                    'bg-amber-100 text-amber-700';

                icon =
                    '<i class="fa-regular fa-clock mr-1"></i>';
            }

            const item =
                document.createElement('div');

            item.className =
                'flex items-center justify-between ' +
                'gap-3 rounded-xl border ' +
                'border-slate-100 bg-slate-50 p-3';

            item.innerHTML = `

                <div class="flex items-center gap-3 min-w-0">

                    <div
                        class="w-9 h-9
                               rounded-xl
                               bg-white
                               border border-slate-100
                               flex items-center
                               justify-center
                               text-slate-400
                               shrink-0"
                    >
                        <i class="fa-solid fa-utensils"></i>
                    </div>

                    <div class="min-w-0">

                        <p
                            class="text-xs
                                   font-black
                                   text-slate-800
                                   truncate"
                        >
                            ${escapeHtml(unit.menu)}
                        </p>

                        <p
                            class="text-[10px]
                                   font-semibold
                                   text-slate-400"
                        >
                            Unit ${escapeHtml(unit.unit)}
                        </p>

                    </div>

                </div>

                <span
                    class="px-2.5 py-1
                           rounded-lg
                           text-[10px]
                           font-black
                           whitespace-nowrap
                           ${statusClass}"
                >
                    ${icon}
                    ${statusText}
                </span>

            `;

            list.appendChild(item);
        });

        const summaryParts = [];

        if (completed > 0) {
            summaryParts.push(`${completed} selesai`);
        }

        if (cancelled > 0) {
            summaryParts.push(`${cancelled} bahan habis`);
        }

        if (processing > 0) {
            summaryParts.push(`${processing} diproses`);
        }

        if (pending > 0) {
            summaryParts.push(`${pending} menunggu`);
        }

        summary.textContent =
            summaryParts.join(' • ');

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };


    // =========================================================
    // TUTUP MODAL DETAIL
    // =========================================================

    window.closeOrderStatusModal = function () {

        const modal =
            document.getElementById(
                'orderStatusModal'
            );

        if (!modal) {
            return;
        }

        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };


    // =========================================================
    // ESCAPE HTML
    // =========================================================

    window.escapeHtml = function (value) {

        const div =
            document.createElement('div');

        div.textContent =
            value ?? '';

        return div.innerHTML;
    };


    // =========================================================
    // KLIK DI LUAR MODAL DETAIL
    // =========================================================

    document.addEventListener(
        'click',
        function (event) {

            const modal =
                document.getElementById(
                    'orderStatusModal'
                );

            if (
                modal &&
                event.target === modal
            ) {
                closeOrderStatusModal();
            }
        }
    );


    // =========================================================
    // TOMBOL ESC
    // =========================================================

    document.addEventListener(
        'keydown',
        function (event) {

            if (event.key === 'Escape') {
                closeOrderStatusModal();
            }

        }
    );


    // =========================================================
    // DYNAMIC ORDER
    // =========================================================

    const ordersBody =
        document.querySelector(
            '[data-orders-body]'
        );

    if (!ordersBody) {
        return;
    }

    const userRole =
        ordersBody.dataset.ordersRole;

    if (
        userRole !== 'kasir' &&
        userRole !== 'dapur' &&
        userRole !== 'owner'
    ) {
        return;
    }


    // =========================================================
    // SIGNATURE ORDER
    // =========================================================

    function getOrderSignature(row) {

        const orderId =
            String(
                row.dataset.orderId || ''
            );

        const paymentMethod =
            String(
                row.dataset.orderPaymentMethod || ''
            ).toLowerCase();

        const paymentStatus =
            String(
                row.dataset.orderPaymentStatus || ''
            ).toLowerCase();

        const orderStatus =
            String(
                row.dataset.orderStatus || ''
            ).toLowerCase();

        let units = [];

        try {

            units = JSON.parse(
                row.dataset.orderUnits || '[]'
            );

        } catch (error) {

            units = [];
        }

        const unitSignature =
            units
                .map(function (unit) {

                    return [
                        String(
                            unit.id ??
                            unit.unit_id ??
                            unit.unit ??
                            ''
                        ),

                        String(
                            unit.status ?? ''
                        ).toLowerCase()

                    ].join(':');

                })
                .sort()
                .join('|');

        return [
            orderId,
            paymentMethod,
            paymentStatus,
            orderStatus,
            unitSignature
        ].join('::');
    }


    // =========================================================
    // AMBIL SIGNATURE TABEL
    // =========================================================

    function getCurrentOrderSignatures(body) {

        if (!body) {
            return [];
        }

        const rows =
            body.querySelectorAll(
                'tr[data-order-id]'
            );

        return Array.from(rows)
            .map(function (row) {
                return getOrderSignature(row);
            })
            .filter(Boolean)
            .sort();
    }


    // =========================================================
    // BASELINE AWAL
    // =========================================================

    let previousOrders =
        getCurrentOrderSignatures(
            ordersBody
        );


    // =========================================================
    // STATUS REQUEST
    // =========================================================

    let isChecking = false;


    // =========================================================
    // BANDINGKAN ARRAY
    // =========================================================

    function areArraysEqual(first, second) {

        if (first.length !== second.length) {
            return false;
        }

        for (
            let i = 0;
            i < first.length;
            i++
        ) {

            if (first[i] !== second[i]) {
                return false;
            }
        }

        return true;
    }


    // =========================================================
    // CEK ORDER TERBARU
    // =========================================================

    async function checkOrders() {

        if (isChecking) {
            return;
        }

        isChecking = true;

        try {

            const response =
                await fetch(
                    window.location.href,
                    {
                        method: 'GET',

                        headers: {
                            'X-Requested-With':
                                'XMLHttpRequest',

                            'Accept':
                                'text/html,application/xhtml+xml'
                        },

                        cache: 'no-store'
                    }
                );

            if (!response.ok) {
                return;
            }

            const html =
                await response.text();

            const parser =
                new DOMParser();

            const parsedDocument =
                parser.parseFromString(
                    html,
                    'text/html'
                );

            const newBody =
                parsedDocument.querySelector(
                    '[data-orders-body]'
                );

            if (!newBody) {
                return;
            }

            const newSignatures =
                getCurrentOrderSignatures(
                    newBody
                );

            const hasChanged =
                !areArraysEqual(
                    previousOrders,
                    newSignatures
                );

            if (!hasChanged) {
                return;
            }

            const currentBody =
                document.querySelector(
                    '[data-orders-body]'
                );

            if (!currentBody) {
                return;
            }

            currentBody.innerHTML =
                newBody.innerHTML;

            previousOrders =
                getCurrentOrderSignatures(
                    currentBody
                );

        } catch (error) {

            console.error(
                'Realtime order error:',
                error
            );

        } finally {

            isChecking = false;
        }
    }


    // =========================================================
    // POLLING REALTIME
    // =========================================================

    setInterval(
        checkOrders,
        1000
    );

})();
