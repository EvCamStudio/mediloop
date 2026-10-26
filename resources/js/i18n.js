const translations = (() => {
    const element = document.getElementById('js-translations');

    if (!element) {
        return {};
    }

    try {
        return JSON.parse(element.textContent ?? '{}');
    } catch {
        return {};
    }
})();

const lookup = (key) => key
    .split('.')
    .reduce((value, part) => (value && typeof value === 'object' ? value[part] : undefined), translations);

export const t = (key, replacements = {}) => {
    const value = lookup(key);

    if (typeof value !== 'string') {
        return key;
    }

    return Object.entries(replacements).reduce(
        (message, [placeholder, replacement]) => message.replaceAll(`:${placeholder}`, String(replacement)),
        value,
    );
};

export const currentLocale = () => document.documentElement.lang || 'en';

export const intlLocale = () => (currentLocale() === 'id' ? 'id-ID' : 'en-US');
