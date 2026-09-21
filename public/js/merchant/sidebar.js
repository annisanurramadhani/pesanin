document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');
    const toggleIcon = document.getElementById('sidebarToggleIcon');

    if (!sidebar || !toggle || !toggleIcon) {
        return;
    }

    const sidebarBrand = document.getElementById('sidebarBrand');
    const sidebarTexts = document.querySelectorAll('.sidebar-text');
    const sidebarSections = document.querySelectorAll('.sidebar-section');
    const sidebarUserInfo = document.querySelector('.sidebar-user-info');
    const sidebarMenus = document.querySelectorAll('.sidebar-menu');

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

    // Terapkan state sidebar saat halaman dibuka
    updateSidebar();

    document.documentElement.classList.remove(
        'sidebar-precollapsed'
    );

    // Toggle sidebar
    toggle.addEventListener('click', function () {
        collapsed = !collapsed;

        localStorage.setItem(
            'pesanin_sidebar_collapsed',
            collapsed
        );

        updateSidebar();
    });
});