document.addEventListener(
    'DOMContentLoaded',
    function () {


        /*
        |--------------------------------------------------------------------------
        | COPY VA
        |--------------------------------------------------------------------------
        */


        window.copyVA = function () {


            const va =
                document
                    .getElementById('va-number')
                    .innerText;



            navigator.clipboard.writeText(va);



            Swal.fire({

                toast: true,

                position: 'top-end',

                icon: 'success',

                title: 'Nomor VA berhasil disalin.',

                showConfirmButton: false,

                timer: 2000,

                timerProgressBar: true

            });


        };




        /*
 |--------------------------------------------------------------------------
 | AUTO CHECK PAYMENT STATUS
 |--------------------------------------------------------------------------
 */

        const paymentStatus =
            document.getElementById('payment-status');

        if (paymentStatus) {

            const url = paymentStatus.dataset.url;

            let checkingPayment = false;
            let paymentCompleted = false;


            const checkPaymentStatus = async () => {

                // Jangan cek lagi kalau pembayaran sudah berhasil
                if (checkingPayment || paymentCompleted) {
                    return;
                }

                checkingPayment = true;

                try {

                    // Cache busting agar selalu mengambil status terbaru
                    const response = await fetch(
                        url + '?_=' + Date.now(),
                        {
                            method: 'GET',
                            cache: 'no-store',
                            headers: {
                                'Accept': 'application/json',
                                'Cache-Control': 'no-cache'
                            }
                        }
                    );


                    if (!response.ok) {
                        throw new Error(
                            'HTTP ' + response.status
                        );
                    }


                    const data =
                        await response.json();


                    console.log(
                        'Payment Status:',
                        data
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | PEMBAYARAN BERHASIL
                    |--------------------------------------------------------------------------
                    */

                    if (
                        data.paid === true ||
                        data.status === 'settlement'
                    ) {

                        paymentCompleted = true;


                        Swal.fire({

                            icon: 'success',

                            title: 'Pembayaran Berhasil',

                            text:
                                'Subscription berhasil diaktifkan.',

                            showConfirmButton: false,

                            timer: 2000,

                            allowOutsideClick: false,

                            allowEscapeKey: false

                        }).then(() => {

                            if (data.redirect) {

                                window.location.href =
                                    data.redirect;

                            } else {

                                window.location.href =
                                    '/dashboard';

                            }

                        });

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | EXPIRED
                    |--------------------------------------------------------------------------
                    */

                    else if (
                        data.status === 'expire'
                    ) {

                        paymentCompleted = true;

                        Swal.fire({

                            icon: 'warning',

                            title: 'Pembayaran Kedaluwarsa',

                            text:
                                'Waktu pembayaran telah berakhir.',

                            confirmButtonText: 'Pilih Paket Lagi',

                            allowOutsideClick: false

                        }).then(() => {

                            window.location.href =
                                '/subscription';

                        });

                    }


                } catch (error) {

                    console.error(
                        'Payment status check error:',
                        error
                    );

                } finally {

                    checkingPayment = false;

                }

            };


            /*
            |--------------------------------------------------------------------------
            | CEK LANGSUNG
            |--------------------------------------------------------------------------
            */

            checkPaymentStatus();


            /*
            |--------------------------------------------------------------------------
            | CEK SETIAP 3 DETIK
            |--------------------------------------------------------------------------
            */

            const paymentInterval =
                setInterval(() => {

                    if (paymentCompleted) {

                        clearInterval(paymentInterval);

                        return;

                    }

                    checkPaymentStatus();

                }, 3000);

        }




        /*
        |--------------------------------------------------------------------------
        | COUNTDOWN
        |--------------------------------------------------------------------------
        */


        const countdownElement =
            document.getElementById(
                'countdown'
            );



        if (countdownElement) {


            const expiredTime =
                new Date(
                    countdownElement.dataset.expired
                ).getTime();



            const countdown =
                setInterval(function () {


                    const now =
                        new Date().getTime();



                    const distance =
                        expiredTime - now;



                    if (distance <= 0) {


                        clearInterval(countdown);


                        countdownElement.innerHTML =
                            "00:00:00";


                        return;

                    }




                    const hours =
                        Math.floor(
                            distance /
                            (1000 * 60 * 60)
                        );



                    const minutes =
                        Math.floor(
                            (distance %
                                (1000 * 60 * 60))
                            /
                            (1000 * 60)
                        );



                    const seconds =
                        Math.floor(
                            (distance %
                                (1000 * 60))
                            /
                            1000
                        );



                    countdownElement.innerHTML =

                        String(hours)
                            .padStart(2, '0')
                        + ":" +

                        String(minutes)
                            .padStart(2, '0')
                        + ":" +

                        String(seconds)
                            .padStart(2, '0');



                }, 1000);



        }



    }
);
