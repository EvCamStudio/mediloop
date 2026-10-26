import { t } from './i18n';

const defaultMessages = () => ({
    required: t('js.validation.required'),
    email: t('js.validation.email'),
    number: t('js.validation.number'),
    date: t('js.validation.date'),
    min: t('js.validation.min'),
    max: t('js.validation.max'),
    step: t('js.validation.step'),
    minlength: t('js.validation.minlength'),
    maxlength: t('js.validation.maxlength'),
    pattern: t('js.validation.pattern'),
    invalid: t('js.validation.invalid'),
});

const VALIDATED_FORM_SELECTOR = 'form[data-validate]';
const ERROR_SELECTOR = '[data-field-error]';
const SUMMARY_SELECTOR = '[data-form-summary]';
const SUMMARY_LIST_SELECTOR = '[data-form-summary-list]';

const isFormField = (element) => element instanceof HTMLElement
    && 'validity' in element
    && element.willValidate;

const isValidatedForm = (form) => form instanceof HTMLFormElement
    && form.matches(VALIDATED_FORM_SELECTOR);

const isVisible = (field) => field.type !== 'hidden'
    && ! field.disabled
    && field.offsetParent !== null;

const messageKeyFor = (field) => {
    const { validity } = field;

    if (validity.valueMissing) {
        return 'required';
    }

    if (validity.badInput) {
        return field.type === 'date' ? 'date' : 'number';
    }

    if (validity.typeMismatch) {
        return field.type === 'email' ? 'email' : 'invalid';
    }

    if (validity.rangeUnderflow) {
        return 'min';
    }

    if (validity.rangeOverflow) {
        return 'max';
    }

    if (validity.stepMismatch) {
        return 'step';
    }

    if (validity.tooShort) {
        return 'minlength';
    }

    if (validity.tooLong) {
        return 'maxlength';
    }

    if (validity.patternMismatch) {
        return 'pattern';
    }

    return 'invalid';
};

const customMessageFor = (field, key) => {
    const attribute = `error${key.charAt(0).toUpperCase()}${key.slice(1)}`;

    return field.dataset[attribute] ?? null;
};

const messageFor = (field) => {
    const key = messageKeyFor(field);
    const custom = customMessageFor(field, key);

    if (custom) {
        return custom;
    }

    if (key === 'min' && field.min !== '') {
        return t('js.validation.min_value', { min: field.min });
    }

    if (key === 'max' && field.max !== '') {
        return t('js.validation.max_value', { max: field.max });
    }

    if (key === 'minlength' && field.minLength > 0) {
        return t('js.validation.min_characters', { min: field.minLength });
    }

    if (key === 'maxlength' && field.maxLength > 0) {
        return t('js.validation.max_characters', { max: field.maxLength });
    }

    const messages = defaultMessages();

    return messages[key] ?? messages.invalid;
};

const errorElementFor = (field) => {
    if (! field.name || ! field.form) {
        return null;
    }

    return field.form.querySelector(`${ERROR_SELECTOR}[data-field-error="${CSS.escape(field.name)}"]`);
};

const addDescribedBy = (field, errorId) => {
    const current = (field.getAttribute('aria-describedby') ?? '').split(/\s+/).filter(Boolean);

    if (! current.includes(errorId)) {
        current.unshift(errorId);
        field.setAttribute('aria-describedby', current.join(' '));
    }
};

const removeDescribedBy = (field, errorId) => {
    const current = (field.getAttribute('aria-describedby') ?? '')
        .split(/\s+/)
        .filter((id) => id && id !== errorId);

    if (current.length > 0) {
        field.setAttribute('aria-describedby', current.join(' '));
    } else {
        field.removeAttribute('aria-describedby');
    }
};

const showError = (field, message) => {
    field.classList.add('is-invalid');
    field.setAttribute('aria-invalid', 'true');

    const errorElement = errorElementFor(field);

    if (! errorElement) {
        return;
    }

    errorElement.textContent = message;
    errorElement.hidden = false;

    if (errorElement.id) {
        addDescribedBy(field, errorElement.id);
    }
};

const clearError = (field) => {
    field.classList.remove('is-invalid');
    field.removeAttribute('aria-invalid');

    const errorElement = errorElementFor(field);

    if (! errorElement) {
        return;
    }

    errorElement.textContent = '';
    errorElement.hidden = true;

    if (errorElement.id) {
        removeDescribedBy(field, errorElement.id);
    }
};

const validateField = (field) => {
    if (! isVisible(field)) {
        return true;
    }

    if (field.validity.valid) {
        clearError(field);

        return true;
    }

    showError(field, messageFor(field));

    return false;
};

const visibleErrorElements = (form) => Array.from(form.querySelectorAll(ERROR_SELECTOR))
    .filter((errorElement) => errorElement.offsetParent !== null)
    .filter((errorElement) => (errorElement.textContent ?? '').trim() !== '');

const summaryItemFor = (form, errorElement) => {
    const field = form.elements.namedItem(errorElement.dataset.fieldError ?? '');
    const label = (errorElement.textContent ?? '').trim();
    const target = field instanceof HTMLElement && field.id ? `#${field.id}` : null;

    return { label, target };
};

const summaryFor = (form) => {
    const insideForm = form.querySelector(SUMMARY_SELECTOR);

    if (insideForm) {
        return insideForm;
    }

    if (! form.id) {
        return null;
    }

    return document.querySelector(`${SUMMARY_SELECTOR}[data-form-summary-for="${CSS.escape(form.id)}"]`);
};

const refreshSummary = (form) => {
    const summary = summaryFor(form);
    const list = summary?.querySelector(SUMMARY_LIST_SELECTOR);

    if (! summary || ! list) {
        return;
    }

    const items = visibleErrorElements(form)
        .map((errorElement) => summaryItemFor(form, errorElement))
        .filter((item) => item.label !== '');

    const nodes = items.map((item) => {
        const listItem = document.createElement('li');

        if (item.target) {
            const link = document.createElement('a');
            link.href = item.target;
            link.textContent = item.label;
            listItem.append(link);
        } else {
            listItem.textContent = item.label;
        }

        return listItem;
    });

    list.replaceChildren(...nodes);
    summary.hidden = nodes.length === 0;
};

const focusFirstInvalid = (field) => {
    field.focus({ preventScroll: true });
    field.scrollIntoView({ block: 'center' });
};

const handleSubmit = (event) => {
    const form = event.target;

    if (! isValidatedForm(form)) {
        return;
    }

    const fields = Array.from(form.elements).filter(isFormField);
    const invalidFields = fields.filter((field) => ! validateField(field));

    refreshSummary(form);

    if (invalidFields.length === 0) {
        return;
    }

    event.preventDefault();
    focusFirstInvalid(invalidFields[0]);
};

const handleFocusOut = (event) => {
    const field = event.target;

    if (! isFormField(field) || ! isValidatedForm(field.form) || ! isVisible(field)) {
        return;
    }

    const wasInvalid = field.getAttribute('aria-invalid') === 'true';
    const hasValue = field.type === 'checkbox' ? field.checked : field.value.trim() !== '';

    if (! wasInvalid && ! hasValue) {
        return;
    }

    validateField(field);
    refreshSummary(field.form);
};

const handleInput = (event) => {
    const field = event.target;

    if (! isFormField(field) || ! isValidatedForm(field.form)) {
        return;
    }

    if (field.getAttribute('aria-invalid') !== 'true') {
        return;
    }

    validateField(field);
    refreshSummary(field.form);
};

const syncServerErrors = () => {
    document.querySelectorAll(VALIDATED_FORM_SELECTOR).forEach((form) => {
        Array.from(form.querySelectorAll(ERROR_SELECTOR)).forEach((errorElement) => {
            const field = form.elements.namedItem(errorElement.dataset.fieldError ?? '');

            if (! (field instanceof HTMLElement) || ! errorElement.id) {
                return;
            }

            if (errorElement.hidden) {
                clearError(field);

                return;
            }

            field.classList.add('is-invalid');
            field.setAttribute('aria-invalid', 'true');
            addDescribedBy(field, errorElement.id);
        });
    });
};

let isInitialized = false;

const initializeFormValidation = () => {
    if (isInitialized) {
        return;
    }

    isInitialized = true;

    document.addEventListener('submit', handleSubmit, true);
    document.addEventListener('focusout', handleFocusOut);
    document.addEventListener('input', handleInput);
    document.addEventListener('livewire:navigated', syncServerErrors);

    syncServerErrors();
};

initializeFormValidation();
