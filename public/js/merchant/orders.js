// =========================================================
// MERCHANT ORDERS JAVASCRIPT
// =========================================================
// Berisi seluruh JavaScript halaman Kelola Pesanan / Riwayat Pesanan.
// Termasuk:
// - Pembayaran Cash
// - Filter Pesanan
// - Konfirmasi Cancel
// - Modal Detail Status
// - Dynamic Order
// =========================================================


(function () {

    /*
    |--------------------------------------------------------------------------
    | FORMAT RUPIAH
    |--------------------------------------------------------------------------
    */

    window.formatRupiah = function (value) {

        return 'Rp ' + Number(value).toLocaleString(
            'id-ID'
        );

    };


    /*
    |--------------------------------------------------------------------------
    | MODAL PEMBAYARAN CASH
    |--------------------------------------------------------------------------
    */

    let cashPaymentTotal = 0;


    /*
    |--------------------------------------------------------------------------
    | BUKA MODAL PEMBAYARAN CASH
    |--------------------------------------------------------------------------
    */

    window.openCashPaymentModal = function (
        encryptedId,
        total,
        orderNumber
    ) {

        cashPaymentTotal = Number(total);


        const modal =
            document.getElementById(
                'cashPaymentModal'
            );

        const form =
            document.getElementById(
                'cashPaymentForm'
            );

        const input =
            document.getElementById(
                'cashReceivedInput'
            );


        if (
            !modal ||
            !form ||
            !input
        ) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | SET ACTION FORM
        |--------------------------------------------------------------------------
        */

        form.action =
            '/merchant/orders/' +
            encryptedId +
            '/payment';


        /*
        |--------------------------------------------------------------------------
        | TAMPILKAN DATA
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            'cashPaymentOrder'
        ).textContent =
            '#' + orderNumber;


        document.getElementById(
            'cashPaymentTotal'
        ).textContent =
            formatRupiah(
                cashPaymentTotal
            );


        /*
        |--------------------------------------------------------------------------
        | RESET
        |--------------------------------------------------------------------------
        */

        input.value = '';


        const hiddenInput =
            document.getElementById(
                'cashReceivedHidden'
            );

        if (hiddenInput) {
            hiddenInput.value = '';
        }


        const changeElement =
            document.getElementById(
                'cashPaymentChange'
            );

        if (changeElement) {
            changeElement.textContent =
                'Rp 0';
        }


        const errorElement =
            document.getElementById(
                'cashPaymentError'
            );

        if (errorElement) {

            errorElement.textContent = '';

            errorElement.classList.add(
                'hidden'
            );
        }


        const submitButton =
            document.getElementById(
                'cashPaymentSubmit'
            );

        if (submitButton) {

            submitButton.disabled =
                true;

        }


        /*
        |--------------------------------------------------------------------------
        | TAMPILKAN MODAL
        |--------------------------------------------------------------------------
        */

        modal.classList.remove(
            'hidden'
        );

        modal.classList.add(
            'flex'
        );


        /*
        |--------------------------------------------------------------------------
        | FOCUS INPUT
        |--------------------------------------------------------------------------
        */

        setTimeout(function () {

            input.focus();

        }, 100);

    };


    /*
    |--------------------------------------------------------------------------
    | TUTUP MODAL PEMBAYARAN CASH
    |--------------------------------------------------------------------------
    */

    window.closeCashPaymentModal = function () {

        const modal =
            document.getElementById(
                'cashPaymentModal'
            );


        if (!modal) {
            return;
        }


        modal.classList.add(
            'hidden'
        );

        modal.classList.remove(
            'flex'
        );

    };


    /*
    |--------------------------------------------------------------------------
    | HITUNG KEMBALIAN
    |--------------------------------------------------------------------------
    */

    const cashReceivedInput =
        document.getElementById(
            'cashReceivedInput'
        );


    if (cashReceivedInput) {

        cashReceivedInput.addEventListener(
            'input',
            function () {

                const received =
                    Number(
                        this.value
                    ) || 0;


                const change =
                    received -
                    cashPaymentTotal;


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


                /*
                |--------------------------------------------------------------------------
                | SIMPAN NILAI
                |--------------------------------------------------------------------------
                */

                if (hiddenInput) {

                    hiddenInput.value =
                        received;

                }


                /*
                |--------------------------------------------------------------------------
                | BELUM DIISI
                |--------------------------------------------------------------------------
                */

                if (
                    received <= 0
                ) {

                    if (changeElement) {

                        changeElement.textContent =
                            'Rp 0';

                    }


                    if (errorElement) {

                        errorElement.textContent =
                            '';

                        errorElement.classList.add(
                            'hidden'
                        );

                    }


                    if (submitButton) {

                        submitButton.disabled =
                            true;

                    }

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | UANG KURANG
                |--------------------------------------------------------------------------
                */

                if (
                    received <
                    cashPaymentTotal
                ) {

                    if (changeElement) {

                        changeElement.textContent =
                            'Rp 0';

                    }


                    if (errorElement) {

                        errorElement.textContent =
                            'Uang pelanggan kurang ' +
                            formatRupiah(
                                cashPaymentTotal -
                                received
                            );

                        errorElement.classList.remove(
                            'hidden'
                        );

                    }


                    if (submitButton) {

                        submitButton.disabled =
                            true;

                    }

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | UANG CUKUP / LEBIH
                |--------------------------------------------------------------------------
                */

                if (errorElement) {

                    errorElement.classList.add(
                        'hidden'
                    );

                }


                if (changeElement) {

                    changeElement.textContent =
                        formatRupiah(
                            change
                        );

                }


                if (submitButton) {

                    submitButton.disabled =
                        false;

                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | KLIK DI LUAR MODAL PEMBAYARAN
    |--------------------------------------------------------------------------
    */

    const cashPaymentModal =
        document.getElementById(
            'cashPaymentModal'
        );


    if (cashPaymentModal) {

        cashPaymentModal.addEventListener(
            'click',
            function (event) {

                if (
                    event.target === this
                ) {

                    closeCashPaymentModal();

                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | FILTER PESANAN
    |--------------------------------------------------------------------------
    */

    window.switchFilterMode = function (mode) {

        document
            .getElementById(
                'inputDayWrapper'
            )
            ?.classList.add(
                'hidden'
            );


        document
            .getElementById(
                'inputMonthWrapper'
            )
            ?.classList.add(
                'hidden'
            );


        document
            .getElementById(
                'inputYearWrapper'
            )
            ?.classList.add(
                'hidden'
            );


        if (
            mode === 'day'
        ) {

            document
                .getElementById(
                    'inputDayWrapper'
                )
                ?.classList.remove(
                    'hidden'
                );

        }


        else if (
            mode === 'month'
        ) {

            document
                .getElementById(
                    'inputMonthWrapper'
                )
                ?.classList.remove(
                    'hidden'
                );

        }


        else if (
            mode === 'year'
        ) {

            document
                .getElementById(
                    'inputYearWrapper'
                )
                ?.classList.remove(
                    'hidden'
                );

        }

    };


    /*
    |--------------------------------------------------------------------------
    | CANCEL CONFIRMATION
    |--------------------------------------------------------------------------
    |
    | Menggunakan event delegation supaya tetap bekerja
    | meskipun isi tbody diganti oleh dynamic refresh.
    |
    */

    document.addEventListener(
        'submit',
        function (event) {

            const form =
                event.target.closest(
                    '.cancel-food-form'
                );


            if (!form) {
                return;
            }


            event.preventDefault();


            if (
                typeof Swal === 'undefined'
            ) {

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

                if (
                    result.isConfirmed
                ) {

                    form.submit();

                }

            });

        }
    );


    /*
    |--------------------------------------------------------------------------
    | MODAL DETAIL STATUS PESANAN
    |--------------------------------------------------------------------------
    */

    window.openOrderStatusModal = function (
        orderNumber,
        orderId
    ) {

        /*
        |--------------------------------------------------------------------------
        | CARI BARIS ORDER
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA UNIT
        |--------------------------------------------------------------------------
        */

        let units = [];


        try {

            units =
                JSON.parse(
                    row.dataset.orderUnits ||
                    '[]'
                );

        } catch (error) {

            console.error(
                'Gagal membaca data unit:',
                error
            );

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | ELEMENT MODAL
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | NOMOR ORDER
        |--------------------------------------------------------------------------
        */

        orderNumberElement.textContent =
            '#' + orderNumber;


        /*
        |--------------------------------------------------------------------------
        | RESET LIST
        |--------------------------------------------------------------------------
        */

        list.innerHTML = '';


        /*
        |--------------------------------------------------------------------------
        | COUNTER
        |--------------------------------------------------------------------------
        */

        let completed = 0;
        let cancelled = 0;
        let processing = 0;
        let pending = 0;


        /*
        |--------------------------------------------------------------------------
        | RENDER SETIAP UNIT
        |--------------------------------------------------------------------------
        */

        units.forEach(function (unit) {

            let statusText = '';
            let statusClass = '';
            let icon = '';


            /*
            |--------------------------------------------------------------------------
            | SELESAI
            |--------------------------------------------------------------------------
            */

            if (
                unit.status ===
                'completed'
            ) {

                completed++;

                statusText =
                    'Selesai';

                statusClass =
                    'bg-emerald-100 text-emerald-700';

                icon =
                    '<i class="fa-solid fa-check mr-1"></i>';

            }


            /*
            |--------------------------------------------------------------------------
            | BAHAN HABIS
            |--------------------------------------------------------------------------
            */

            else if (
                unit.status ===
                'cancelled'
            ) {

                cancelled++;

                statusText =
                    'Bahan Habis';

                statusClass =
                    'bg-rose-100 text-rose-700';

                icon =
                    '<i class="fa-solid fa-box-open mr-1"></i>';

            }


            /*
            |--------------------------------------------------------------------------
            | DIPROSES
            |--------------------------------------------------------------------------
            */

            else if (
                unit.status ===
                'processing'
            ) {

                processing++;

                statusText =
                    'Diproses';

                statusClass =
                    'bg-blue-100 text-blue-700';

                icon =
                    '<i class="fa-solid fa-fire mr-1"></i>';

            }


            /*
            |--------------------------------------------------------------------------
            | MENUNGGU
            |--------------------------------------------------------------------------
            */

            else {

                pending++;

                statusText =
                    'Menunggu';

                statusClass =
                    'bg-amber-100 text-amber-700';

                icon =
                    '<i class="fa-regular fa-clock mr-1"></i>';

            }


            /*
            |--------------------------------------------------------------------------
            | BUAT ELEMENT
            |--------------------------------------------------------------------------
            */

            const item =
                document.createElement(
                    'div'
                );


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


            list.appendChild(
                item
            );

        });


        /*
        |--------------------------------------------------------------------------
        | RINGKASAN
        |--------------------------------------------------------------------------
        */

        const summaryParts = [];


        if (
            completed > 0
        ) {

            summaryParts.push(
                `${completed} selesai`
            );

        }


        if (
            cancelled > 0
        ) {

            summaryParts.push(
                `${cancelled} bahan habis`
            );

        }


        if (
            processing > 0
        ) {

            summaryParts.push(
                `${processing} diproses`
            );

        }


        if (
            pending > 0
        ) {

            summaryParts.push(
                `${pending} menunggu`
            );

        }


        summary.textContent =
            summaryParts.join(
                ' • '
            );


        /*
        |--------------------------------------------------------------------------
        | TAMPILKAN MODAL
        |--------------------------------------------------------------------------
        */

        modal.classList.remove(
            'hidden'
        );

        modal.classList.add(
            'flex'
        );

    };


    /*
    |--------------------------------------------------------------------------
    | TUTUP MODAL DETAIL
    |--------------------------------------------------------------------------
    */

    window.closeOrderStatusModal =
        function () {

            const modal =
                document.getElementById(
                    'orderStatusModal'
                );


            if (!modal) {
                return;
            }


            modal.classList.add(
                'hidden'
            );

            modal.classList.remove(
                'flex'
            );

        };


    /*
    |--------------------------------------------------------------------------
    | ESCAPE HTML
    |--------------------------------------------------------------------------
    */

    window.escapeHtml = function (value) {

        const div =
            document.createElement(
                'div'
            );


        div.textContent =
            value ?? '';


        return div.innerHTML;

    };


    /*
    |--------------------------------------------------------------------------
    | KLIK DI LUAR MODAL DETAIL
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | TOMBOL ESC
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key ===
                'Escape'
            ) {

                closeOrderStatusModal();

            }

        }
    );


    // =========================================================
    // DYNAMIC ORDER
    // =========================================================
    // Digunakan untuk Kasir dan Dapur
    // agar pesanan diperbarui tanpa Ctrl + R.
    // =========================================================


    /*
    |--------------------------------------------------------------------------
    | ELEMENT UTAMA
    |--------------------------------------------------------------------------
    */

    const ordersBody =
        document.querySelector(
            '[data-orders-body]'
        );


    /*
    |--------------------------------------------------------------------------
    | JIKA BUKAN HALAMAN ORDER
    |--------------------------------------------------------------------------
    */

    if (!ordersBody) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | ROLE USER
    |--------------------------------------------------------------------------
    */

    const userRole =
        ordersBody.dataset.ordersRole;


    /*
    |--------------------------------------------------------------------------
    | HANYA KASIR & DAPUR % OWNER
    |--------------------------------------------------------------------------
    */

    if (
        userRole !== 'kasir' &&
        userRole !== 'dapur' && 
        userRole !== 'owner'
    ) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN ORDER YANG RELEVAN
    |--------------------------------------------------------------------------
    |
    | Kasir:
    | hanya order cash yang masih pending.
    |
    | Dapur:
    | seluruh order yang sedang tampil.
    |
    */

    let previousOrders =
        getCurrentRelevantOrderIds();


    /*
    |--------------------------------------------------------------------------
    | STATUS REQUEST
    |--------------------------------------------------------------------------
    |
    | Mencegah request bertumpuk jika server lambat.
    |
    */

    let isChecking =
        false;


    /*
    |--------------------------------------------------------------------------
    | AMBIL ID ORDER YANG RELEVAN DARI TABEL
    |--------------------------------------------------------------------------
    */

    function getCurrentRelevantOrderIds() {

        const rows =
            document.querySelectorAll(
                '[data-orders-body] tr[data-order-id]'
            );


        return Array.from(rows)
            .filter(function (row) {

                /*
                |--------------------------------------------------------------------------
                | OWNER
                |--------------------------------------------------------------------------
                |
                | Semua order yang tampil diperhatikan.
                |
                */

                if (
                    userRole === 'owner'
                ) {

                    return true;

                }


                /*
                |--------------------------------------------------------------------------
                | DAPUR
                |--------------------------------------------------------------------------
                */

                if (
                    userRole === 'dapur'
                ) {

                    return true;

                }


                /*
                |--------------------------------------------------------------------------
                | KASIR
                |--------------------------------------------------------------------------
                */

                const paymentMethod =
                    (
                        row.dataset
                            .orderPaymentMethod ||
                        ''
                    ).toLowerCase();


                const paymentStatus =
                    (
                        row.dataset
                            .orderPaymentStatus ||
                        ''
                    ).toLowerCase();


                const isCash =
                    [
                        'cash',
                        'kasir',
                        'tunai'
                    ].includes(
                        paymentMethod
                    );


                return (
                    isCash &&
                    paymentStatus ===
                    'pending'
                );

            })
            .map(function (row) {

                return String(
                    row.dataset.orderId
                );

            })
            .filter(Boolean)
            .sort();

    }


    /*
    |--------------------------------------------------------------------------
    | CEK APAKAH ADA PERUBAHAN ORDER
    |--------------------------------------------------------------------------
    */

    async function checkOrders() {

        if (isChecking) {
            return;
        }


        isChecking = true;


        try {

            const response =
                await fetch(
                    '/merchant/orders/check',
                    {
                        method: 'GET',

                        headers: {

                            'X-Requested-With':
                                'XMLHttpRequest',

                            'Accept':
                                'application/json'

                        },

                        cache: 'no-store'

                    }
                );


            /*
            |--------------------------------------------------------------------------
            | RESPONSE TIDAK VALID
            |--------------------------------------------------------------------------
            */

            if (!response.ok) {
                return;
            }


            const data =
                await response.json();


            if (
                !data.success ||
                !Array.isArray(
                    data.orders
                )
            ) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | ID ORDER TERBARU DARI SERVER
            |--------------------------------------------------------------------------
            */

            const serverOrders =
                data.orders
                    .map(function (order) {

                        /*
                        |--------------------------------------------------------------------------
                        | OWNER
                        |--------------------------------------------------------------------------
                        | Sertakan status pembayaran, status order,
                        | dan status setiap unit.
                        |--------------------------------------------------------------------------
                        */

                        if (
                            userRole === 'owner'
                        ) {

                            const units =
                                Array.isArray(
                                    order.units
                                )
                                    ? order.units
                                        .map(function (unit) {

                                            return (
                                                String(unit.id) +
                                                ':' +
                                                String(unit.status)
                                            );

                                        })
                                        .sort()
                                        .join('|')
                                    : '';


                            return (
                                String(order.id) +
                                ':' +
                                String(
                                    order.payment_status || ''
                                ) +
                                ':' +
                                String(
                                    order.status || ''
                                ) +
                                ':' +
                                units
                            );

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | KASIR + DAPUR
                        |--------------------------------------------------------------------------
                        */

                        return String(
                            order.id
                        );

                    })
                    .sort();


            /*
            |--------------------------------------------------------------------------
            | BANDINKAN
            |--------------------------------------------------------------------------
            */

            const hasChanged =
                !areArraysEqual(
                    previousOrders,
                    serverOrders
                );


            /*
            |--------------------------------------------------------------------------
            | JIKA ADA PERUBAHAN
            |--------------------------------------------------------------------------
            */

            if (hasChanged) {

                const refreshed =
                    await refreshOrderTable();


                /*
                |--------------------------------------------------------------------------
                | UPDATE BASELINE SETELAH REFRESH BERHASIL
                |--------------------------------------------------------------------------
                */

                if (refreshed) {

                    previousOrders =
                        getCurrentRelevantOrderIds();

                }

            }


        } catch (error) {

            console.error(
                'Dynamic order check error:',
                error
            );

        } finally {

            isChecking = false;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | BANDINGKAN ARRAY
    |--------------------------------------------------------------------------
    */

    function areArraysEqual(
        first,
        second
    ) {

        if (
            first.length !==
            second.length
        ) {

            return false;

        }


        for (
            let i = 0;
            i < first.length;
            i++
        ) {

            if (
                first[i] !==
                second[i]
            ) {

                return false;

            }

        }


        return true;

    }


    /*
    |--------------------------------------------------------------------------
    | REFRESH TABEL TANPA RELOAD HALAMAN
    |--------------------------------------------------------------------------
    */

    async function refreshOrderTable() {

        const currentBody =
            document.querySelector(
                '[data-orders-body]'
            );


        if (!currentBody) {
            return false;
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | AMBIL HALAMAN TERBARU
            |--------------------------------------------------------------------------
            */

            const response =
                await fetch(
                    window.location.href,
                    {
                        method: 'GET',

                        headers: {

                            'X-Requested-With':
                                'XMLHttpRequest',

                            'Accept':
                                'text/html'

                        },

                        cache: 'no-store'

                    }
                );


            if (!response.ok) {
                return false;
            }


            const html =
                await response.text();


            /*
            |--------------------------------------------------------------------------
            | PARSE HTML
            |--------------------------------------------------------------------------
            */

            const parser =
                new DOMParser();


            const documentHTML =
                parser.parseFromString(
                    html,
                    'text/html'
                );


            /*
            |--------------------------------------------------------------------------
            | AMBIL TBODY TERBARU
            |--------------------------------------------------------------------------
            */

            const newBody =
                documentHTML.querySelector(
                    '[data-orders-body]'
                );


            if (!newBody) {
                return false;
            }


            /*
            |--------------------------------------------------------------------------
            | GANTI ISI TBODY
            |--------------------------------------------------------------------------
            |
            | Hanya isi tabel yang diganti.
            | Modal dan elemen lain tidak disentuh.
            |
            */

            currentBody.innerHTML =
                newBody.innerHTML;


            return true;


        } catch (error) {

            console.error(
                'Dynamic order refresh error:',
                error
            );

            return false;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | POLLING
    |--------------------------------------------------------------------------
    |
    | Cek setiap 3 detik INI JADINYA 1 DETIK.
    |
    */

    setInterval(
        checkOrders,
        1000
    );


})();