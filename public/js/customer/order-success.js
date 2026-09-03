document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | PAYMENT CONFIG
    |--------------------------------------------------------------------------
    */

    const paymentContainer =
        document.getElementById('payment-container');

    if (!paymentContainer) {
        return;
    }


    const paymentStatus =
        paymentContainer.dataset.paymentStatus || '';

    const paymentMethod =
        paymentContainer.dataset.paymentMethod || '';

    const paymentExpiresAt =
        paymentContainer.dataset.paymentExpiresAt || '';

    const paymentUrl =
        paymentContainer.dataset.paymentUrl || '';


    /*
    |--------------------------------------------------------------------------
    | COPY VIRTUAL ACCOUNT
    |--------------------------------------------------------------------------
    */

    window.copyVA = function () {

        const vaElement =
            document.getElementById('va-number');

        if (!vaElement) {
            return;
        }

        const va =
            vaElement.innerText.trim();

        if (!va) {
            return;
        }


        if (
            navigator.clipboard &&
            window.isSecureContext
        ) {

            navigator.clipboard
                .writeText(va)
                .then(function () {

                    alert(
                        'Nomor Virtual Account berhasil disalin.'
                    );

                })
                .catch(function () {

                    fallbackCopyVA(va);

                });

        } else {

            fallbackCopyVA(va);

        }
    };


    function fallbackCopyVA(va) {

        const textarea =
            document.createElement('textarea');

        textarea.value = va;

        textarea.style.position =
            'fixed';

        textarea.style.opacity =
            '0';

        document.body.appendChild(
            textarea
        );

        textarea.select();

        try {

            document.execCommand('copy');

            alert(
                'Nomor Virtual Account berhasil disalin.'
            );

        } catch (error) {

            alert(
                'Gagal menyalin nomor Virtual Account.'
            );

        }

        document.body.removeChild(
            textarea
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PAYMENT COUNTDOWN
    |--------------------------------------------------------------------------
    */

    const timerContainer =
        document.getElementById(
            'payment-countdown-container'
        );

    const timerElement =
        document.getElementById(
            'payment-countdown'
        );


    if (
        timerContainer &&
        timerElement &&
        paymentExpiresAt &&
        paymentStatus === 'pending'
    ) {

        startCountdown(
            paymentExpiresAt,
            timerContainer,
            timerElement
        );
    }


    function startCountdown(
        expiryTime,
        container,
        element
    ) {

        const expiry =
            new Date(expiryTime).getTime();


        if (
            Number.isNaN(expiry)
        ) {
            console.warn(
                'payment_expires_at tidak valid:',
                expiryTime
            );

            return;
        }


        function updateCountdown() {

            const now =
                new Date().getTime();

            const distance =
                expiry - now;


            /*
            |--------------------------------------------------------------------------
            | EXPIRED
            |--------------------------------------------------------------------------
            */

            if (distance <= 0) {

                element.innerText =
                    '00:00';

                container.classList.remove(
                    'bg-amber-50',
                    'border-amber-100'
                );

                container.classList.add(
                    'bg-red-50',
                    'border-red-100'
                );


                const title =
                    document.getElementById(
                        'payment-countdown-title'
                    );

                const description =
                    document.getElementById(
                        'payment-countdown-description'
                    );


                if (title) {

                    title.innerText =
                        'Pembayaran Kedaluwarsa';

                }


                if (description) {

                    description.innerText =
                        'Waktu pembayaran telah habis. Silakan buat pesanan baru.';

                }


                clearInterval(
                    countdownInterval
                );


                /*
                |--------------------------------------------------------------------------
                | REFRESH PAGE
                |--------------------------------------------------------------------------
                |
                | Server akan mengubah payment_status
                | menjadi expired melalui endpoint payment.
                |
                */

                setTimeout(
                    function () {

                        window.location.reload();

                    },
                    1000
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | CALCULATE TIME
            |--------------------------------------------------------------------------
            */

            const totalSeconds =
                Math.floor(
                    distance / 1000
                );


            const hours =
                Math.floor(
                    totalSeconds / 3600
                );


            const minutes =
                Math.floor(
                    (totalSeconds % 3600) / 60
                );


            const seconds =
                totalSeconds % 60;


            let timeText;


            if (hours > 0) {

                timeText =
                    String(hours)
                        .padStart(2, '0')
                    + ':' +
                    String(minutes)
                        .padStart(2, '0')
                    + ':' +
                    String(seconds)
                        .padStart(2, '0');

            } else {

                timeText =
                    String(minutes)
                        .padStart(2, '0')
                    + ':' +
                    String(seconds)
                        .padStart(2, '0');
            }


            element.innerText =
                timeText;
        }


        updateCountdown();


        const countdownInterval =
            setInterval(
                updateCountdown,
                1000
            );
    }


    /*
    |--------------------------------------------------------------------------
    | AUTO CHECK PAYMENT STATUS
    |--------------------------------------------------------------------------
    */

    if (
        !paymentUrl ||
        paymentStatus !== 'pending' ||
        (
            paymentMethod !== 'qris' &&
            paymentMethod !== 'bank'
        )
    ) {
        return;
    }


    let checkingPayment = false;


    async function checkPaymentStatus() {

        if (checkingPayment) {
            return;
        }


        checkingPayment = true;


        try {

            const response =
                await fetch(
                    paymentUrl,
                    {
                        method: 'GET',

                        headers: {
                            'X-Requested-With':
                                'XMLHttpRequest',

                            'Accept':
                                'application/json'
                        },

                        cache:
                            'no-store'
                    }
                );


            if (!response.ok) {

                throw new Error(
                    'HTTP error ' +
                    response.status
                );
            }


            const data =
                await response.json();


            /*
            |--------------------------------------------------------------------------
            | PAYMENT PAID
            |--------------------------------------------------------------------------
            */

            if (
                data.success &&
                data.payment_status === 'paid'
            ) {

                clearInterval(
                    paymentStatusInterval
                );

                window.location.reload();

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | PAYMENT EXPIRED
            |--------------------------------------------------------------------------
            */

            if (
                data.success &&
                data.payment_status === 'expired'
            ) {

                clearInterval(
                    paymentStatusInterval
                );

                window.location.reload();

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | PAYMENT FAILED
            |--------------------------------------------------------------------------
            */

            if (
                data.success &&
                data.payment_status === 'failed'
            ) {

                clearInterval(
                    paymentStatusInterval
                );

                window.location.reload();

                return;
            }


        } catch (error) {

            console.error(
                'Gagal mengecek status pembayaran:',
                error
            );

        } finally {

            checkingPayment = false;

        }
    }


    /*
    |--------------------------------------------------------------------------
    | CHECK EVERY 5 SECONDS
    |--------------------------------------------------------------------------
    */

    const paymentStatusInterval =
        setInterval(
            checkPaymentStatus,
            1000
        );


    /*
    |--------------------------------------------------------------------------
    | CHECK IMMEDIATELY
    |--------------------------------------------------------------------------
    */

    checkPaymentStatus();

});
