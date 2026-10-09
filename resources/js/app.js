import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

/**
 * Destructive actions: <form data-confirm="Delete Amapro FC?"> asks for confirmation
 * in the shared <dialog id="confirm-dialog"> before submitting. Runs in the capture
 * phase so it can cancel the submit before the loading handler below reacts.
 */
document.addEventListener('submit', (event) => {
    const form = event.target;
    const dialog = document.getElementById('confirm-dialog');
    if (!(form instanceof HTMLFormElement) || !form.dataset.confirm || form.dataset.confirmed || !dialog) return;

    event.preventDefault();
    const submitter = event.submitter;
    dialog.querySelector('[data-confirm-message]').textContent = form.dataset.confirm;
    dialog.querySelector('[data-confirm-button]').textContent = form.dataset.confirmLabel || 'Delete';
    dialog.showModal();

    dialog.addEventListener('close', () => {
        if (dialog.returnValue !== 'confirm') return;
        form.dataset.confirmed = 'true';
        form.requestSubmit(submitter && form.contains(submitter) ? submitter : undefined);
    }, { once: true });
}, true);

/**
 * Submit feedback for account forms: the clicked submit button shows a spinner and
 * its data-loading-text, and repeat submissions are blocked until the page changes.
 */
document.addEventListener('submit', (event) => {
    const form = event.target;
    if (event.defaultPrevented || !(form instanceof HTMLFormElement) || form.method === 'dialog') return;

    if (form.dataset.submitting) {
        event.preventDefault();
        return;
    }
    form.dataset.submitting = 'true';

    const button = event.submitter ?? form.querySelector('[type="submit"]');
    if (!button || !button.dataset.loadingText) return;

    button.dataset.originalHtml = button.innerHTML;
    button.classList.add('is-loading');
    button.setAttribute('aria-disabled', 'true');
    button.innerHTML = `<span class="spinner" aria-hidden="true"></span> ${button.dataset.loadingText}`;
});

// Restore buttons when a page is restored from the back/forward cache.
window.addEventListener('pageshow', (event) => {
    if (!event.persisted) return;
    document.querySelectorAll('form[data-submitting]').forEach((form) => delete form.dataset.submitting);
    document.querySelectorAll('.btn.is-loading').forEach((button) => {
        button.innerHTML = button.dataset.originalHtml;
        button.classList.remove('is-loading');
        button.removeAttribute('aria-disabled');
    });
});
