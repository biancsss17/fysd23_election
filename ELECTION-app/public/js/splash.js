(() => {
    const splash = document.querySelector('.splash');
    if (!splash?.dataset.homeUrl) return;

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let leaving = false;

    const continueToHome = () => {
        if (leaving) return;
        leaving = true;
        window.dispatchEvent(new CustomEvent('electionSessionReady', {
            detail: { district: '23-FYS', term: '2027-2030' },
        }));
        splash.classList.add('is-leaving');
        window.setTimeout(() => window.location.replace(splash.dataset.homeUrl), reducedMotion.matches ? 0 : 350);
    };

    const timer = window.setTimeout(continueToHome, 5000);

    document.querySelector('.splash-skip')?.addEventListener('click', (event) => {
        if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        window.clearTimeout(timer);
        continueToHome();
    });
})();
