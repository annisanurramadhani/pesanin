document.addEventListener('DOMContentLoaded', () => {

    /*
    |--------------------------------------------------------------------------
    | ELEMENTS
    |--------------------------------------------------------------------------
    */

    const modal = document.getElementById('audit-detail-modal');
    const backdrop = document.getElementById('audit-modal-backdrop');
    const modalContent = document.getElementById('audit-modal-content');
    const closeButton = document.getElementById('audit-modal-close');
    const closeBottomButton = document.getElementById('audit-modal-close-bottom');

    const searchInput = document.getElementById('search');
    const tableBody = document.getElementById('audit-log-table-body');


    /*
    |--------------------------------------------------------------------------
    | AUDIT DETAIL MODAL
    |--------------------------------------------------------------------------
    */

    if (modal) {

        const actionElement = document.getElementById('audit-modal-action');
        const userElement = document.getElementById('audit-modal-user');
        const dateElement = document.getElementById('audit-modal-date');
        const modelElement = document.getElementById('audit-modal-model');
        const idElement = document.getElementById('audit-modal-id');
        const ipElement = document.getElementById('audit-modal-ip');
        const agentElement = document.getElementById('audit-modal-agent');
        const oldValuesElement = document.getElementById('audit-modal-old');
        const newValuesElement = document.getElementById('audit-modal-new');


        /*
        |--------------------------------------------------------------------------
        | FORMAT VALUE
        |--------------------------------------------------------------------------
        */

        const formatValue = (value) => {

            if (value === null || value === undefined) {
                return '-';
            }

            if (typeof value === 'object') {
                return JSON.stringify(value, null, 2);
            }

            return String(value);
        };


        /*
        |--------------------------------------------------------------------------
        | SET MODAL VALUE
        |--------------------------------------------------------------------------
        */

        const setValues = (element, value) => {

            if (!element) {
                return;
            }

            if (
                value === null ||
                value === undefined ||
                value === '' ||
                (
                    typeof value === 'object' &&
                    Object.keys(value).length === 0
                )
            ) {
                element.textContent = '-';
                return;
            }

            element.textContent = formatValue(value);
        };


        /*
        |--------------------------------------------------------------------------
        | OPEN MODAL
        |--------------------------------------------------------------------------
        */

        const openModal = () => {

            modal.classList.remove('hidden');

            requestAnimationFrame(() => {

                if (backdrop) {
                    backdrop.classList.remove('opacity-0');
                }

                if (modalContent) {

                    modalContent.classList.remove(
                        'opacity-0',
                        'scale-95'
                    );

                    modalContent.classList.add(
                        'opacity-100',
                        'scale-100'
                    );
                }

            });

            document.body.classList.add('overflow-hidden');
        };


        /*
        |--------------------------------------------------------------------------
        | CLOSE MODAL
        |--------------------------------------------------------------------------
        */

        const closeModal = () => {

            if (backdrop) {
                backdrop.classList.add('opacity-0');
            }

            if (modalContent) {

                modalContent.classList.remove(
                    'opacity-100',
                    'scale-100'
                );

                modalContent.classList.add(
                    'opacity-0',
                    'scale-95'
                );
            }

            setTimeout(() => {

                modal.classList.add('hidden');

                document.body.classList.remove(
                    'overflow-hidden'
                );

            }, 200);
        };


        /*
        |--------------------------------------------------------------------------
        | LOAD AUDIT DETAIL
        |--------------------------------------------------------------------------
        */

        const loadAuditDetail = (button) => {

            const data = button.dataset;

            let oldValues = {};
            let newValues = {};


            /*
            |--------------------------------------------------------------------------
            | OLD VALUES
            |--------------------------------------------------------------------------
            */

            try {

                oldValues = data.old
                    ? JSON.parse(data.old)
                    : {};

            } catch (error) {

                console.error(
                    'Gagal membaca old_values audit log:',
                    error
                );

            }


            /*
            |--------------------------------------------------------------------------
            | NEW VALUES
            |--------------------------------------------------------------------------
            */

            try {

                newValues = data.new
                    ? JSON.parse(data.new)
                    : {};

            } catch (error) {

                console.error(
                    'Gagal membaca new_values audit log:',
                    error
                );

            }


            /*
            |--------------------------------------------------------------------------
            | MODAL DATA
            |--------------------------------------------------------------------------
            */

            if (actionElement) {
                actionElement.textContent =
                    data.action || '-';
            }

            if (userElement) {
                userElement.textContent =
                    data.user || '-';
            }

            if (dateElement) {
                dateElement.textContent =
                    data.date || '-';
            }

            if (modelElement) {
                modelElement.textContent =
                    data.model || '-';
            }

            if (idElement) {
                idElement.textContent =
                    data.modelId || '-';
            }

            if (ipElement) {
                ipElement.textContent =
                    data.ip || '-';
            }

            if (agentElement) {
                agentElement.textContent =
                    data.agent || '-';
            }


            /*
            |--------------------------------------------------------------------------
            | OLD & NEW VALUES
            |--------------------------------------------------------------------------
            */

            setValues(
                oldValuesElement,
                oldValues
            );

            setValues(
                newValuesElement,
                newValues
            );


            /*
            |--------------------------------------------------------------------------
            | SHOW MODAL
            |--------------------------------------------------------------------------
            */

            openModal();
        };


        /*
        |--------------------------------------------------------------------------
        | DETAIL BUTTON
        |--------------------------------------------------------------------------
        |
        | Event delegation digunakan karena tabel akan diganti
        | oleh live search.
        |
        */

        document.addEventListener('click', (event) => {

            const button =
                event.target.closest('.audit-detail-btn');

            if (!button) {
                return;
            }

            loadAuditDetail(button);
        });


        /*
        |--------------------------------------------------------------------------
        | CLOSE MODAL
        |--------------------------------------------------------------------------
        */

        if (closeButton) {
            closeButton.addEventListener(
                'click',
                closeModal
            );
        }

        if (closeBottomButton) {

            closeBottomButton.addEventListener(
                'click',
                closeModal
            );
        }

        if (backdrop) {

            backdrop.addEventListener(
                'click',
                closeModal
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ESCAPE
        |--------------------------------------------------------------------------
        */

        document.addEventListener('keydown', (event) => {

            if (
                event.key === 'Escape' &&
                !modal.classList.contains('hidden')
            ) {
                closeModal();
            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | LIVE SEARCH
    |--------------------------------------------------------------------------
    */

    if (searchInput && tableBody) {

        let searchTimeout = null;
        let controller = null;


        searchInput.addEventListener(
            'input',
            function () {

                clearTimeout(searchTimeout);

                const searchValue =
                    this.value.trim();


                searchTimeout = setTimeout(
                    async () => {

                        /*
                        |--------------------------------------------------------------------------
                        | Cancel previous request
                        |--------------------------------------------------------------------------
                        */

                        if (controller) {
                            controller.abort();
                        }

                        controller =
                            new AbortController();


                        /*
                        |--------------------------------------------------------------------------
                        | Current URL
                        |--------------------------------------------------------------------------
                        */

                        const url =
                            new URL(
                                window.location.href
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | Search parameter
                        |--------------------------------------------------------------------------
                        */

                        if (searchValue !== '') {

                            url.searchParams.set(
                                'search',
                                searchValue
                            );

                        } else {

                            url.searchParams.delete(
                                'search'
                            );
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Reset pagination
                        |--------------------------------------------------------------------------
                        */

                        url.searchParams.delete(
                            'page'
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | Loading
                        |--------------------------------------------------------------------------
                        */

                        tableBody.style.opacity = '0.5';

                        tableBody.style.pointerEvents =
                            'none';


                        try {

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

                                        cache: 'no-store',

                                        signal:
                                            controller.signal
                                    }
                                );


                            if (!response.ok) {

                                throw new Error(
                                    'HTTP ' +
                                    response.status
                                );
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Get HTML
                            |--------------------------------------------------------------------------
                            */

                            const html =
                                await response.text();


                            /*
                            |--------------------------------------------------------------------------
                            | Parse HTML
                            |--------------------------------------------------------------------------
                            */

                            const parser =
                                new DOMParser();

                            const documentResponse =
                                parser.parseFromString(
                                    html,
                                    'text/html'
                                );


                            /*
                            |--------------------------------------------------------------------------
                            | Get new table body
                            |--------------------------------------------------------------------------
                            */

                            const newTableBody =
                                documentResponse.getElementById(
                                    'audit-log-table-body'
                                );


                            if (!newTableBody) {

                                throw new Error(
                                    'Tabel hasil pencarian tidak ditemukan.'
                                );
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Replace table body
                            |--------------------------------------------------------------------------
                            */

                            tableBody.innerHTML =
                                newTableBody.innerHTML;


                            /*
                            |--------------------------------------------------------------------------
                            | Update browser URL
                            |--------------------------------------------------------------------------
                            */

                            window.history.replaceState(
                                {},
                                '',
                                url.toString()
                            );


                        } catch (error) {

                            if (
                                error.name !==
                                'AbortError'
                            ) {

                                console.error(
                                    'Live search audit log error:',
                                    error
                                );
                            }

                        } finally {

                            tableBody.style.opacity =
                                '1';

                            tableBody.style.pointerEvents =
                                'auto';
                        }

                    },
                    300
                );

            }
        );

    }

});