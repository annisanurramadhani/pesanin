document.addEventListener('DOMContentLoaded', function () {

    // =========================================================
    // DELETE PACKAGE
    // =========================================================

    const deleteForms = document.querySelectorAll('.delete-package-form');

    deleteForms.forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            Swal.fire({
                icon: 'warning',
                title: 'Hapus Paket?',
                text: 'Paket yang dihapus tidak dapat dikembalikan.',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#111827',
                background: '#ffffff',
                color: '#111827',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl px-5 py-2.5 font-bold',
                    cancelButton: 'rounded-xl px-5 py-2.5 font-bold'
                }
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });


    // =========================================================
    // CREATE / EDIT PACKAGE
    // =========================================================

    const forms = document.querySelectorAll('.package-form');

    if (!forms.length) {
        return;
    }


    // =========================================================
    // SECURE TEXT
    // =========================================================

    const dangerousPattern =
        /<\s*\/?\s*(script|iframe|object|embed|svg|style|link|meta|form|input|textarea|select|button|php)\b[^>]*>|<\?php|<\?=|<%|%>|{{|}}|{!!|!!}|javascript\s*:|vbscript\s*:|data\s*:\s*text\/html|on[a-z]+\s*=|expression\s*\(|url\s*\(\s*javascript\s*:|@php\b|@endphp\b|@if\b|@endif\b|@foreach\b|@endforeach\b|@for\b|@endfor\b|@while\b|@endwhile\b|@include\b|@extends\b|@section\b|@yield\b|@csrf\b/i;


    function containsDangerousInput(value) {

        if (!value) {
            return false;
        }

        const normalizedValue = String(value)
            .replace(/&lt;/gi, '<')
            .replace(/&gt;/gi, '>')
            .replace(/&quot;/gi, '"')
            .replace(/&#039;/gi, "'")
            .replace(/&amp;/gi, '&')
            .trim();

        return dangerousPattern.test(value) ||
               dangerousPattern.test(normalizedValue);
    }


    // =========================================================
    // SWEETALERT ERROR
    // =========================================================

    function showError(title, text, input = null) {

        Swal.fire({
            icon: 'error',
            title: title,
            text: text,
            confirmButtonText: 'OK',
            confirmButtonColor: '#111827',
            background: '#ffffff',
            color: '#111827',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl px-5 py-2.5 font-bold'
            }
        }).then(function () {

            if (input) {
                input.focus();
                input.select?.();
            }

        });
    }


    // =========================================================
    // VALIDATE EACH FORM
    // =========================================================

    forms.forEach(function (form) {

        form.addEventListener('submit', function (event) {

            event.preventDefault();


            // -------------------------------------------------
            // INPUT
            // -------------------------------------------------

            const nameInput =
                form.querySelector('[name="name"]');

            const badgeInput =
                form.querySelector('[name="badge"]');

            const descriptionInput =
                form.querySelector('[name="description"]');

            const statusInput =
                form.querySelector('[name="status"]');

            const sortOrderInput =
                form.querySelector('[name="sort_order"]');


            // -------------------------------------------------
            // VALUE
            // -------------------------------------------------

            const name =
                nameInput?.value.trim() ?? '';

            const badge =
                badgeInput?.value.trim() ?? '';

            const description =
                descriptionInput?.value.trim() ?? '';

            const status =
                statusInput?.value ?? '';

            const sortOrder =
                sortOrderInput?.value.trim() ?? '';


            // =================================================
            // SECURE TEXT CHECK
            // =================================================

            if (containsDangerousInput(name)) {

                showError(
                    'Input Tidak Valid',
                    'Nama paket mengandung kode atau karakter yang tidak diizinkan.',
                    nameInput
                );

                return;
            }


            if (containsDangerousInput(badge)) {

                showError(
                    'Input Tidak Valid',
                    'Badge mengandung kode atau karakter yang tidak diizinkan.',
                    badgeInput
                );

                return;
            }


            if (containsDangerousInput(description)) {

                showError(
                    'Input Tidak Valid',
                    'Deskripsi mengandung kode atau karakter yang tidak diizinkan.',
                    descriptionInput
                );

                return;
            }


            // =================================================
            // NAME
            // =================================================

            if (!name) {

                showError(
                    'Nama Paket',
                    'Nama paket wajib diisi.',
                    nameInput
                );

                return;
            }


            if (name.length > 100) {

                showError(
                    'Nama Paket',
                    'Nama paket maksimal 100 karakter.',
                    nameInput
                );

                return;
            }


            // =================================================
            // BADGE
            // =================================================

            if (badge.length > 50) {

                showError(
                    'Badge',
                    'Badge maksimal 50 karakter.',
                    badgeInput
                );

                return;
            }


            // =================================================
            // DESCRIPTION
            // =================================================

            if (description.length > 1000) {

                showError(
                    'Deskripsi',
                    'Deskripsi maksimal 1000 karakter.',
                    descriptionInput
                );

                return;
            }


            // =================================================
            // STATUS
            // =================================================

            if (!status) {

                showError(
                    'Status',
                    'Status paket wajib dipilih.',
                    statusInput
                );

                return;
            }


            if (!['active', 'inactive'].includes(status)) {

                showError(
                    'Status',
                    'Status paket tidak valid.',
                    statusInput
                );

                return;
            }


            // =================================================
            // SUBMIT
            // =================================================

            form.submit();

        });

    });

});