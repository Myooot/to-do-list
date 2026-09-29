document.querySelectorAll('[data-max-length]').forEach((field) => {
    const maxLength = Number.parseInt(field.dataset.maxLength, 10);
    const hintId = field.getAttribute('aria-describedby');
    const hint = hintId ? document.getElementById(hintId) : null;

    if (!Number.isFinite(maxLength) || hint === null) {
        return;
    }

    const updateCount = () => {
        const length = field.value.length;
        const isTooLong = length > maxLength;

        hint.textContent = `${length} / ${maxLength} characters`;
        hint.classList.toggle('field-hint--error', isTooLong);
        field.setCustomValidity(isTooLong ? `Please use ${maxLength} characters or fewer.` : '');
    };

    field.addEventListener('input', updateCount);
    updateCount();
});
