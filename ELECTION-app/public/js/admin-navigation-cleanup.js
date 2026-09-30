document.addEventListener('DOMContentLoaded', () => {
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

    return async () => {
        if (refreshing || document.hidden || window.isEditingWinner || document.activeElement?.isContentEditable) return;
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
            if (currentMain && nextMain && currentMain.innerHTML !== nextMain.innerHTML) {
                currentMain.replaceWith(nextMain);
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
