(() => {
    // Use delegated clicks so the launcher still works if the page shell or
    // dialog is inserted after this script is evaluated.
    document.addEventListener('click', event => {
        const openButton = event.target.closest('[data-tutorial-open]');
        if (!openButton) return;

        const dialog = document.getElementById('tutorial-dialog');
        const video = dialog?.querySelector('[data-tutorial-video]');
        if (!dialog || !video) return;

        if (!video.getAttribute('src')) video.src = video.dataset.src;
        if (!dialog.open) dialog.showModal();

        if (!dialog.dataset.closeBound) {
            dialog.dataset.closeBound = 'true';
            dialog.querySelector('[data-tutorial-close]')?.addEventListener('click', () => dialog.close());
            dialog.addEventListener('close', () => {
                video.pause();
                openButton.focus();
            });
        }
    });
})();
