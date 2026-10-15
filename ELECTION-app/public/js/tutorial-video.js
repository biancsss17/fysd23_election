(() => {
    const initializeTutorial = () => {
        const dialog = document.getElementById('tutorial-dialog');
        const openButton = document.querySelector('[data-tutorial-open]');
        if (!dialog || !openButton || dialog.dataset.initialized) return;

        dialog.dataset.initialized = 'true';
        const video = dialog.querySelector('[data-tutorial-video]');
        openButton.addEventListener('click', () => {
            if (!video.getAttribute('src')) video.src = video.dataset.src;
            dialog.showModal();
        });
        dialog.querySelector('[data-tutorial-close]').addEventListener('click', () => dialog.close());
        dialog.addEventListener('close', () => {
            video.pause();
            openButton.focus();
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeTutorial, { once: true });
    } else {
        initializeTutorial();
    }
})();
