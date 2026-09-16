document.addEventListener('DOMContentLoaded', function () {

    const container =
        document.getElementById('order-detail-container');

    if (!container) {
        return;
    }


    const statusUrl =
        container.dataset.statusUrl;


    if (!statusUrl) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | STATUS CONFIG
    |--------------------------------------------------------------------------
    */

    const statusConfig = {

        pending: {
            text: 'Sedang di Masak',
            icon: 'fa-fire',

            wrapper:
                'bg-amber-50 border-amber-100',

            iconWrapper:
                'bg-amber-100',

            color:
                'text-amber-700',

            iconColor:
                'text-amber-600',
        },

        processing: {
            text: 'Sedang di Masak',
            icon: 'fa-fire',

            wrapper:
                'bg-amber-50 border-amber-100',

            iconWrapper:
                'bg-amber-100',

            color:
                'text-amber-700',

            iconColor:
                'text-amber-600',
        },

        completed: {
            text: 'Selesai',
            icon: 'fa-check',

            wrapper:
                'bg-emerald-50 border-emerald-100',

            iconWrapper:
                'bg-emerald-100',

            color:
                'text-emerald-700',

            iconColor:
                'text-emerald-600',
        },

        cancelled: {
            text: 'Cancel (Bahan Habis)',
            icon: 'fa-box-open',

            wrapper:
                'bg-red-50 border-red-100',

            iconWrapper:
                'bg-red-100',

            color:
                'text-red-700',

            iconColor:
                'text-red-600',
        },

    };


    /*
    |--------------------------------------------------------------------------
    | UPDATE UNIT
    |--------------------------------------------------------------------------
    */

    function updateUnit(unit) {

        const element =
            document.querySelector(
                `[data-unit-id="${unit.id}"]`
            );


        if (!element) {
            return;
        }


        const config =
            statusConfig[unit.status]
            ?? statusConfig.pending;


        /*
        | Simpan status terbaru
        */

        element.dataset.status =
            unit.status;


        /*
        | Wrapper
        */

        element.className =
            `order-unit-status
             flex items-center
             justify-between gap-3
             rounded-lg
             border px-3 py-2
             ${config.wrapper}`;


        /*
        | Icon wrapper
        */

        const iconWrapper =
            element.querySelector(
                '.unit-status-icon'
            );


        if (iconWrapper) {

            iconWrapper.className =
                `unit-status-icon
                 flex h-7 w-7
                 items-center
                 justify-center
                 rounded-full
                 ${config.iconWrapper}`;
        }


        /*
        | Icon
        */

        const icon =
            element.querySelector(
                '.unit-status-icon i'
            );


        if (icon) {

            icon.className =
                `fa-solid
                 ${config.icon}
                 text-xs
                 ${config.iconColor}`;
        }


        /*
        | Menu number
        */

        const menuNumber =
            element.querySelector(
                '.unit-status-icon'
            )?.parentElement
            ?.querySelector(
                'span:last-child'
            );


        if (menuNumber) {

            menuNumber.className =
                `text-xs
                 font-semibold
                 ${config.color}`;
        }


        /*
        | Status text
        */

        const statusText =
            element.querySelector(
                '.unit-status-text'
            );


        if (statusText) {

            statusText.className =
                `unit-status-text
                 text-xs font-bold
                 text-right
                 ${config.color}`;

            statusText.textContent =
                config.text;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE OVERALL STATUS
    |--------------------------------------------------------------------------
    */

    function updateOverallStatus(items) {

        const statusElement =
            document.getElementById(
                'order-overall-status'
            );


        if (!statusElement) {
            return;
        }


        const statuses = [];


        items.forEach(item => {

            item.units.forEach(unit => {

                statuses.push(
                    unit.status
                );

            });

        });


        if (!statuses.length) {
            return;
        }


        let text =
            'Sedang di Masak';

        let wrapper =
            'bg-amber-50 text-amber-700';

        let dot =
            'bg-amber-500';


        /*
        |--------------------------------------------------------------------------
        | SEMUA SELESAI
        |--------------------------------------------------------------------------
        */

        if (
            statuses.every(
                status =>
                    status === 'completed'
            )
        ) {

            text =
                'Selesai';

            wrapper =
                'bg-emerald-50 text-emerald-700';

            dot =
                'bg-emerald-500';
        }


        /*
        |--------------------------------------------------------------------------
        | SEMUA BAHAN HABIS
        |--------------------------------------------------------------------------
        */

        else if (
            statuses.every(
                status =>
                    status === 'cancelled'
            )
        ) {

            text =
                'Cancel (Bahan Habis)';

            wrapper =
                'bg-red-50 text-red-700';

            dot =
                'bg-red-500';
        }


        /*
        |--------------------------------------------------------------------------
        | CAMPURAN
        |--------------------------------------------------------------------------
        */

        else if (
            statuses.some(
                status =>
                    status === 'completed'
            )
            &&
            statuses.some(
                status =>
                    status === 'cancelled'
            )
        ) {

            text =
                'Pesanan Sebagian Selesai';

            wrapper =
                'bg-amber-50 text-amber-700';

            dot =
                'bg-amber-500';
        }


        statusElement.className =
            `inline-flex items-center
             gap-1.5 px-3 py-1.5
             rounded-full
             text-xs font-bold
             ${wrapper}`;


        statusElement.innerHTML = `
            <span
                class="w-1.5 h-1.5
                       rounded-full
                       ${dot}">
            </span>

            ${text}
        `;
    }


    /*
    |--------------------------------------------------------------------------
    | FETCH STATUS
    |--------------------------------------------------------------------------
    */

    async function checkStatus() {

        try {

            const response =
                await fetch(
                    statusUrl,
                    {
                        method: 'GET',

                        headers: {
                            'Accept':
                                'application/json',

                            'X-Requested-With':
                                'XMLHttpRequest',
                        },

                        cache: 'no-store',
                    }
                );


            if (!response.ok) {
                return;
            }


            const data =
                await response.json();


            if (
                !data.success
            ) {
                return;
            }


            /*
            | Update setiap unit
            */

            data.items.forEach(item => {

                item.units.forEach(unit => {

                    updateUnit(unit);

                });

            });


            /*
            | Update status keseluruhan
            */

            updateOverallStatus(
                data.items
            );

        } catch (error) {

            /*
            |--------------------------------------------------------------------------
            | Jangan ganggu customer kalau polling gagal.
            |--------------------------------------------------------------------------
            */

            console.warn(
                'Gagal memperbarui status pesanan:',
                error
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CEK PERTAMA
    |--------------------------------------------------------------------------
    */

    checkStatus();


    /*
    |--------------------------------------------------------------------------
    | POLLING
    |--------------------------------------------------------------------------
    |
    | Setiap 2 detik.
    |--------------------------------------------------------------------------
    */

    const polling =
        setInterval(
            checkStatus,
            2000
        );


    /*
    |--------------------------------------------------------------------------
    | STOP POLLING SAAT HALAMAN DITINGGALKAN
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        'beforeunload',
        function () {

            clearInterval(
                polling
            );

        }
    );

});