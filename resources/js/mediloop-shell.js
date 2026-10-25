const getElement = (id) => document.getElementById(id);

const closeAllDropdowns = () => {
    ['ml-settings', 'ml-profile'].forEach((name) => {
        const button = getElement(`${name}-btn`);
        const dropdown = getElement(`${name}-dropdown`);

        if (dropdown) {
            dropdown.style.display = 'none';
        }

        button?.setAttribute('aria-expanded', 'false');
    });
};

const closeMobileSidebar = () => {
    const sidebar = getElement('ml-sidebar');
    const backdrop = getElement('ml-sidebar-backdrop');

    if (!sidebar && !backdrop) {
        return;
    }

    sidebar?.classList.remove('is-open');
    backdrop?.classList.remove('is-active');
    getElement('ml-hamburger-btn')?.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
};

const openMobileSidebar = () => {
    const sidebar = getElement('ml-sidebar');
    const backdrop = getElement('ml-sidebar-backdrop');

    if (!sidebar || !backdrop) {
        return;
    }

    sidebar.classList.add('is-open');
    backdrop.classList.add('is-active');
    getElement('ml-hamburger-btn')?.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
};

const toggleDropdown = (name) => {
    const button = getElement(`${name}-btn`);
    const dropdown = getElement(`${name}-dropdown`);

    if (!button || !dropdown) {
        return;
    }

    const willOpen = dropdown.style.display !== 'block';

    closeAllDropdowns();

    if (willOpen) {
        dropdown.style.display = 'block';
        button.setAttribute('aria-expanded', 'true');
    }
};

const handleDocumentClick = (event) => {
    if (!(event.target instanceof Element)) {
        return;
    }

    if (event.target.closest('#ml-hamburger-btn')) {
        openMobileSidebar();
        return;
    }

    if (event.target.closest('#ml-sidebar-close') || event.target.closest('#ml-sidebar-backdrop')) {
        closeMobileSidebar();
        return;
    }

    if (event.target.closest('#ml-settings-btn')) {
        toggleDropdown('ml-settings');
        return;
    }

    if (event.target.closest('#ml-profile-btn')) {
        toggleDropdown('ml-profile');
        return;
    }

    const isInsideSettings = event.target.closest('#ml-settings-dropdown');
    const isInsideProfile = event.target.closest('#ml-profile-dropdown');

    if (!isInsideSettings && !isInsideProfile) {
        closeAllDropdowns();
    }
};

const handleDocumentKeydown = (event) => {
    if (event.key === 'Escape') {
        closeAllDropdowns();
        closeMobileSidebar();
        return;
    }

    if (!(event.target instanceof Element) || !event.target.closest('#ml-profile-btn')) {
        return;
    }

    if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        toggleDropdown('ml-profile');
    }
};

let scrollTimeout;

const handleScroll = (event) => {
    if (!getElement('ml-sidebar')) {
        return;
    }

    document.documentElement.classList.add('is-scrolling');
    document.body.classList.add('is-scrolling');

    if (event.target instanceof Element && event.target.classList.contains('ml-table-responsive')) {
        event.target.classList.add('is-scrolling');
    }

    clearTimeout(scrollTimeout);
    scrollTimeout = setTimeout(() => {
        document.documentElement.classList.remove('is-scrolling');
        document.body.classList.remove('is-scrolling');
        document.querySelectorAll('.is-scrolling').forEach((element) => {
            element.classList.remove('is-scrolling');
        });
    }, 900);
};

const resetScrollIndicators = () => {
    document.documentElement.classList.remove('is-scrolling');
    document.body.classList.remove('is-scrolling');
    document.querySelectorAll('.is-scrolling').forEach((element) => {
        element.classList.remove('is-scrolling');
    });
};

let isInitialized = false;

const initializeMediloopShell = () => {
    if (isInitialized) {
        return;
    }

    isInitialized = true;

    document.addEventListener('click', handleDocumentClick);
    document.addEventListener('keydown', handleDocumentKeydown);
    document.addEventListener('livewire:navigating', () => {
        closeAllDropdowns();
        closeMobileSidebar();
        resetScrollIndicators();
    });
    window.addEventListener('scroll', handleScroll, { passive: true, capture: true });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeMediloopShell);
} else {
    initializeMediloopShell();
}
