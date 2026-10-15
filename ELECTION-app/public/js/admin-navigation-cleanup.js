document.addEventListener('DOMContentLoaded', () => {
    const logoutForm = document.querySelector('form[action$="/admin/logout"]');

    // The logout form needs its own handler. If the generic handler marks it as
    // submitting first, a second handler can mistake the first submit for a
    // duplicate and cancel the actual POST, leaving the button stuck on
    // "Processing…".
    document.querySelectorAll('form').forEach((form) => {
        if (form === logoutForm) return;

        form.addEventListener('submit', (event) => {
            form.dataset.submitting = 'true';
            const submitter = event.submitter || form.querySelector('button[type="submit"]');
            if (submitter) {
                submitter.disabled = true;
                submitter.dataset.originalLabel = submitter.innerHTML;
                submitter.textContent = 'Processing…';
            }
        });
    });

    if (logoutForm) {
        logoutForm.addEventListener('submit', (event) => {
            if (logoutForm.dataset.submitting === 'true') {
                event.preventDefault();
                return;
            }

            logoutForm.dataset.submitting = 'true';
            window.adminLogoutInProgress = true;
            const submitter = event.submitter || logoutForm.querySelector('button[type="submit"]');
            if (submitter) {
                submitter.disabled = true;
                submitter.textContent = 'Signing out…';
            }
            // Leave the first submit's default action intact so the browser
            // sends the POST to the server immediately.
        });
    }

    document.querySelectorAll('a[data-path="login"]').forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = window.adminLogoutUrl || '/admin/logout';
            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = window.adminCsrfToken || '';
            form.appendChild(token);
            document.body.appendChild(form);
            form.submit();
        });
    });

});

window.refreshLivePage = window.refreshLivePage || (() => {
    let refreshing = false;

    const comparableMainMarkup = (main) => {
        const clone = main.cloneNode(true);

        clone.querySelectorAll('[data-dashboard-abstain], [data-abstain-result]').forEach((element) => element.remove());
        clone.querySelectorAll('span').forEach((element) => {
            const label = element.textContent.trim();
            if (label === 'ABSTAIN' || label === 'FINAL RESULT') element.closest('div.flex, span')?.remove();
        });
        clone.querySelectorAll('section').forEach((section) => {
            if (section.querySelector('h2')?.textContent.trim() === 'Abstentions by position') section.remove();
        });

        return clone.innerHTML;
    };

    return async () => {
        if (refreshing || window.adminLogoutInProgress || document.hidden || document.body.classList.contains('position-management-page') || window.isEditingWinner || document.activeElement?.isContentEditable || document.querySelector('form[data-submitting="true"]')) return;
        refreshing = true;

        try {
            const response = await fetch(window.location.href, {
                headers: { Accept: 'text/html' },
                cache: 'no-store',
                credentials: 'same-origin',
            });
            if (!response.ok) return;

            // Never inject the standalone login page into an authenticated admin shell.
            // A full navigation removes the old header/sidebar as soon as the session ends.
            if (new URL(response.url, window.location.href).pathname === '/admin/login') {
                window.location.assign(response.url);
                return;
            }

            const documentText = await response.text();
            const nextDocument = new DOMParser().parseFromString(documentText, 'text/html');

            // Also guard against a login response returned with the current URL.
            // The login form is standalone and must never be mounted inside the old admin page.
            if (nextDocument.querySelector('#admin-login-form') && !nextDocument.querySelector('body > header')) {
                window.location.assign(response.url || window.location.href);
                return;
            }

            const currentMain = document.querySelector('main');
            const nextMain = nextDocument.querySelector('main');
            if (currentMain && nextMain && comparableMainMarkup(currentMain) !== comparableMainMarkup(nextMain)) {
                const scrollX = window.scrollX;
                const scrollY = window.scrollY;
                currentMain.replaceWith(nextMain);
                window.scrollTo(scrollX, scrollY);
                document.dispatchEvent(new CustomEvent('admin-main-refreshed'));
            }
            const currentResultsDocument = document.querySelector('[data-live-results-document]');
            const nextResultsDocument = nextDocument.querySelector('[data-live-results-document]');
            if (currentResultsDocument && nextResultsDocument) currentResultsDocument.replaceWith(nextResultsDocument);
            window.updateResultLabels?.();
            window.updateDashboardResult?.();
        } catch {
            // Live updates are best effort and should not interrupt the page.
        } finally {
            refreshing = false;
        }
    };
})();
