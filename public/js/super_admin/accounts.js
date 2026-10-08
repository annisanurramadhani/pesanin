document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('#account-filter-form');
    const searchInput = document.querySelector('#account-search');
    const roleSelect = document.querySelector('#account-role');
    const statusSelect = document.querySelector('#account-status');
    const tableWrapper = document.querySelector('#accounts-table-wrapper');

    let searchTimeout = null;
    let controller = null;

    /**
     * =========================================================
     * ACCOUNT FILTER
     * =========================================================
     */

    const loadAccounts = (resetPage = true) => {
        if (!form || !tableWrapper) return;

        const url = new URL(form.action, window.location.origin);
        const formData = new FormData(form);

        for (const [key, value] of formData.entries()) {
            if (value !== '') {
                url.searchParams.set(key, value);
            }
        }

        if (resetPage) {
            url.searchParams.delete('page');
        }

        // Batalkan request sebelumnya jika masih berjalan
        if (controller) {
            controller.abort();
        }

        controller = new AbortController();

        tableWrapper.classList.add('opacity-60', 'pointer-events-none');

        fetch(url.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
            },
            signal: controller.signal,
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal mengambil data akun.');
                }

                return response.text();
            })
            .then(html => {
                const parser = new DOMParser();
                const document = parser.parseFromString(html, 'text/html');

                const newTableWrapper =
                    document.querySelector('#accounts-table-wrapper');

                if (newTableWrapper) {
                    tableWrapper.innerHTML = newTableWrapper.innerHTML;
                }

                // Update URL tanpa reload
                window.history.replaceState(
                    {},
                    '',
                    url.toString()
                );

                // Re-bind pagination setelah HTML diganti
                bindPagination();

                // Re-bind delete confirmation
                bindDeleteConfirmation();
            })
            .catch(error => {
                if (error.name !== 'AbortError') {
                    console.error('Account filter error:', error);
                }
            })
            .finally(() => {
                tableWrapper.classList.remove(
                    'opacity-60',
                    'pointer-events-none'
                );
            });
    };

    /**
     * =========================================================
     * LIVE SEARCH
     * =========================================================
     */

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);

            searchTimeout = setTimeout(() => {
                loadAccounts(true);
            }, 300);
        });
    }

    /**
     * =========================================================
     * ROLE FILTER
     * =========================================================
     */

    if (roleSelect) {
        roleSelect.addEventListener('change', () => {
            loadAccounts(true);
        });
    }

    /**
     * =========================================================
     * STATUS FILTER
     * =========================================================
     */

    if (statusSelect) {
        statusSelect.addEventListener('change', () => {
            loadAccounts(true);
        });
    }

    /**
     * =========================================================
     * FORM FILTER
     * =========================================================
     */

    if (form) {
        form.addEventListener('submit', event => {
            event.preventDefault();

            loadAccounts(true);
        });
    }

    /**
     * =========================================================
     * PAGINATION
     * =========================================================
     */

    const bindPagination = () => {
        const paginationLinks =
            tableWrapper?.querySelectorAll('a[href*="page="]');

        paginationLinks?.forEach(link => {
            link.addEventListener('click', event => {
                event.preventDefault();

                const url = new URL(link.href);

                // Pertahankan search
                if (searchInput?.value.trim()) {
                    url.searchParams.set(
                        'search',
                        searchInput.value.trim()
                    );
                }

                // Pertahankan role
                if (roleSelect?.value) {
                    url.searchParams.set(
                        'role',
                        roleSelect.value
                    );
                }

                // Pertahankan status
                if (statusSelect?.value) {
                    url.searchParams.set(
                        'status',
                        statusSelect.value
                    );
                }

                loadAccountsFromUrl(url);
            });
        });
    };

    /**
     * =========================================================
     * LOAD PAGINATION URL
     * =========================================================
     */

    const loadAccountsFromUrl = url => {
        if (!tableWrapper) return;

        if (controller) {
            controller.abort();
        }

        controller = new AbortController();

        tableWrapper.classList.add(
            'opacity-60',
            'pointer-events-none'
        );

        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
            },
            signal: controller.signal,
        })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const document = parser.parseFromString(
                    html,
                    'text/html'
                );

                const newTableWrapper =
                    document.querySelector('#accounts-table-wrapper');

                if (newTableWrapper) {
                    tableWrapper.innerHTML =
                        newTableWrapper.innerHTML;
                }

                window.history.replaceState(
                    {},
                    '',
                    url.toString()
                );

                bindPagination();
                bindDeleteConfirmation();
            })
            .catch(error => {
                if (error.name !== 'AbortError') {
                    console.error(
                        'Pagination error:',
                        error
                    );
                }
            })
            .finally(() => {
                tableWrapper.classList.remove(
                    'opacity-60',
                    'pointer-events-none'
                );
            });
    };

    /**
     * =========================================================
     * DELETE CONFIRMATION
     * =========================================================
     */

    const bindDeleteConfirmation = () => {
        document
            .querySelectorAll('.delete-account-form')
            .forEach(form => {
                // Hindari event listener dobel
                if (form.dataset.confirmBound === 'true') {
                    return;
                }

                form.dataset.confirmBound = 'true';

                form.addEventListener('submit', function (event) {
                    event.preventDefault();

                    Swal.fire({
                        title: 'Hapus akun?',
                        text: 'Akun yang dihapus tidak dapat digunakan lagi.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                    }).then(result => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
    };

    /**
     * =========================================================
     * INITIALIZE
     * =========================================================
     */

    bindPagination();
    bindDeleteConfirmation();
});