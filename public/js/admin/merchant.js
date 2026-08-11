document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('merchantForm');

    if (!form) {
        return;
    }

    const nameInput = document.getElementById('name');
    const phoneInput = document.getElementById('phone');
    const addressInput = document.getElementById('address');
    const subscriptionInput = document.getElementById('subscription_expires_at');

    const dangerousPattern = /<\s*(script|\/script|iframe|\/iframe|object|\/object|embed|\/embed|svg|\/svg|style|\/style|link|meta|form|\/form|input|textarea|select|button|php|%php)|<\?php|<\?=|\{\{|\}\}|\{!!|!!\}|javascript\s*:|vbscript\s*:|data\s*:\s*text\/html|on[a-z]+\s*=|expression\s*\(|url\s*\(\s*javascript\s*:|@php\b|@endphp\b|@if\b|@endif\b|@foreach\b|@endforeach\b/i;

    function containsDangerousInput(value) {
        if (!value) {
            return false;
        }

        const textarea = document.createElement('textarea');
        textarea.innerHTML = value;

        const decodedValue = textarea.value;

        return dangerousPattern.test(value) || dangerousPattern.test(decodedValue);
    }

    function showError(title, text, input = null) {
        Swal.fire({
            icon: 'error',
            title: title,
            text: text,
            confirmButtonText: 'OK',
            confirmButtonColor: '#111827'
        }).then(() => {
            if (input) {
                input.focus();
            }
        });
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        const name = nameInput.value.trim();
        const phone = phoneInput.value.trim();
        const address = addressInput.value.trim();
        const subscription = subscriptionInput.value.trim();

        if (containsDangerousInput(name)) {
            showError(
                'Input Tidak Valid',
                'Nama merchant mengandung kode atau karakter yang tidak diizinkan.',
                nameInput
            );
            return;
        }

        if (containsDangerousInput(phone)) {
            showError(
                'Input Tidak Valid',
                'Nomor telepon mengandung kode atau karakter yang tidak diizinkan.',
                phoneInput
            );
            return;
        }

        if (containsDangerousInput(address)) {
            showError(
                'Input Tidak Valid',
                'Alamat merchant mengandung kode atau karakter yang tidak diizinkan.',
                addressInput
            );
            return;
        }

        if (containsDangerousInput(subscription)) {
            showError(
                'Input Tidak Valid',
                'Masa langganan mengandung kode atau karakter yang tidak diizinkan.',
                subscriptionInput
            );
            return;
        }

        if (!name) {
            showError(
                'Nama Merchant',
                'Nama merchant wajib diisi.',
                nameInput
            );
            return;
        }

        if (name.length > 255) {
            showError(
                'Nama Merchant',
                'Nama merchant maksimal 255 karakter.',
                nameInput
            );
            return;
        }

        if (!phone) {
            showError(
                'Nomor Telepon',
                'Nomor telepon wajib diisi.',
                phoneInput
            );
            return;
        }

        if (!/^[0-9]+$/.test(phone)) {
            showError(
                'Nomor Telepon',
                'Nomor telepon hanya boleh berisi angka.',
                phoneInput
            );
            return;
        }

        if (phone.length < 10 || phone.length > 20) {
            showError(
                'Nomor Telepon',
                'Nomor telepon harus terdiri dari 10 sampai 20 angka.',
                phoneInput
            );
            return;
        }

        if (!address) {
            showError(
                'Alamat Merchant',
                'Alamat merchant wajib diisi.',
                addressInput
            );
            return;
        }

        if (address.length > 1000) {
            showError(
                'Alamat Merchant',
                'Alamat merchant maksimal 1000 karakter.',
                addressInput
            );
            return;
        }

        if (!subscription) {
            showError(
                'Masa Langganan',
                'Masa langganan wajib dipilih.',
                subscriptionInput
            );
            return;
        }

        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');

        const todayString = `${year}-${month}-${day}`;

        if (subscription < todayString) {
            showError(
                'Masa Langganan',
                'Masa langganan tidak boleh sebelum hari ini.',
                subscriptionInput
            );
            return;
        }

        form.submit();
    });
});
