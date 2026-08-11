document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('merchantForm');

    if (!form) {
        return;
    }

    const nameInput = document.getElementById('name');
    const phoneInput = document.getElementById('phone');
    const addressInput = document.getElementById('address');
    const subscriptionInput = document.getElementById('subscription_expires_at');

    form.addEventListener('submit', function (event) {

        event.preventDefault();

        const name = nameInput.value.trim();
        const phone = phoneInput.value.trim();
        const address = addressInput.value.trim();
        const subscription = subscriptionInput.value.trim();

        if (!name) {
            Swal.fire({
                icon: 'error',
                title: 'Nama Merchant',
                text: 'Nama merchant wajib diisi.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#111827'
            });

            nameInput.focus();
            return;
        }

        if (!phone) {
            Swal.fire({
                icon: 'error',
                title: 'Nomor Telepon',
                text: 'Nomor telepon wajib diisi.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#111827'
            });

            phoneInput.focus();
            return;
        }

        if (phone.length < 10) {
            Swal.fire({
                icon: 'error',
                title: 'Nomor Telepon',
                text: 'Nomor telepon harus terdiri dari minimal 10 angka.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#111827'
            });

            phoneInput.focus();
            return;
        }

        if (!address) {
            Swal.fire({
                icon: 'error',
                title: 'Alamat Merchant',
                text: 'Alamat merchant wajib diisi.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#111827'
            });

            addressInput.focus();
            return;
        }

        if (!subscription) {
            Swal.fire({
                icon: 'error',
                title: 'Masa Langganan',
                text: 'Masa langganan wajib dipilih.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#111827'
            });

            subscriptionInput.focus();
            return;
        }

        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');

        const todayString = `${year}-${month}-${day}`;

        if (subscription < todayString) {
            Swal.fire({
                icon: 'error',
                title: 'Masa Langganan',
                text: 'Masa langganan tidak boleh sebelum hari ini.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#111827'
            });

            subscriptionInput.focus();
            return;
        }

        form.submit();
    });

});
