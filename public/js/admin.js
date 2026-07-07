const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
const sidebarOverlay = document.querySelector('[data-sidebar-overlay]');
const desktopSidebar = window.matchMedia('(min-width: 861px)');

function syncSidebarState() {
    if (!sidebarToggle) {
        return;
    }

    const isDesktop = desktopSidebar.matches;
    const isCollapsed = document.body.classList.contains('admin-sidebar-collapsed');
    const isOpen = document.body.classList.contains('admin-sidebar-open');

    sidebarToggle.setAttribute('aria-expanded', String(isDesktop ? !isCollapsed : isOpen));
}

function closeMobileSidebar() {
    document.body.classList.remove('admin-sidebar-open');
    syncSidebarState();
}

function applyStoredDesktopState() {
    if (!desktopSidebar.matches) {
        document.body.classList.remove('admin-sidebar-collapsed');
        closeMobileSidebar();
        return;
    }

    document.body.classList.toggle(
        'admin-sidebar-collapsed',
        localStorage.getItem('eluzeAdminSidebar') === 'collapsed'
    );
    document.body.classList.remove('admin-sidebar-open');
    syncSidebarState();
}

sidebarToggle?.addEventListener('click', () => {
    if (desktopSidebar.matches) {
        document.body.classList.toggle('admin-sidebar-collapsed');
        localStorage.setItem(
            'eluzeAdminSidebar',
            document.body.classList.contains('admin-sidebar-collapsed') ? 'collapsed' : 'expanded'
        );
    } else {
        document.body.classList.toggle('admin-sidebar-open');
    }

    syncSidebarState();
});

sidebarOverlay?.addEventListener('click', closeMobileSidebar);

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        closeMobileSidebar();
    }
});

document.querySelectorAll('.admin-nav__link').forEach((link) => {
    link.addEventListener('click', closeMobileSidebar);
});

desktopSidebar.addEventListener('change', applyStoredDesktopState);
applyStoredDesktopState();
