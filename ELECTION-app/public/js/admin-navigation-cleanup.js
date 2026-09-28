document.addEventListener('DOMContentLoaded', () => {
    const escapeHtml = (value) => String(value).replace(/[&<>'"]/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' })[character]);

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
        headerStyles.textContent = '.standard-admin-logo{width:56px!important;height:56px!important;border-radius:9999px!important}.standard-admin-title{font-size:24px!important;line-height:1.15!important;font-weight:700!important;letter-spacing:-.01em!important;text-transform:uppercase!important}body>header{height:80px!important}body>header>div{height:80px!important;min-height:80px!important}aside{top:80px!important}body>main,body>div>main{padding-top:6rem!important}';
        document.head.appendChild(headerStyles);
    }

    if (Array.isArray(window.adminSubmissions)) {
        const tableBody = document.getElementById('nominations-table-body');
        if (tableBody) {
            tableBody.innerHTML = window.adminSubmissions.length
                ? window.adminSubmissions.map((submission) => `<tr class="hover:bg-surface-container-low transition-colors"><td class="py-space-md px-space-lg text-center"><div class="flex items-center justify-center gap-space-sm"><span class="material-symbols-outlined text-secondary text-[20px]">badge</span><div><div class="font-headline-sm text-headline-sm text-primary-container">${escapeHtml(submission.candidate_name)}</div><div class="font-body-sm text-body-sm text-on-surface-variant">${escapeHtml(submission.submitted_by_email)}</div></div></div></td><td class="py-space-md px-space-md text-center">${escapeHtml(submission.submission_type)}</td><td class="py-space-md px-space-lg text-center"><span class="rounded bg-tertiary-fixed px-space-sm py-1 font-label-md text-label-md">${escapeHtml(submission.status)}</span></td></tr>`).join('')
                : '<tr><td colspan="3" class="py-10 text-center text-on-surface-variant">No candidacy or nomination submissions yet.</td></tr>';
        }

        const finalBallotSection = Array.from(document.querySelectorAll('main section')).find((section) =>
            section.querySelector('h2')?.textContent.includes('FINAL BALLOT')
        );
        const finalBallotGrid = finalBallotSection?.querySelector('.grid');
        if (finalBallotGrid) {
            finalBallotGrid.innerHTML = window.adminSubmissions.length
                ? window.adminSubmissions.map((submission, index) => `<div class="p-space-md bg-surface-container-low rounded-lg flex flex-col justify-between space-y-space-md hover:shadow-sm transition-shadow"><div class="flex items-start justify-between"><div class="space-y-1"><span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Ballot Slot #${String(index + 1).padStart(2, '0')}</span><div class="font-headline-md text-headline-md text-primary-container">${escapeHtml(submission.candidate_name)}</div><div class="font-body-sm text-body-sm text-on-surface-variant">${escapeHtml(submission.submission_type)}</div></div><div class="w-8 h-8 rounded-full bg-secondary text-on-secondary flex items-center justify-center font-bold">✓</div></div><div class="pt-space-sm text-on-surface-variant font-label-sm text-label-sm flex items-center gap-1"><span class="material-symbols-outlined text-[16px] text-secondary">check_circle</span>${escapeHtml(submission.status)}</div></div>`).join('')
                : '<div class="col-span-full rounded-lg border border-dashed border-surface-container-high p-space-lg text-center text-on-surface-variant">No candidates are currently listed for the final ballot.</div>';
        }
    }

    document.querySelectorAll('a[href$="/admin/audit-log"]').forEach((link) => link.remove());
    document.querySelectorAll('a[href$="/admin/candidates-nominations"]').forEach((link) => link.remove());

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
            item.className = 'flex items-center gap-3 rounded px-4 py-3 text-base text-[#263143] transition hover:bg-[#dee8ff] hover:text-[#111c2d]';
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
            ['/admin/logout', 'logout', 'Sign out'],
        ].map(([path, icon, label]) => `<a href="${path}"><span class="material-symbols-outlined">${icon}</span><span>${label}</span></a>`).join('');
        document.body.appendChild(mobileNav);
        mobileNav.querySelector('a[href="/admin/logout"]')?.addEventListener('click', (event) => {
            event.preventDefault();
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/logout';
            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = document.querySelector('form[action$="/admin/logout"] input[name="_token"]')?.value || document.querySelector('input[name="_token"]')?.value || '';
            form.appendChild(token);
            document.body.appendChild(form);
            form.submit();
        });

        const responsiveStyles = document.createElement('style');
        responsiveStyles.textContent = `
            .mobile-admin-nav { display:none; }
            @media (max-width: 1023px) {
                body { padding-bottom: 4.75rem !important; overflow-x: hidden; }
                .mobile-admin-nav { position:fixed; inset:auto 0 0; z-index:60; display:grid; grid-template-columns:repeat(6,minmax(0,1fr)); gap:.25rem; padding:.45rem .5rem calc(.45rem + env(safe-area-inset-bottom)); background:#0e1c2f; box-shadow:0 -2px 12px rgba(0,0,0,.18); }
                .mobile-admin-nav a { min-width:0; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:.15rem; min-height:3.2rem; border-radius:.5rem; color:#d8e3fb; font-size:.68rem; line-height:1; text-decoration:none; }
                .mobile-admin-nav a:hover, .mobile-admin-nav a:focus-visible { background:#1d3557; color:#fff; outline:none; }
                .mobile-admin-nav .material-symbols-outlined { font-size:1.3rem; }
                body > header { height:auto !important; min-height:4rem; }
                body > header > div { min-height:4rem; height:auto !important; padding:.5rem .75rem !important; }
                .standard-admin-logo { width:44px !important; height:44px !important; }
                .standard-admin-title { max-width:calc(100vw - 9rem); white-space:normal !important; font-size:clamp(.72rem,2.7vw,1rem) !important; line-height:1.15 !important; }
                body > header form, body > header > div > div:last-child > span { display:none !important; }
                aside { display:none !important; }
                main { margin-left:0 !important; padding-left:1rem !important; padding-right:1rem !important; }
            }
            @media (max-width: 480px) {
                .mobile-admin-nav a { font-size:.6rem; }
                .mobile-admin-nav .material-symbols-outlined { font-size:1.15rem; }
                main { padding-left:.75rem !important; padding-right:.75rem !important; }
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

            const documentText = await response.text();
            const nextDocument = new DOMParser().parseFromString(documentText, 'text/html');
            const currentMain = document.querySelector('main');
            const nextMain = nextDocument.querySelector('main');
            if (currentMain && nextMain) currentMain.replaceWith(nextMain);
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
