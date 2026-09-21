document.addEventListener('DOMContentLoaded', function () {

    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');
    const toggleIcon = document.getElementById('sidebarToggleIcon');

    const sidebarBrand = document.getElementById('sidebarBrand');

    const sidebarTexts =
        document.querySelectorAll('.sidebar-text');

    const sidebarMenus =
        document.querySelectorAll('.sidebar-menu');

    const sidebarUserInfo =
        document.querySelector('.sidebar-user-info');

    // Chevron khusus menu dropdown
    const sidebarChevrons =
        document.querySelectorAll('.sidebar-chevron');

    if (!sidebar || !toggle) {
        return;
    }

    let collapsed =
        localStorage.getItem('pesanin_admin_sidebar_collapsed') === 'true';


    function updateSidebar() {

        if (collapsed) {

            // ==========================================
            // SIDEBAR COLLAPSED
            // ==========================================

            sidebar.classList.remove('w-64');
            sidebar.classList.add('w-20');


            // ------------------------------------------
            // Brand
            // ------------------------------------------

            if (sidebarBrand) {
                sidebarBrand.classList.add(
                    'opacity-0',
                    'w-0',
                    'overflow-hidden'
                );
            }


            // ------------------------------------------
            // Text menu
            // ------------------------------------------

            sidebarTexts.forEach(function (text) {
                text.classList.add(
                    'opacity-0',
                    'w-0',
                    'overflow-hidden'
                );
            });


            // ------------------------------------------
            // User info
            // ------------------------------------------

            if (sidebarUserInfo) {
                sidebarUserInfo.classList.add(
                    'opacity-0',
                    'w-0',
                    'overflow-hidden'
                );
            }


            // ------------------------------------------
            // Menu
            // ------------------------------------------

            sidebarMenus.forEach(function (menu) {

                menu.classList.remove('gap-3');
                menu.classList.add('justify-center');

                const icon = menu.querySelector('i');

                if (icon && !icon.classList.contains('fa-chevron-down')) {
                    icon.classList.add('mx-auto');
                }
            });


            // ------------------------------------------
            // Chevron dropdown
            // ------------------------------------------

            sidebarChevrons.forEach(function (chevron) {

                chevron.classList.add(
                    'opacity-0',
                    'w-0',
                    'overflow-hidden'
                );

            });


            // ------------------------------------------
            // Hamburger icon
            // ------------------------------------------

            if (toggleIcon) {

                toggleIcon.classList.remove('fa-bars');
                toggleIcon.classList.add('fa-xmark');

            }


        } else {

            // ==========================================
            // SIDEBAR NORMAL
            // ==========================================

            sidebar.classList.remove('w-20');
            sidebar.classList.add('w-64');


            // ------------------------------------------
            // Brand
            // ------------------------------------------

            if (sidebarBrand) {
                sidebarBrand.classList.remove(
                    'opacity-0',
                    'w-0',
                    'overflow-hidden'
                );
            }


            // ------------------------------------------
            // Text menu
            // ------------------------------------------

            sidebarTexts.forEach(function (text) {
                text.classList.remove(
                    'opacity-0',
                    'w-0',
                    'overflow-hidden'
                );
            });


            // ------------------------------------------
            // User info
            // ------------------------------------------

            if (sidebarUserInfo) {
                sidebarUserInfo.classList.remove(
                    'opacity-0',
                    'w-0',
                    'overflow-hidden'
                );
            }


            // ------------------------------------------
            // Menu
            // ------------------------------------------

            sidebarMenus.forEach(function (menu) {

                menu.classList.remove('justify-center');
                menu.classList.add('gap-3');

                const icon = menu.querySelector('i');

                if (icon && !icon.classList.contains('fa-chevron-down')) {
                    icon.classList.remove('mx-auto');
                }
            });


            // ------------------------------------------
            // Chevron dropdown
            // ------------------------------------------

            sidebarChevrons.forEach(function (chevron) {

                chevron.classList.remove(
                    'opacity-0',
                    'w-0',
                    'overflow-hidden'
                );

            });


            // ------------------------------------------
            // Hamburger icon
            // ------------------------------------------

            if (toggleIcon) {

                toggleIcon.classList.remove('fa-xmark');
                toggleIcon.classList.add('fa-bars');

            }
        }
    }


    // Terapkan kondisi awal
    updateSidebar();
    document.documentElement.classList.remove(
        'sidebar-precollapsed'
    );

    // ==========================================
    // TOGGLE SIDEBAR
    // ==========================================

    toggle.addEventListener('click', function () {

        collapsed = !collapsed;

        localStorage.setItem(
            'pesanin_admin_sidebar_collapsed',
            collapsed
        );

        updateSidebar();

    });

});