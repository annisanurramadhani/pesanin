/*
|--------------------------------------------------------------------------
| Merchant Finance JS
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| BANK ACCOUNT MODAL
|--------------------------------------------------------------------------
*/

function openBankModal() {

    const modal =
        document.getElementById('bankModal');

    if (modal) {
        modal.classList.remove('hidden');
    }

}


function closeBankModal() {

    const modal =
        document.getElementById('bankModal');

    if (modal) {
        modal.classList.add('hidden');
    }

}


/*
|--------------------------------------------------------------------------
| WITHDRAW MODAL
|--------------------------------------------------------------------------
*/

function openWithdrawModal() {

    const modal =
        document.getElementById('withdrawModal');

    if (modal) {
        modal.classList.remove('hidden');
    }

}


function closeWithdrawModal() {

    const modal =
        document.getElementById('withdrawModal');

    if (modal) {
        modal.classList.add('hidden');
    }

}


/*
|--------------------------------------------------------------------------
| CLOSE CLICK OUTSIDE MODAL
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const bankModal =
            document.getElementById('bankModal');

        const withdrawModal =
            document.getElementById('withdrawModal');


        if (bankModal) {

            bankModal.addEventListener(
                'click',
                function (event) {

                    if (
                        event.target === bankModal
                    ) {
                        closeBankModal();
                    }

                }
            );

        }


        if (withdrawModal) {

            withdrawModal.addEventListener(
                'click',
                function (event) {

                    if (
                        event.target === withdrawModal
                    ) {
                        closeWithdrawModal();
                    }

                }
            );

        }

    }
);


/*
|--------------------------------------------------------------------------
| CLOSE WITH ESC
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'keydown',
    function (event) {

        if (event.key === 'Escape') {

            closeBankModal();
            closeWithdrawModal();

        }

    }
);


/*
|--------------------------------------------------------------------------
| MERCHANT FINANCE REALTIME
|--------------------------------------------------------------------------
|
| Fungsi:
| - Update status withdrawal tanpa reload
| - Update riwayat withdrawal
| - Update saldo merchant
| - Update total pendapatan
| - Update total penarikan
|
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    function () {

        /*
        |--------------------------------------------------------------------------
        | CONFIG
        |--------------------------------------------------------------------------
        */

        if (
            typeof window.financeConfig ===
            'undefined'
        ) {

            console.warn(
                '[Merchant Finance] financeConfig tidak ditemukan.'
            );

            return;
        }


        const config =
            window.financeConfig;


        const realtimeUrl =
            config.withdrawalRealtimeUrl;


        if (!realtimeUrl) {

            console.warn(
                '[Merchant Finance] URL realtime tidak ditemukan.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | ELEMENT
        |--------------------------------------------------------------------------
        */

        const withdrawalHistoryBody =
            document.getElementById(
                'withdrawalHistoryBody'
            );


        const withdrawalRealtimeStatus =
            document.getElementById(
                'withdrawalRealtimeStatus'
            );


        const walletBalance =
            document.getElementById(
                'walletBalance'
            );


        const totalIncome =
            document.getElementById(
                'totalIncome'
            );


        const totalWithdraw =
            document.getElementById(
                'totalWithdraw'
            );


        if (!withdrawalHistoryBody) {

            console.warn(
                '[Merchant Finance] withdrawalHistoryBody tidak ditemukan.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | STATE
        |--------------------------------------------------------------------------
        */

        let isFetching = false;


        /*
        |--------------------------------------------------------------------------
        | FORMAT RUPIAH
        |--------------------------------------------------------------------------
        */

        function formatRupiah(value) {

            const number =
                Number(value || 0);

            return new Intl.NumberFormat(
                'id-ID'
            ).format(number);

        }


        /*
        |--------------------------------------------------------------------------
        | FORMAT DATE
        |--------------------------------------------------------------------------
        */

        function formatDate(value) {

            if (!value) {
                return '-';
            }


            const date =
                new Date(value);


            if (
                Number.isNaN(
                    date.getTime()
                )
            ) {

                return '-';
            }


            return (
                date.toLocaleDateString(
                    'id-ID',
                    {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    }
                )
                +
                ' '
                +
                date.toLocaleTimeString(
                    'id-ID',
                    {
                        hour: '2-digit',
                        minute: '2-digit'
                    }
                )
            );

        }


        /*
        |--------------------------------------------------------------------------
        | ESCAPE HTML
        |--------------------------------------------------------------------------
        */

        function escapeHtml(value) {

            if (
                value === null ||
                value === undefined
            ) {
                return '';
            }


            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

        }


        /*
        |--------------------------------------------------------------------------
        | STATUS BADGE
        |--------------------------------------------------------------------------
        */

        function getStatusBadge(status) {

            const normalizedStatus =
                String(
                    status || ''
                ).toLowerCase();


            switch (
                normalizedStatus
            ) {

                case 'pending':

                    return `
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1.5 text-xs font-bold text-amber-700"
                        >
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                            Menunggu
                        </span>
                    `;


                case 'processing':

                    return `
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1.5 text-xs font-bold text-blue-700"
                        >
                            <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                            Diproses
                        </span>
                    `;


                case 'paid':

                    return `
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-700"
                        >
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                            Berhasil
                        </span>
                    `;


                case 'rejected':

                    return `
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full bg-rose-100 px-3 py-1.5 text-xs font-bold text-rose-700"
                        >
                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                            Ditolak
                        </span>
                    `;


                case 'failed':

                    return `
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3 py-1.5 text-xs font-bold text-red-700"
                        >
                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                            Gagal
                        </span>
                    `;


                default:

                    return `
                        <span
                            class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-600"
                        >
                            ${escapeHtml(
                                status || '-'
                            )}
                        </span>
                    `;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | STATUS TEXT
        |--------------------------------------------------------------------------
        */

        function getStatusText(status) {

            const normalizedStatus =
                String(
                    status || ''
                ).toLowerCase();


            switch (
                normalizedStatus
            ) {

                case 'pending':
                    return 'Pengajuan penarikan sedang menunggu verifikasi admin.';


                case 'processing':
                    return 'Penarikan sedang diproses.';


                case 'paid':
                    return 'Penarikan berhasil diproses.';


                case 'rejected':
                    return 'Pengajuan penarikan ditolak.';


                case 'failed':
                    return 'Penarikan gagal diproses.';


                default:
                    return 'Pengajuan penarikan sedang diproses.';

            }

        }


        /*
        |--------------------------------------------------------------------------
        | REALTIME INDICATOR
        |--------------------------------------------------------------------------
        */

        function setRealtimeStatus(
            online
        ) {

            if (
                !withdrawalRealtimeStatus
            ) {
                return;
            }


            if (online) {

                withdrawalRealtimeStatus.innerHTML = `
                    <span class="inline-flex items-center gap-2 text-xs font-semibold text-emerald-600">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Realtime aktif
                    </span>
                `;

            } else {

                withdrawalRealtimeStatus.innerHTML = `
                    <span class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400">
                        <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                        Menghubungkan...
                    </span>
                `;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE SUMMARY
        |--------------------------------------------------------------------------
        */

        function updateSummary(
            summary
        ) {

            if (!summary) {
                return;
            }


            if (
                walletBalance &&
                typeof summary.balance !==
                    'undefined'
            ) {

                walletBalance.textContent =
                    'Rp ' +
                    formatRupiah(
                        summary.balance
                    );

            }


            if (
                totalIncome &&
                typeof summary.total_income !==
                    'undefined'
            ) {

                totalIncome.textContent =
                    'Rp ' +
                    formatRupiah(
                        summary.total_income
                    );

            }


            if (
                totalWithdraw &&
                typeof summary.total_withdraw !==
                    'undefined'
            ) {

                totalWithdraw.textContent =
                    'Rp ' +
                    formatRupiah(
                        summary.total_withdraw
                    );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | RENDER WITHDRAWAL ROW
        |--------------------------------------------------------------------------
        */

        function buildWithdrawalRow(
            withdrawal
        ) {

            const status =
                String(
                    withdrawal.status || ''
                ).toLowerCase();


            const note =
                withdrawal.note ||
                withdrawal.payout_status ||
                getStatusText(status);


            return `
                <tr
                    class="withdrawal-history-row border-b border-slate-100"
                    data-withdrawal-id="${escapeHtml(
                        withdrawal.id
                    )}"
                    data-status="${escapeHtml(
                        status
                    )}"
                >

                    <td class="p-4">
                        ${formatDate(
                            withdrawal.created_at
                        )}
                    </td>


                    <td class="p-4 text-right font-black">
                        Rp ${formatRupiah(
                            withdrawal.amount
                        )}
                    </td>


                    <td class="p-4 text-center">
                        ${getStatusBadge(
                            status
                        )}
                    </td>


                    <td class="p-4 text-slate-500">
                        ${escapeHtml(
                            note
                        )}
                    </td>

                </tr>
            `;

        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE TABLE
        |--------------------------------------------------------------------------
        */

        function renderWithdrawals(
            withdrawals
        ) {

            if (
                !Array.isArray(
                    withdrawals
                )
            ) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | EMPTY
            |--------------------------------------------------------------------------
            */

            if (
                withdrawals.length === 0
            ) {

                withdrawalHistoryBody.innerHTML = `
                    <tr id="withdrawalEmptyRow">

                        <td
                            colspan="4"
                            class="p-8 text-center text-slate-400"
                        >

                            <div class="flex flex-col items-center gap-2">

                                <i
                                    class="fa-solid fa-money-bill-transfer text-2xl text-slate-300"
                                ></i>

                                <span>
                                    Belum ada pengajuan penarikan.
                                </span>

                            </div>

                        </td>

                    </tr>
                `;

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | BUILD ROW
            |--------------------------------------------------------------------------
            */

            let html = '';


            withdrawals.forEach(
                function (withdrawal) {

                    html +=
                        buildWithdrawalRow(
                            withdrawal
                        );

                }
            );


            withdrawalHistoryBody.innerHTML =
                html;

        }


        /*
        |--------------------------------------------------------------------------
        | FETCH REALTIME
        |--------------------------------------------------------------------------
        */

        async function fetchWithdrawalRealtime() {

            if (isFetching) {
                return;
            }


            isFetching = true;


            try {

                const separator =
                    realtimeUrl.includes('?')
                        ? '&'
                        : '?';


                const url =
                    realtimeUrl +
                    separator +
                    '_=' +
                    Date.now();


                const response =
                    await fetch(
                        url,
                        {
                            method: 'GET',

                            headers: {
                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest'
                            },

                            credentials:
                                'same-origin',

                            cache:
                                'no-store'
                        }
                    );


                console.log(
                    '[Merchant Finance] HTTP:',
                    response.status
                );


                if (!response.ok) {

                    throw new Error(
                        `HTTP ${response.status}`
                    );

                }


                const contentType =
                    response.headers.get(
                        'content-type'
                    ) || '';


                if (
                    !contentType.includes(
                        'application/json'
                    )
                ) {

                    const text =
                        await response.text();


                    console.error(
                        '[Merchant Finance] Response bukan JSON:',
                        text.substring(
                            0,
                            500
                        )
                    );


                    throw new Error(
                        'Response realtime bukan JSON.'
                    );

                }


                const result =
                    await response.json();


                console.log(
                    '[Merchant Finance] Realtime data:',
                    result
                );


                renderWithdrawals(
                    result.data || []
                );


                updateSummary(
                    result.summary || null
                );


                /*
                |--------------------------------------------------------------------------
                | Request berhasil
                |--------------------------------------------------------------------------
                */

                setRealtimeStatus(
                    true
                );


            } catch (error) {

                console.error(
                    '[Merchant Finance] Realtime error:',
                    error
                );


                setRealtimeStatus(
                    false
                );

            } finally {

                isFetching = false;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | INITIAL LOAD
        |--------------------------------------------------------------------------
        */

        fetchWithdrawalRealtime();


        /*
        |--------------------------------------------------------------------------
        | POLLING
        |--------------------------------------------------------------------------
        |
        | Cek status setiap 5 detik.
        |
        */

        setInterval(
            fetchWithdrawalRealtime,
            5000
        );


        /*
        |--------------------------------------------------------------------------
        | PREVENT DOUBLE SUBMIT
        |--------------------------------------------------------------------------
        */

        const withdrawalForm =
            document.getElementById(
                'withdrawForm'
            );


        if (withdrawalForm) {

            withdrawalForm.addEventListener(
                'submit',
                function () {

                    const submitButton =
                        document.getElementById(
                            'submitWithdrawButton'
                        );


                    if (!submitButton) {
                        return;
                    }


                    submitButton.disabled =
                        true;


                    submitButton.classList.add(
                        'opacity-60',
                        'cursor-not-allowed'
                    );


                    submitButton.innerHTML = `
                        <span class="inline-flex items-center gap-2">

                            <svg
                                class="animate-spin h-4 w-4"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >

                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>

                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                                ></path>

                            </svg>

                            Mengajukan...

                        </span>
                    `;

                }
            );

        }

    }
);
