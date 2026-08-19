document.addEventListener("DOMContentLoaded", function () {
    // =========================================================
    // DELETE PACKAGE DURATION
    // =========================================================

    const deleteForms = document.querySelectorAll(".delete-duration-form");

    deleteForms.forEach(function (form) {
        form.addEventListener("submit", function (event) {
            event.preventDefault();

            Swal.fire({
                icon: "warning",
                title: "Hapus Durasi?",
                text: "Durasi yang dihapus tidak dapat dikembalikan.",
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus",
                cancelButtonText: "Batal",
                confirmButtonColor: "#f59e0b",
                cancelButtonColor: "#111827",
                background: "#ffffff",
                color: "#111827",
                reverseButtons: true,
                customClass: {
                    popup: "rounded-2xl",
                    confirmButton: "rounded-xl px-5 py-2.5 font-bold",
                    cancelButton: "rounded-xl px-5 py-2.5 font-bold",
                },
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // =========================================================
    // CREATE / EDIT DURATION
    // =========================================================

    const forms = document.querySelectorAll(".duration-form");

    if (!forms.length) {
        return;
    }

    // =========================================================
    // SECURE TEXT
    // =========================================================

    const dangerousPattern =
        /<\s*\/?\s*(script|iframe|object|embed|svg|style|link|meta|form|input|textarea|select|button|php)\b[^>]*>|\<\?php|\<\?=|<%|%>|{{|}}|{!!|!!}|javascript\s*:|vbscript\s*:|data\s*:\s*text\/html|on[a-z]+\s*=|expression\s*\(|url\s*\(\s*javascript\s*:|@php\b|@endphp\b|@if\b|@endif\b|@foreach\b|@endforeach\b|@for\b|@endfor\b|@while\b|@endwhile\b|@include\b|@extends\b|@section\b|@yield\b|@csrf\b/i;

    function containsDangerousInput(value) {
        if (!value) {
            return false;
        }

        const normalizedValue = String(value)
            .replace(/&lt;/gi, "<")
            .replace(/&gt;/gi, ">")
            .replace(/&quot;/gi, '"')
            .replace(/&#039;/gi, "'")
            .replace(/&amp;/gi, "&")
            .trim();

        return dangerousPattern.test(normalizedValue);
    }

    // =========================================================
    // SWEETALERT ERROR
    // =========================================================

    function showError(title, text, input = null) {
        Swal.fire({
            icon: "error",
            title: title,
            text: text,
            confirmButtonText: "OK",
            confirmButtonColor: "#111827",
            background: "#ffffff",
            color: "#111827",
            customClass: {
                popup: "rounded-2xl",
                confirmButton: "rounded-xl px-5 py-2.5 font-bold",
            },
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
        form.addEventListener("submit", function (event) {
            event.preventDefault();

            // -------------------------------------------------
            // INPUT
            // -------------------------------------------------

            const nameInput = form.querySelector('[name="name"]');

            const durationDaysInput = form.querySelector(
                '[name="duration_days"]',
            );

            const priceInput = form.querySelector('[name="price"]');

            const discountPriceInput = form.querySelector(
                '[name="discount_price"]',
            );

            const sortOrderInput = form.querySelector('[name="sort_order"]');

            const statusInput = form.querySelector('[name="status"]');

            // -------------------------------------------------
            // VALUE
            // -------------------------------------------------

            const name = nameInput?.value.trim() ?? "";

            console.log('nameInput:', nameInput);
console.log('name value:', name);

            const durationDays = durationDaysInput?.value.trim() ?? "";

            const price = priceInput?.value.trim() ?? "";

            const discountPrice = discountPriceInput?.value.trim() ?? "";

            const sortOrder = sortOrderInput?.value.trim() ?? "";

            const status = statusInput?.value ?? "";

            // =================================================
            // NAME
            // =================================================

            if (!name) {
                showError(
                    "Data Belum Lengkap",
                    "Silakan lengkapi data terlebih dahulu.",
                    nameInput,
                );

                return;
            }

            // =================================================
            // SECURE TEXT CHECK
            // =================================================

            if (containsDangerousInput(name)) {
                showError(
                    "Input Tidak Valid",
                    "Nama durasi mengandung kode atau karakter yang tidak diizinkan.",
                    nameInput,
                );

                return;
            }

            // =================================================
            // DURATION DAYS
            // =================================================

            if (durationDays === "") {
                showError(
                    "Durasi",
                    "Jumlah hari wajib diisi.",
                    durationDaysInput,
                );

                return;
            }

            if (!/^\d+$/.test(durationDays)) {
                showError(
                    "Durasi",
                    "Durasi hanya boleh berupa angka.",
                    durationDaysInput,
                );

                return;
            }

            if (Number(durationDays) < 1) {
                showError(
                    "Durasi",
                    "Durasi minimal 1 hari.",
                    durationDaysInput,
                );

                return;
            }

            // =================================================
            // PRICE
            // =================================================

            if (price === "") {
                showError(
                    "Harga Normal",
                    "Harga normal wajib diisi.",
                    priceInput,
                );

                return;
            }

            if (!/^\d+(\.\d+)?$/.test(price)) {
                showError(
                    "Harga Normal",
                    "Harga normal hanya boleh berupa angka.",
                    priceInput,
                );

                return;
            }

            if (Number(price) < 0) {
                showError(
                    "Harga Normal",
                    "Harga normal tidak boleh kurang dari 0.",
                    priceInput,
                );

                return;
            }

            // =================================================
            // DISCOUNT PRICE
            // =================================================

            if (discountPrice !== "") {
                if (!/^\d+(\.\d+)?$/.test(discountPrice)) {
                    showError(
                        "Harga Diskon",
                        "Harga diskon hanya boleh berupa angka.",
                        discountPriceInput,
                    );

                    return;
                }

                if (Number(discountPrice) < 0) {
                    showError(
                        "Harga Diskon",
                        "Harga diskon tidak boleh kurang dari 0.",
                        discountPriceInput,
                    );

                    return;
                }

                if (Number(discountPrice) > Number(price)) {
                    showError(
                        "Harga Diskon",
                        "Harga diskon tidak boleh lebih besar dari harga normal.",
                        discountPriceInput,
                    );

                    return;
                }
            }

            // =================================================
            // STATUS
            // =================================================

            if (!status) {
                showError("Status", "Status wajib dipilih.", statusInput);

                return;
            }

            if (!["active", "inactive"].includes(status)) {
                showError("Status", "Status tidak valid.", statusInput);

                return;
            }

            // =================================================
            // SUBMIT
            // =================================================

            form.submit();
        });
    });
});
