(() => {
    const init = () => {
        const sequence = document.querySelector('main .grid > section:first-child');
        if (!sequence || sequence.dataset.swapInitialized === 'true') return;
        sequence.dataset.swapInitialized = 'true';

        let draggedId = null;
        let selectedId = null;
        let pointerDrag = null;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
            || document.querySelector('input[name="_token"]')?.value
            || '';
        const getBox = (target) => target instanceof Element
            ? target.closest('.position-box[draggable="true"]')
            : target?.parentElement?.closest?.('.position-box[draggable="true"]');
        const clearSelection = () => {
            sequence.querySelectorAll('.position-box.ring-2').forEach((box) => box.classList.remove('ring-2', 'ring-[#115cb9]'));
            selectedId = null;
        };
        const swapBoxes = (sourceId, targetId) => {
            if (!sourceId || !targetId || sourceId === targetId) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/position-management/${sourceId}/reorder`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="target_position_id" value="${targetId}">
            `;
            document.body.appendChild(form);
            form.submit();
        };
        const selectOrSwap = async (event) => {
            if (draggedId) return;
            const box = getBox(event.target);
            if (!box || event.target.closest?.('button, a, form')) return;
            if (!selectedId) {
                selectedId = box.dataset.positionId;
                box.classList.add('ring-2', 'ring-[#115cb9]');
                return;
            }
            const sourceId = selectedId;
            const targetId = box.dataset.positionId;
            clearSelection();
            await swapBoxes(sourceId, targetId);
        };
        sequence.addEventListener('keydown', async (event) => {
            if (event.key !== 'Enter' && event.key !== ' ') return;
            event.preventDefault();
            await selectOrSwap(event);
        });
        sequence.addEventListener('pointerdown', (event) => {
            const box = getBox(event.target);
            if (!box || event.target.closest?.('button, a, form') || event.button !== 0) return;
            event.preventDefault();
            pointerDrag = { box, sourceId: box.dataset.positionId, pointerId: event.pointerId, startX: event.clientX, startY: event.clientY, dragging: false };
            box.setPointerCapture?.(event.pointerId);
        });
        sequence.addEventListener('pointermove', (event) => {
            if (!pointerDrag || pointerDrag.pointerId !== event.pointerId) return;
            if (!pointerDrag.dragging && Math.hypot(event.clientX - pointerDrag.startX, event.clientY - pointerDrag.startY) < 6) return;
            pointerDrag.dragging = true;
            pointerDrag.box.classList.add('opacity-50');
            sequence.querySelectorAll('.position-box.ring-2').forEach((box) => box.classList.remove('ring-2', 'ring-[#115cb9]'));
            getBox(document.elementFromPoint(event.clientX, event.clientY))?.classList.add('ring-2', 'ring-[#115cb9]');
        });
        sequence.addEventListener('pointerup', async (event) => {
            if (!pointerDrag || pointerDrag.pointerId !== event.pointerId) return;
            const currentDrag = pointerDrag;
            pointerDrag = null;
            currentDrag.box.classList.remove('opacity-50');
            const target = getBox(document.elementFromPoint(event.clientX, event.clientY));
            sequence.querySelectorAll('.position-box.ring-2').forEach((box) => box.classList.remove('ring-2', 'ring-[#115cb9]'));
            if (currentDrag.dragging) {
                clearSelection();
                await swapBoxes(currentDrag.sourceId, target?.dataset.positionId);
            } else {
                await selectOrSwap(event);
            }
        });
        sequence.addEventListener('pointercancel', () => {
            pointerDrag?.box.classList.remove('opacity-50');
            pointerDrag = null;
            clearSelection();
        });
        sequence.addEventListener('dragstart', (event) => {
            const box = getBox(event.target);
            if (!box) return;
            draggedId = box.dataset.positionId;
            event.dataTransfer?.setData('text/plain', draggedId);
            if (event.dataTransfer) event.dataTransfer.effectAllowed = 'move';
            box.classList.add('opacity-50');
        });
        sequence.addEventListener('dragend', (event) => {
            getBox(event.target)?.classList.remove('opacity-50');
            draggedId = null;
        });
        sequence.addEventListener('dragover', (event) => {
            const box = getBox(event.target);
            if (!box) return;
            event.preventDefault();
            box.classList.add('ring-2', 'ring-[#115cb9]');
        });
        sequence.addEventListener('dragleave', (event) => getBox(event.target)?.classList.remove('ring-2', 'ring-[#115cb9]'));
        sequence.addEventListener('drop', async (event) => {
            const box = getBox(event.target);
            if (!box) return;
            event.preventDefault();
            box.classList.remove('ring-2', 'ring-[#115cb9]');
            const sourceId = draggedId || event.dataTransfer?.getData('text/plain');
            clearSelection();
            await swapBoxes(sourceId, box.dataset.positionId);
        });
        sequence.querySelectorAll('.position-box[draggable="true"]').forEach((box) => box.classList.add('cursor-grab'));
    };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init, { once: true });
    else init();
    document.addEventListener('admin-main-refreshed', init);
})();
