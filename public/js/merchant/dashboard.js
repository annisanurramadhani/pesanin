document.addEventListener('DOMContentLoaded', function () {

    const statsContainer =
        document.getElementById('dashboard-stats');

    const ordersBody =
        document.getElementById('dashboard-orders-body');


    if (!statsContainer || !ordersBody) {

        console.warn(
            'Dashboard realtime element tidak ditemukan'
        );

        return;
    }


    console.log(
        'Dashboard realtime aktif'
    );


    let isRefreshing = false;



    async function refreshDashboard() {


        if (isRefreshing) {
            return;
        }


        if (document.hidden) {
            return;
        }


        isRefreshing = true;


        try {


            const url =
                new URL(
                    window.location.href
                );


            url.searchParams.set(
                '_dashboard_realtime',
                Date.now()
            );



            const response =
                await fetch(
                    url.toString(),
                    {

                        method: 'GET',

                        headers: {

                            'X-Requested-With':
                                'XMLHttpRequest',

                            'Accept':
                                'text/html'

                        },


                        cache:
                            'no-store'

                    }
                );



            console.log(
                'Realtime check:',
                response.status
            );



            if (!response.ok) {

                return;

            }



            const html =
                await response.text();



            const parser =
                new DOMParser();



            const documentParser =
                parser.parseFromString(
                    html,
                    'text/html'
                );



            const newStats =
                documentParser.getElementById(
                    'dashboard-stats'
                );


            const newOrdersBody =
                documentParser.getElementById(
                    'dashboard-orders-body'
                );



            if (
                !newStats ||
                !newOrdersBody
            ) {

                console.warn(
                    'Data dashboard baru tidak ditemukan'
                );

                return;

            }



            /*
            |--------------------------------------------------------------------------
            | UPDATE STAT CARD
            |--------------------------------------------------------------------------
            */


            if (
                statsContainer.innerHTML !==
                newStats.innerHTML
            ) {


                statsContainer.innerHTML =
                    newStats.innerHTML;


                console.log(
                    'Stat dashboard diperbarui'
                );

            }



            /*
            |--------------------------------------------------------------------------
            | UPDATE TABLE ORDER
            |--------------------------------------------------------------------------
            */


            if (
                ordersBody.innerHTML !==
                newOrdersBody.innerHTML
            ) {


                ordersBody.innerHTML =
                    newOrdersBody.innerHTML;


                console.log(
                    'Order dashboard diperbarui'
                );


            }



        }
        catch(error){


            console.error(
                'Dashboard realtime error:',
                error
            );


        }
        finally {


            isRefreshing = false;


        }

    }




    // cek pertama kali

    refreshDashboard();



    // realtime setiap 2 detik

    setInterval(
        refreshDashboard,
        2000
    );


});
