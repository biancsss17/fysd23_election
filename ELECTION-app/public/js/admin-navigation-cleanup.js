document.addEventListener('DOMContentLoaded', () => {
    const adminHeader = document.querySelector('body > header');
    if (adminHeader) {
        const logo = adminHeader.querySelector('img');
        logo?.classList.add('standard-admin-logo');
        const title = Array.from(adminHeader.querySelectorAll('*')).find((element) =>
            element.children.length === 0 && element.textContent.toUpperCase().includes('DISTRICT 23 FYS')
        );
        if (title) {
            title.textContent = 'DISTRICT 23 FYS - ELECTION OF OFFICERS FOR 2027–2030';
            title.classList.add('standard-admin-title');
        }
        const headerStyles = document.createElement('style');
        headerStyles.textContent = '.standard-admin-logo{width:56px!important;height:56px!important;border-radius:9999px!important}.standard-admin-title{font-size:24px!important;line-height:1.15!important;font-weight:700!important;letter-spacing:-.01em!important;text-transform:uppercase!important}body>header{height:80px!important}body>header>div{height:80px!important;min-height:80px!important}aside{top:80px!important;width:16rem!important}body>main,body>div>main{padding-top:6rem!important}@media (min-width:1024px){body>main,body>div>main{margin-left:0!important;padding-left:18rem!important;padding-right:2rem!important}}';
        document.head.appendChild(headerStyles);
    }

    const adminNav = document.querySelector('aside nav');
    if (adminNav) {
        const navigationItems = [
            ['/admin/dashboard', 'dashboard', 'Dashboard'],
            ['/admin/voter-management', 'badge', 'Voter Management'],
            ['/admin/position-management', 'work', 'Position Management'],
            ['/admin/results-document-preview', 'description', 'Results & Document Preview'],
            ['/admin/audit-log', 'history_edu', 'Audit Log'],
        ];

        adminNav.className = 'flex flex-col gap-1 px-2';
        adminNav.replaceChildren(...navigationItems.map(([path, icon, label]) => {
            const link = adminNav.parentElement?.querySelector(`a[href$="${path}"]`) || document.querySelector(`aside a[href$="${path}"]`);
            const item = link || document.createElement('a');
            item.href = path;
            const isActive = window.location.pathname === path || (path !== '/admin/dashboard' && window.location.pathname.startsWith(`${path}/`));
            item.className = isActive
                ? 'flex items-center gap-3 rounded border-l-4 border-[#ffdf98] bg-[#dee8ff] px-4 py-3 pl-5 text-base font-bold text-[#115cb9] transition'
                : 'flex items-center gap-3 rounded px-4 py-3 text-base text-[#263143] transition hover:bg-[#dee8ff] hover:text-[#111c2d]';
            item.removeAttribute('aria-current');
            item.innerHTML = `<span class="material-symbols-outlined text-[22px]">${icon}</span><span>${label}</span>`;
            return item;
        }));
    }

    if (!document.querySelector('.mobile-admin-nav')) {
        const mobileNav = document.createElement('nav');
        mobileNav.className = 'mobile-admin-nav';
        mobileNav.setAttribute('aria-label', 'Admin navigation');
        mobileNav.innerHTML = [
            ['/admin/dashboard', 'dashboard', 'Dashboard'],
            ['/admin/voter-management', 'badge', 'Voters'],
            ['/admin/position-management', 'work', 'Positions'],
            ['/admin/results-document-preview', 'description', 'Results'],
            ['/admin/audit-log', 'history_edu', 'Audit'],
        ].map(([path, icon, label]) => `<a href="${path}"><span class="material-symbols-outlined">${icon}</span><span>${label}</span></a>`).join('');
        document.body.appendChild(mobileNav);

        const responsiveStyles = document.createElement('style');
        responsiveStyles.textContent = `
            .mobile-admin-nav { display:none; }
            @media (max-width: 1023px) {
                html, body { min-height:100%; overflow-x:hidden !important; overflow-y:auto !important; }
                body { padding-bottom: 4.75rem !important; }
                .mobile-admin-nav { position:fixed; inset:auto 0 0; z-index:60; display:grid; grid-template-columns:repeat(5,minmax(0,1fr)); gap:.25rem; padding:.45rem .5rem calc(.45rem + env(safe-area-inset-bottom)); background:#0e1c2f; box-shadow:0 -2px 12px rgba(0,0,0,.18); }
                .mobile-admin-nav a { min-width:0; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:.15rem; min-height:3.2rem; border-radius:.5rem; color:#d8e3fb; font-size:.68rem; line-height:1; text-decoration:none; }
                .mobile-admin-nav a:hover, .mobile-admin-nav a:focus-visible { background:#1d3557; color:#fff; outline:none; }
                .mobile-admin-nav .material-symbols-outlined { font-size:1.3rem; }
                body > header { height:auto !important; min-height:5rem; }
                body > header > div { min-height:5rem; height:auto !important; padding:.65rem .75rem !important; align-items:center; }
                body > header > div > div:first-child { min-width:0; flex:1 1 auto; }
                .standard-admin-logo { width:44px !important; height:44px !important; }
                .standard-admin-title { display:block !important; min-width:0 !important; max-width:none; overflow-wrap:anywhere; text-overflow:clip !important; white-space:normal !important; font-size:clamp(.72rem,3.9vw,1.1rem) !important; line-height:1.12 !important; }
                body > header form { display:block !important; }
                body > header form button { padding:.55rem .65rem !important; font-size:.7rem !important; white-space:nowrap; }
                body > header > div > div:last-child > span { display:none !important; }
                aside { display:none !important; }
                main { margin-left:0 !important; padding-left:1rem !important; padding-right:1rem !important; min-width:0; width:100%; max-width:100%; overflow:visible; overflow-wrap:anywhere; }
                main > div, main section, main article { max-width:100%; min-width:0; }
                main h1 { font-size:clamp(1.35rem,6vw,2rem) !important; line-height:1.15; }
                main h2 { font-size:clamp(1.15rem,5vw,1.5rem) !important; line-height:1.2; }
                main table { max-width:none; }
            }
            @media (max-width: 480px) {
                .mobile-admin-nav a { font-size:.6rem; }
                .mobile-admin-nav .material-symbols-outlined { font-size:1.15rem; }
                main { padding-left:.75rem !important; padding-right:.75rem !important; }
                body > header > div { gap:.5rem; }
                .standard-admin-logo { width:40px !important; height:40px !important; }
                .standard-admin-title { font-size:clamp(.66rem,3.7vw,.9rem) !important; }
                body > header form button { padding:.45rem .5rem !important; font-size:.62rem !important; }
            }
        `;
        document.head.appendChild(responsiveStyles);
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

    const statusLabel = Array.from(document.querySelectorAll('aside *')).find((element) =>
        element.children.length === 0 && element.textContent.trim() === 'Status: Official Session'
    );

    statusLabel?.closest('div.rounded')?.remove();

    const rosterLabel = Array.from(document.querySelectorAll('aside *')).find((element) =>
        element.children.length === 0 && element.textContent.trim() === 'Roster Administration'
    );

    rosterLabel?.closest('div.rounded')?.remove();
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
