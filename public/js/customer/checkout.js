document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | PAYMENT METHOD
    |--------------------------------------------------------------------------
    */

    const paymentMethods = document.querySelectorAll(
        'input[name="payment_method"]'
    );

    const bankSelection = document.getElementById('bank-selection');
    const bankSelect = document.getElementById('bank');

    function toggleBankSelection() {
        const selected = document.querySelector(
            'input[name="payment_method"]:checked'
        );

        if (
            selected &&
            selected.value === 'bank'
        ) {
            bankSelection.classList.remove('hidden');
            bankSelect.required = true;
        } else {
            bankSelection.classList.add('hidden');
            bankSelect.required = false;
            bankSelect.value = '';
        }
    }

    paymentMethods.forEach(function (radio) {
        radio.addEventListener(
            'change',
            toggleBankSelection
        );
    });

    toggleBankSelection();


    /*
    |--------------------------------------------------------------------------
    | CUSTOMER MEMORY
    |--------------------------------------------------------------------------
    */

    const customerTokenInput =
        document.getElementById('customer_token');

    const customerNameInput =
        document.getElementById('customer_name');

    const customerPhoneInput =
        document.getElementById('customer_phone');

    const customerEmailInput =
        document.getElementById('customer_email');

    const CUSTOMER_TOKEN_KEY =
        'pesanin_customer_token';

    /*
    |--------------------------------------------------------------------------
    | AMBIL TOKEN DARI LOCAL STORAGE
    |--------------------------------------------------------------------------
    */

    const customerToken =
        localStorage.getItem(CUSTOMER_TOKEN_KEY);

    if (
        customerToken &&
        customerTokenInput
    ) {
        customerTokenInput.value =
            customerToken;
    }


    /*
    |--------------------------------------------------------------------------
    | LOAD CUSTOMER PROFILE
    |--------------------------------------------------------------------------
    */

    if (
        customerToken &&
        customerNameInput &&
        customerPhoneInput &&
        customerEmailInput
    ) {
        loadCustomerProfile(customerToken);
    }


    /*
    |--------------------------------------------------------------------------
    | FUNCTION LOAD CUSTOMER
    |--------------------------------------------------------------------------
    */

    async function loadCustomerProfile(token) {

        try {

            /*
            |--------------------------------------------------------------------------
            | AMBIL URL ROUTE CUSTOMER PROFILE
            |--------------------------------------------------------------------------
            */

            const profileUrl =
                window.location.pathname.replace(
                    '/checkout',
                    '/customer/profile'
                );

            const response = await fetch(
                `${profileUrl}?customer_token=${encodeURIComponent(token)}`,
                {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }
            );


            /*
            |--------------------------------------------------------------------------
            | CUSTOMER TIDAK DITEMUKAN
            |--------------------------------------------------------------------------
            */

            if (!response.ok) {

                if (response.status === 404) {
                    localStorage.removeItem(
                        CUSTOMER_TOKEN_KEY
                    );

                    if (customerTokenInput) {
                        customerTokenInput.value = '';
                    }
                }

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | PARSE RESPONSE
            |--------------------------------------------------------------------------
            */

            const data =
                await response.json();


            if (
                !data.success ||
                !data.customer
            ) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | ISI DATA CUSTOMER
            |--------------------------------------------------------------------------
            */

            customerNameInput.value =
                data.customer.name ?? '';

            customerPhoneInput.value =
                data.customer.phone ?? '';

            customerEmailInput.value =
                data.customer.email ?? '';


        } catch (error) {

            /*
            |--------------------------------------------------------------------------
            | ERROR
            |--------------------------------------------------------------------------
            |
            | Jangan mengganggu proses checkout apabila
            | customer profile gagal diambil.
            |
            */

            console.error(
                'Gagal mengambil data customer:',
                error
            );
        }
    }

});