import './mediloop-shell';
import './form-validation';
import './batch-form';
import { t } from './i18n';

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

    const showLabel = toggle.dataset.labelShow ?? t('js.password.show');
    const hideLabel = toggle.dataset.labelHide ?? t('js.password.hide');

    toggle.addEventListener('click', () => {
        const isVisible = input.type === 'text';

        input.type = isVisible ? 'password' : 'text';
        toggle.setAttribute('aria-pressed', String(!isVisible));
        toggle.setAttribute('aria-label', isVisible ? showLabel : hideLabel);
        toggle.classList.toggle('is-visible', !isVisible);
        input.focus({ preventScroll: true });
    });
});
