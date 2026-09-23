document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | ELEMENT
    |--------------------------------------------------------------------------
    */

    const tableBody =
        document.getElementById('withdrawalTableBody');

    const rejectModal =
        document.getElementById('rejectWithdrawalModal');

    const rejectBackdrop =
        document.getElementById('rejectWithdrawalBackdrop');

    const closeRejectModalButton =
        document.getElementById('closeRejectWithdrawalModal');

    const cancelRejectButton =
        document.getElementById('cancelRejectWithdrawal');

    const rejectForm =
        document.getElementById('rejectWithdrawalForm');

    const rejectNote =
        document.getElementById('rejectWithdrawalNote');

    const rejectCounter =
        document.getElementById('rejectWithdrawalCounter');

    const rejectMerchant =
        document.getElementById('rejectWithdrawalMerchant');

    const rejectNumber =
        document.getElementById('rejectWithdrawalNumber');

    const rejectAmount =
        document.getElementById('rejectWithdrawalAmount');

    const submitRejectButton =
        document.getElementById('submitRejectWithdrawal');


    /*
    |--------------------------------------------------------------------------
    | VALIDASI ELEMENT
    |--------------------------------------------------------------------------
    */

    if (!tableBody) {
        console.warn(
            '[Withdrawal] Table body tidak ditemukan.'
        );

        return;
    }


    const realtimeUrl =
        tableBody.dataset.realtimeUrl;

    const approveUrlTemplate =
        tableBody.dataset.approveUrlTemplate;

    const rejectUrlTemplate =
        tableBody.dataset.rejectUrlTemplate;


    if (!realtimeUrl) {
        console.warn(
            '[Withdrawal] Realtime URL tidak ditemukan.'
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
            Number(value) || 0;

        return new Intl.NumberFormat('id-ID')
            .format(number);

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
    | BUILD URL
    |--------------------------------------------------------------------------
    */

    function buildUrl(template, id) {

        if (!template) {
            return '#';
        }

        return template.replace(
            '__ID__',
            encodeURIComponent(id)
        );

    }


    /*
    |--------------------------------------------------------------------------
    | STATUS BADGE
    |--------------------------------------------------------------------------
    */

    function getStatusBadge(status) {

        const badges = {

            pending: `
                <span
                    class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1.5 text-xs font-black text-amber-700"
                >
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                    Menunggu
                </span>
            `,

            processing: `
                <span
                    class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1.5 text-xs font-black text-blue-700"
                >
                    <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                    Diproses
                </span>
            `,

            paid: `
                <span
                    class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-black text-emerald-700"
                >
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    Berhasil
                </span>
            `,

            failed: `
                <span
                    class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3 py-1.5 text-xs font-black text-red-700"
                >
                    <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                    Gagal
                </span>
            `,

            rejected: `
                <span
                    class="inline-flex items-center gap-1.5 rounded-full bg-rose-100 px-3 py-1.5 text-xs font-black text-rose-700"
                >
                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                    Ditolak
                </span>
            `

        };


        return badges[status] || `
            <span class="text-xs font-bold text-slate-400">
                -
            </span>
        `;

    }


    /*
    |--------------------------------------------------------------------------
    | PAYOUT HTML
    |--------------------------------------------------------------------------
    */

    function buildPayoutHtml(withdrawal) {

        let html = `
            <span>
                ${escapeHtml(
                    withdrawal.payout_status ?? '-'
                )}
            </span>
        `;


        if (withdrawal.payout_id) {

            html += `
                <p class="mt-1 break-all text-[10px] text-slate-400">
                    ${escapeHtml(
                        withdrawal.payout_id
                    )}
                </p>
            `;

        }


        if (
            withdrawal.status === 'failed' &&
            withdrawal.payout_response?.message
        ) {

            html += `
                <p class="mt-1 text-red-600">
                    ${escapeHtml(
                        withdrawal.payout_response.message
                    )}
                </p>
            `;

        }


        if (withdrawal.note) {

            html += `
                <p class="mt-1 text-rose-600">
                    ${escapeHtml(
                        withdrawal.note
                    )}
                </p>
            `;

        }


        return html;

    }


    /*
    |--------------------------------------------------------------------------
    | BANK HTML
    |--------------------------------------------------------------------------
    */

    function buildBankHtml(withdrawal) {

        const bankAccount =
            withdrawal.bank_account;


        if (!bankAccount) {

            return `
                <span class="text-slate-400">
                    Rekening tidak ditemukan
                </span>
            `;

        }


        return `
            <p class="font-bold text-slate-700">
                ${escapeHtml(
                    bankAccount.bank_name
                )}
            </p>

            <p class="mt-1 text-xs text-slate-500">
                ${escapeHtml(
                    bankAccount.account_number
                )}
            </p>

            <p class="text-xs text-slate-500">
                a.n ${escapeHtml(
                    bankAccount.account_name
                )}
            </p>
        `;

    }


    /*
    |--------------------------------------------------------------------------
    | ACTION HTML
    |--------------------------------------------------------------------------
    */

    function getActionHtml(withdrawal) {

        /*
        |--------------------------------------------------------------------------
        | PENDING
        |--------------------------------------------------------------------------
        */

        if (withdrawal.status === 'pending') {

            const approveUrl =
                buildUrl(
                    approveUrlTemplate,
                    withdrawal.id
                );


            return `
                <div
                    class="flex w-full min-w-[220px] items-center justify-center gap-2"
                >

                    <!-- SETUJUI -->
                    <form
                        method="POST"
                        action="${approveUrl}"
                        class="flex-1"
                    >

                        <input
                            type="hidden"
                            name="_token"
                            value="${escapeHtml(
                                window.withdrawalCsrfToken || ''
                            )}"
                        >

                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-emerald-500 px-4 py-2.5 text-xs font-black text-white shadow-sm transition hover:bg-emerald-600 active:scale-[0.98]"
                        >
                            <i class="fa-solid fa-check text-[11px]"></i>
                            Setujui
                        </button>

                    </form>


                    <!-- TOLAK -->
                    <button
                        type="button"
                        class="withdrawal-reject-trigger flex-1 inline-flex items-center justify-center gap-1.5 rounded-lg bg-rose-500 px-4 py-2.5 text-xs font-black text-white shadow-sm transition hover:bg-rose-600 active:scale-[0.98]"
                        data-withdrawal-id="${escapeHtml(
                            withdrawal.id
                        )}"
                        data-merchant-name="${escapeHtml(
                            withdrawal.merchant?.name ?? '-'
                        )}"
                        data-amount="${escapeHtml(
                            formatRupiah(
                                withdrawal.amount
                            )
                        )}"
                    >
                        <i class="fa-solid fa-xmark text-[11px]"></i>
                        Tolak
                    </button>

                </div>
            `;

        }


        /*
        |--------------------------------------------------------------------------
        | PAID
        |--------------------------------------------------------------------------
        */

        if (withdrawal.status === 'paid') {

            return `
                <div class="flex flex-col items-center gap-1">

                    <span
                        class="inline-flex items-center gap-1.5 text-xs font-black text-emerald-600"
                    >
                        <i class="fa-solid fa-circle-check"></i>
                        Selesai
                    </span>

                    ${
                        withdrawal.payout_id
                            ? `
                                <p class="max-w-[220px] break-all text-[10px] text-slate-400">
                                    ${escapeHtml(
                                        withdrawal.payout_id
                                    )}
                                </p>
                            `
                            : ''
                    }

                </div>
            `;

        }


        /*
        |--------------------------------------------------------------------------
        | PROCESSING
        |--------------------------------------------------------------------------
        */

        if (withdrawal.status === 'processing') {

            return `
                <span
                    class="inline-flex items-center gap-1.5 text-xs font-black text-blue-600"
                >
                    <i class="fa-solid fa-spinner fa-spin"></i>
                    Menunggu payout
                </span>
            `;

        }


        /*
        |--------------------------------------------------------------------------
        | REJECTED
        |--------------------------------------------------------------------------
        */

        if (withdrawal.status === 'rejected') {

            return `
                <span
                    class="inline-flex items-center gap-1.5 text-xs font-black text-rose-600"
                >
                    <i class="fa-solid fa-circle-xmark"></i>
                    Ditolak
                </span>
            `;

        }


        /*
        |--------------------------------------------------------------------------
        | FAILED
        |--------------------------------------------------------------------------
        */

        if (withdrawal.status === 'failed') {

            return `
                <span
                    class="inline-flex items-center gap-1.5 text-xs font-black text-red-600"
                >
                    <i class="fa-solid fa-circle-exclamation"></i>
                    Gagal
                </span>
            `;

        }


        return `
            <span class="text-xs font-black text-slate-400">
                Selesai
            </span>
        `;

    }


    /*
    |--------------------------------------------------------------------------
    | BUILD NEW ROW
    |--------------------------------------------------------------------------
    */

    function buildWithdrawalRow(withdrawal) {

        const merchantName =
            withdrawal.merchant?.name ?? '-';


        return `
            <tr
                data-withdrawal-id="${escapeHtml(
                    withdrawal.id
                )}"
                data-status="${escapeHtml(
                    withdrawal.status
                )}"
                class="withdrawal-row border-b border-slate-100 transition-colors hover:bg-slate-50"
            >

                <!-- MERCHANT -->
                <td class="withdrawal-merchant px-6 py-5">

                    <p class="font-black text-slate-800">
                        ${escapeHtml(
                            merchantName
                        )}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        #WD-${escapeHtml(
                            withdrawal.id
                        )}
                    </p>

                </td>


                <!-- NOMINAL -->
                <td class="withdrawal-amount px-6 py-5">

                    <span class="font-black text-slate-800">
                        Rp ${formatRupiah(
                            withdrawal.amount
                        )}
                    </span>

                </td>


                <!-- REKENING -->
                <td class="withdrawal-bank px-6 py-5">

                    ${buildBankHtml(
                        withdrawal
                    )}

                </td>


                <!-- STATUS -->
                <td class="withdrawal-status px-6 py-5 text-center">

                    ${getStatusBadge(
                        withdrawal.status
                    )}

                </td>


                <!-- PAYOUT -->
                <td class="withdrawal-payout px-6 py-5 text-xs text-slate-500">

                    ${buildPayoutHtml(
                        withdrawal
                    )}

                </td>


                <!-- AKSI -->
                <td class="withdrawal-action px-6 py-5 text-center">

                    ${getActionHtml(
                        withdrawal
                    )}

                </td>

            </tr>
        `;

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE EXISTING ROW
    |--------------------------------------------------------------------------
    */

    function updateWithdrawalRow(withdrawal) {

        const row =
            tableBody.querySelector(
                `[data-withdrawal-id="${CSS.escape(
                    String(withdrawal.id)
                )}"]`
            );


        /*
        |--------------------------------------------------------------------------
        | ROW BELUM ADA
        |--------------------------------------------------------------------------
        */

        if (!row) {

            const emptyRow =
                tableBody.querySelector(
                    '.withdrawal-empty-row'
                );


            if (emptyRow) {
                emptyRow.remove();
            }


            tableBody.insertAdjacentHTML(
                'afterbegin',
                buildWithdrawalRow(
                    withdrawal
                )
            );


            return;

        }


        /*
        |--------------------------------------------------------------------------
        | STATUS SEBELUMNYA
        |--------------------------------------------------------------------------
        */

        const previousStatus =
            row.dataset.status;


        /*
        |--------------------------------------------------------------------------
        | UPDATE MERCHANT
        |--------------------------------------------------------------------------
        */

        const merchantCell =
            row.querySelector(
                '.withdrawal-merchant'
            );


        if (merchantCell) {

            const merchantName =
                withdrawal.merchant?.name ?? '-';

            const newMerchantHtml = `
                <p class="font-black text-slate-800">
                    ${escapeHtml(
                        merchantName
                    )}
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    #WD-${escapeHtml(
                        withdrawal.id
                    )}
                </p>
            `;


            if (
                merchantCell.innerHTML.trim() !==
                newMerchantHtml.trim()
            ) {

                merchantCell.innerHTML =
                    newMerchantHtml;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE NOMINAL
        |--------------------------------------------------------------------------
        */

        const amountCell =
            row.querySelector(
                '.withdrawal-amount'
            );


        if (amountCell) {

            const newAmountHtml = `
                <span class="font-black text-slate-800">
                    Rp ${formatRupiah(
                        withdrawal.amount
                    )}
                </span>
            `;


            if (
                amountCell.innerHTML.trim() !==
                newAmountHtml.trim()
            ) {

                amountCell.innerHTML =
                    newAmountHtml;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE REKENING
        |--------------------------------------------------------------------------
        */

        const bankCell =
            row.querySelector(
                '.withdrawal-bank'
            );


        if (bankCell) {

            const newBankHtml =
                buildBankHtml(
                    withdrawal
                );


            if (
                bankCell.innerHTML.trim() !==
                newBankHtml.trim()
            ) {

                bankCell.innerHTML =
                    newBankHtml;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE STATUS
        |--------------------------------------------------------------------------
        */

        const statusCell =
            row.querySelector(
                '.withdrawal-status'
            );


        if (statusCell) {

            const newStatusHtml =
                getStatusBadge(
                    withdrawal.status
                );


            if (
                statusCell.innerHTML.trim() !==
                newStatusHtml.trim()
            ) {

                statusCell.innerHTML =
                    newStatusHtml;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE PAYOUT
        |--------------------------------------------------------------------------
        */

        const payoutCell =
            row.querySelector(
                '.withdrawal-payout'
            );


        if (payoutCell) {

            const newPayoutHtml =
                buildPayoutHtml(
                    withdrawal
                );


            if (
                payoutCell.innerHTML.trim() !==
                newPayoutHtml.trim()
            ) {

                payoutCell.innerHTML =
                    newPayoutHtml;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE ACTION
        |--------------------------------------------------------------------------
        |
        | Hanya ketika status berubah.
        |
        | Dengan begitu modal / input yang sedang digunakan
        | tidak terganggu oleh polling 5 detik.
        |
        */

        if (
            previousStatus !==
            withdrawal.status
        ) {

            const actionCell =
                row.querySelector(
                    '.withdrawal-action'
                );


            if (actionCell) {

                actionCell.innerHTML =
                    getActionHtml(
                        withdrawal
                    );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE DATASET
        |--------------------------------------------------------------------------
        */

        row.dataset.status =
            withdrawal.status;

    }


    /*
    |--------------------------------------------------------------------------
    | SYNC WITHDRAWALS
    |--------------------------------------------------------------------------
    */

    function syncWithdrawals(withdrawals) {

        if (!Array.isArray(withdrawals)) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | TIDAK ADA DATA
        |--------------------------------------------------------------------------
        */

        if (withdrawals.length === 0) {

            const existingRows =
                tableBody.querySelectorAll(
                    '.withdrawal-row'
                );


            if (
                existingRows.length === 0 &&
                !tableBody.querySelector(
                    '.withdrawal-empty-row'
                )
            ) {

                tableBody.innerHTML = `
                    <tr class="withdrawal-empty-row">

                        <td
                            colspan="6"
                            class="py-12 text-center text-slate-400"
                        >

                            <div class="flex flex-col items-center justify-center gap-2">

                                <i class="fa-solid fa-money-bill-transfer text-2xl text-slate-300"></i>

                                <span class="text-sm">
                                    Belum ada permintaan penarikan
                                </span>

                            </div>

                        </td>

                    </tr>
                `;

            }


            return;

        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE DATA
        |--------------------------------------------------------------------------
        */

        withdrawals.forEach(
            function (withdrawal) {

                updateWithdrawalRow(
                    withdrawal
                );

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | FETCH REALTIME
    |--------------------------------------------------------------------------
    */

    async function fetchWithdrawals() {

        if (isFetching) {
            return;
        }


        isFetching = true;


        try {

            const url =
                `${realtimeUrl}?_=${Date.now()}`;


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

                throw new Error(
                    'Response realtime bukan JSON.'
                );

            }


            const result =
                await response.json();


            syncWithdrawals(
                result.data || []
            );


        } catch (error) {

            console.error(
                '[Withdrawal] Realtime error:',
                error
            );

        } finally {

            isFetching = false;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | MODAL
    |--------------------------------------------------------------------------
    */

    function openRejectModal(
        withdrawalId,
        merchantName,
        amount
    ) {

        if (!rejectModal) {
            return;
        }


        rejectMerchant.textContent =
            merchantName || '-';


        rejectNumber.textContent =
            `#WD-${withdrawalId}`;


        rejectAmount.textContent =
            `Rp ${amount}`;


        rejectForm.action =
            buildUrl(
                rejectUrlTemplate,
                withdrawalId
            );


        rejectNote.value =
            '';


        updateRejectCounter();


        rejectModal.classList.remove(
            'hidden'
        );


        rejectModal.setAttribute(
            'aria-hidden',
            'false'
        );


        document.body.classList.add(
            'overflow-hidden'
        );


        setTimeout(
            function () {

                rejectNote.focus();

            },
            100
        );

    }


    /*
    |--------------------------------------------------------------------------
    | CLOSE MODAL
    |--------------------------------------------------------------------------
    */

    function closeRejectModal() {

        if (!rejectModal) {
            return;
        }


        rejectModal.classList.add(
            'hidden'
        );


        rejectModal.setAttribute(
            'aria-hidden',
            'true'
        );


        document.body.classList.remove(
            'overflow-hidden'
        );


        rejectNote.value =
            '';


        updateRejectCounter();

    }


    /*
    |--------------------------------------------------------------------------
    | CHARACTER COUNTER
    |--------------------------------------------------------------------------
    */

    function updateRejectCounter() {

        if (
            !rejectNote ||
            !rejectCounter
        ) {
            return;
        }


        const length =
            rejectNote.value.length;


        rejectCounter.textContent =
            `${length}/1000 karakter`;


        if (length >= 900) {

            rejectCounter.classList.remove(
                'text-slate-400'
            );

            rejectCounter.classList.add(
                'text-rose-500'
            );

        } else {

            rejectCounter.classList.remove(
                'text-rose-500'
            );

            rejectCounter.classList.add(
                'text-slate-400'
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | EVENT: TOMBOL TOLAK
    |--------------------------------------------------------------------------
    |
    | Menggunakan event delegation karena tombol dapat
    | dibuat ulang ketika ada withdrawal baru.
    |
    */

    tableBody.addEventListener(
        'click',
        function (event) {

            const rejectButton =
                event.target.closest(
                    '.withdrawal-reject-trigger'
                );


            if (!rejectButton) {
                return;
            }


            const withdrawalId =
                rejectButton.dataset.withdrawalId;


            const merchantName =
                rejectButton.dataset.merchantName;


            const amount =
                rejectButton.dataset.amount;


            openRejectModal(
                withdrawalId,
                merchantName,
                amount
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | CLOSE BUTTON
    |--------------------------------------------------------------------------
    */

    if (closeRejectModalButton) {

        closeRejectModalButton.addEventListener(
            'click',
            closeRejectModal
        );

    }


    /*
    |--------------------------------------------------------------------------
    | CANCEL BUTTON
    |--------------------------------------------------------------------------
    */

    if (cancelRejectButton) {

        cancelRejectButton.addEventListener(
            'click',
            closeRejectModal
        );

    }


    /*
    |--------------------------------------------------------------------------
    | BACKDROP
    |--------------------------------------------------------------------------
    */

    if (rejectBackdrop) {

        rejectBackdrop.addEventListener(
            'click',
            closeRejectModal
        );

    }


    /*
    |--------------------------------------------------------------------------
    | ESCAPE
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Escape' &&
                rejectModal &&
                !rejectModal.classList.contains(
                    'hidden'
                )
            ) {

                closeRejectModal();

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | CHARACTER COUNTER EVENT
    |--------------------------------------------------------------------------
    */

    if (rejectNote) {

        rejectNote.addEventListener(
            'input',
            updateRejectCounter
        );

    }


    /*
    |--------------------------------------------------------------------------
    | FORM SUBMIT
    |--------------------------------------------------------------------------
    */

    if (rejectForm) {

        rejectForm.addEventListener(
            'submit',
            function (event) {

                const note =
                    rejectNote.value.trim();


                if (!note) {

                    event.preventDefault();

                    rejectNote.focus();

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | Disable button agar tidak double submit.
                |--------------------------------------------------------------------------
                */

                if (submitRejectButton) {

                    submitRejectButton.disabled =
                        true;

                    submitRejectButton.classList.add(
                        'cursor-not-allowed',
                        'opacity-70'
                    );

                    submitRejectButton.innerHTML = `
                        <i class="fa-solid fa-spinner fa-spin text-[11px]"></i>
                        Memproses...
                    `;

                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | INITIAL FETCH
    |--------------------------------------------------------------------------
    */

    fetchWithdrawals();


    /*
    |--------------------------------------------------------------------------
    | REALTIME POLLING
    |--------------------------------------------------------------------------
    |
    | Cek data withdrawal setiap 5 detik.
    |
    */

    setInterval(
        fetchWithdrawals,
        5000
    );

});
