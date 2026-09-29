document.querySelector('.notice__close')?.addEventListener('click', (event) => {
    event.currentTarget.closest('.notice')?.remove();
});

document.querySelectorAll('form[data-task-form]').forEach((form) => {
    const title = form.querySelector('#title');
    const error = form.querySelector('#title-error');

    const validateTitle = () => {
        const isEmpty = !title.value.trim();
        title.setAttribute('aria-invalid', String(isEmpty));
        error.hidden = !isEmpty;
        return !isEmpty;
    };

    title.addEventListener('input', () => {
        if (title.getAttribute('aria-invalid') === 'true') validateTitle();
    });

    form.addEventListener('submit', (event) => {
        if (!validateTitle()) {
            event.preventDefault();
            title.focus();
        }
    });
});

document.querySelectorAll('form[data-disable-on-submit]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (event.defaultPrevented) return;

        const button = form.querySelector('button[type="submit"]');
        if (!button) return;

        button.disabled = true;
        button.textContent = button.dataset.loadingText || 'Please wait…';
    });
});
