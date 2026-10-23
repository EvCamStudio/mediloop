const passwordToggles = document.querySelectorAll('[data-password-toggle]');

passwordToggles.forEach((toggle) => {
    const targetId = toggle.getAttribute('data-password-target');
    const control = toggle.closest('.password-control');
    const input = targetId
        ? document.getElementById(targetId)
        : control?.querySelector('input');

    if (!(input instanceof HTMLInputElement)) {
        return;
    }

    const showLabel = toggle.dataset.labelShow ?? 'Tampilkan password';
    const hideLabel = toggle.dataset.labelHide ?? 'Sembunyikan password';

    toggle.addEventListener('click', () => {
        const isVisible = input.type === 'text';

        input.type = isVisible ? 'password' : 'text';
        toggle.setAttribute('aria-pressed', String(!isVisible));
        toggle.setAttribute('aria-label', isVisible ? showLabel : hideLabel);
        toggle.classList.toggle('is-visible', !isVisible);
        input.focus({ preventScroll: true });
    });
});
