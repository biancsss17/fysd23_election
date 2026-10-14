(() => {
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
    dialog.addEventListener('click', event => {
        const bounds = dialog.getBoundingClientRect();
        if (event.target === dialog && (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom)) dialog.close();
    });
})();
