document.addEventListener('DOMContentLoaded', function () {

    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');
    const toggleIcon = document.getElementById('sidebarToggleIcon');

    if (!sidebar || !toggle || !toggleIcon) {
        console.error('Sidebar element tidak ditemukan.');
        return;
    }


    // ==========================================================
    // SIDEBAR ELEMENT
    // ==========================================================

    const sidebarBrand =
        document.getElementById('sidebarBrand');

    const sidebarTexts =
        document.querySelectorAll('.sidebar-text');

    const sidebarSections =
        document.querySelectorAll('.sidebar-section');

    const sidebarUserInfo =
        document.querySelector('.sidebar-user-info');

    const sidebarMenus =
        document.querySelectorAll('.sidebar-menu');


    // ==========================================================
    // SIDEBAR STATE
    // ==========================================================

    let collapsed =
        localStorage.getItem('pesanin_sidebar_collapsed') === 'true';


    function updateSidebar() {

        if (collapsed) {

            sidebar.classList.remove('w-64');
            sidebar.classList.add('w-20');

            if (sidebarBrand) {
                sidebarBrand.classList.add(
                    'opacity-0',
                    'w-0',
                    'overflow-hidden'
                );
            }

            sidebarTexts.forEach(function (text) {
                text.classList.add(
                    'opacity-0',
                    'w-0',
                    'overflow-hidden'
                );
            });

            sidebarSections.forEach(function (section) {
                section.classList.add('hidden');
            });

            if (sidebarUserInfo) {
                sidebarUserInfo.classList.add(
                    'opacity-0',
                    'w-0',
                    'overflow-hidden'
                );
            }

            sidebarMenus.forEach(function (menu) {

                menu.classList.remove('gap-3');
                menu.classList.add('justify-center');

                const icon = menu.querySelector('i');

                if (icon) {
                    icon.classList.add('mx-auto');
                }
            });

            toggleIcon.classList.remove('fa-bars');
            toggleIcon.classList.add('fa-xmark');

        } else {

            sidebar.classList.remove('w-20');
            sidebar.classList.add('w-64');

            if (sidebarBrand) {
                sidebarBrand.classList.remove(
                    'opacity-0',
                    'w-0',
                    'overflow-hidden'
                );
            }

            sidebarTexts.forEach(function (text) {
                text.classList.remove(
                    'opacity-0',
                    'w-0',
                    'overflow-hidden'
                );
            });

            sidebarSections.forEach(function (section) {
                section.classList.remove('hidden');
            });

            if (sidebarUserInfo) {
                sidebarUserInfo.classList.remove(
                    'opacity-0',
                    'w-0',
                    'overflow-hidden'
                );
            }

            sidebarMenus.forEach(function (menu) {

                menu.classList.remove('justify-center');
                menu.classList.add('gap-3');

                const icon = menu.querySelector('i');

                if (icon) {
                    icon.classList.remove('mx-auto');
                }
            });

            toggleIcon.classList.remove('fa-xmark');
            toggleIcon.classList.add('fa-bars');
        }
    }


    updateSidebar();

    document.documentElement.classList.remove(
        'sidebar-precollapsed'
    );


    // ==========================================================
    // TOGGLE SIDEBAR
    // ==========================================================

    toggle.addEventListener('click', function () {

        collapsed = !collapsed;

        localStorage.setItem(
            'pesanin_sidebar_collapsed',
            collapsed
        );

        updateSidebar();
    });


    // ==========================================================
    // WITHDRAWAL NOTIFICATION
    // ==========================================================

    const notificationConfig =
        document.getElementById(
            'withdrawalNotificationConfig'
        );

    const badges =
        document.querySelectorAll(
            '.withdrawal-notification-badge'
        );


    if (!notificationConfig) {

        console.error(
            'withdrawalNotificationConfig tidak ditemukan.'
        );

        return;
    }


    if (badges.length === 0) {

        console.error(
            'Badge withdrawal tidak ditemukan.'
        );

        return;
    }


    const pendingCountUrl =
        notificationConfig.dataset.pendingCountUrl;


    if (!pendingCountUrl) {

        console.error(
            'pendingCountUrl tidak ditemukan.'
        );

        return;
    }


    console.log(
        '[Withdrawal] Notification aktif:',
        pendingCountUrl
    );


    // ==========================================================
    // UPDATE BADGE
    // ==========================================================

    function updateWithdrawalBadge(count) {

        count = Number(count) || 0;

        console.log(
            '[Withdrawal] Update badge:',
            count
        );

        badges.forEach(function (badge) {

            badge.textContent = count;

            if (count > 0) {

                badge.classList.remove('hidden');
                badge.classList.add('flex');

            } else {

                badge.classList.remove('flex');
                badge.classList.add('hidden');
            }
        });
    }


    // ==========================================================
    // FETCH PENDING COUNT
    // ==========================================================

    async function fetchWithdrawalPendingCount() {

        console.log(
            '[Withdrawal] Checking pending count...'
        );

        try {

            const url =
                pendingCountUrl +
                (pendingCountUrl.includes('?') ? '&' : '?') +
                '_=' +
                Date.now();


            const response = await fetch(url, {

                method: 'GET',

                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },

                credentials: 'same-origin',

                cache: 'no-store'
            });


            console.log(
                '[Withdrawal] HTTP:',
                response.status,
                response.url
            );


            const contentType =
                response.headers.get('content-type') || '';


            if (!response.ok) {

                throw new Error(
                    `HTTP ${response.status}`
                );
            }


            if (!contentType.includes('application/json')) {

                const text =
                    await response.text();

                console.error(
                    '[Withdrawal] Response bukan JSON:',
                    text.substring(0, 500)
                );

                throw new Error(
                    'Response bukan JSON'
                );
            }


            const data =
                await response.json();


            console.log(
                '[Withdrawal] Server count:',
                data.count
            );


            updateWithdrawalBadge(
                data.count
            );


        } catch (error) {

            console.error(
                '[Withdrawal] Polling error:',
                error
            );
        }
    }


    // ==========================================================
    // INITIAL REQUEST
    // ==========================================================

    fetchWithdrawalPendingCount();


    // ==========================================================
    // POLLING
    // ==========================================================

    setInterval(
        fetchWithdrawalPendingCount,
        5000
    );

});