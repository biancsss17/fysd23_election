<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Election Positions | District 23 FYS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>#position-form fieldset { display: none !important; }</style>
</head>
<body class="min-h-screen bg-[#f9f9ff] font-[Inter] text-[#111c2d] antialiased">
    @include('admin.partials.header')
    <aside class="fixed bottom-0 left-0 top-16 z-40 hidden w-64 bg-[#f0f3ff] py-6 lg:flex lg:flex-col"><div class="px-6 pb-4 text-[11px] font-bold uppercase tracking-wider text-[#44474c]">Navigation</div><nav class="flex flex-col gap-1 px-2"><a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 rounded px-4 py-2.5 text-sm text-[#44474c] transition hover:bg-[#dee8ff]"><span class="material-symbols-outlined text-xl">dashboard</span>Dashboard</a><a href="{{ route('admin.voter-management') }}" class="flex items-center gap-2 rounded px-4 py-2.5 text-sm text-[#44474c] transition hover:bg-[#dee8ff]"><span class="material-symbols-outlined text-xl">badge</span>Voter Management</a><a aria-current="page" href="{{ route('admin.position-management') }}" class="flex items-center gap-2 rounded border-l-4 border-[#ffdf98] bg-[#dee8ff] px-4 py-2.5 pl-5 text-sm font-bold text-[#115cb9]"><span class="material-symbols-outlined text-xl">work</span>Position Management</a><a href="{{ route('admin.results-document-preview') }}" class="flex items-center gap-2 rounded px-4 py-2.5 text-sm text-[#44474c] transition hover:bg-[#dee8ff]"><span class="material-symbols-outlined text-xl">description</span>Results &amp; Document Preview</a><a href="{{ route('admin.audit-log') }}" class="flex items-center gap-2 rounded px-4 py-2.5 text-sm text-[#44474c] transition hover:bg-[#dee8ff]"><span class="material-symbols-outlined text-xl">history_edu</span>Audit Log</a></nav></aside>
    <main class="min-h-screen bg-[#f9f9ff] px-4 pb-10 pt-24 sm:px-6 lg:ml-64"><div class="mx-auto flex w-full max-w-6xl flex-col gap-6">
        <section class="rounded-xl bg-white p-5 shadow-sm sm:p-6"><div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start"><div><span class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-[#115cb9]"><span class="material-symbols-outlined text-lg">format_list_numbered</span>Order of Proceedings</span><h1 class="mt-2 font-[Outfit] text-3xl font-bold sm:text-4xl">ELECTION POSITIONS</h1><p class="mt-2 text-sm text-[#44474c]">Sequential election schedule. Positions open one at a time in order.</p></div></div></section>
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.35fr_1fr]">
            <section class="rounded-xl bg-white p-5 shadow-sm sm:p-6"><div class="mb-5"><div><h2 class="font-[Outfit] text-2xl font-bold">Ballot Sequence</h2><p class="mt-1 text-xs text-[#44474c]">Drag a position box onto another box, or click two boxes to swap their order.</p></div></div><div class="space-y-2">
                @forelse ($positions as $index => $position)
                    @php($isCurrent = ! $position->is_completed && ! $position->is_closed && ! $positions->slice(0, $index)->contains(fn ($item): bool => ! $item->is_completed && ! $item->is_closed))
                    <div class="position-box touch-none flex items-center gap-3 rounded-lg border p-3 {{ $isCurrent ? 'border-[#659dfe] bg-[#e7eeff]' : 'border-[#eef1fa] bg-[#f9f9ff]' }}" data-position-id="{{ $position->id }}" draggable="true" role="button" tabindex="0" aria-label="Swap {{ $position->name }} position"><span class="w-8 text-xs font-bold text-[#75777d]">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span><div class="min-w-0 flex-1"><div class="flex flex-wrap items-center gap-2"><h3 class="font-[Outfit] text-sm font-bold uppercase sm:text-base">{{ $position->name }}</h3>@if ($isCurrent)<span class="rounded-full bg-[#115cb9] px-2 py-0.5 text-[10px] font-bold text-white">CURRENT</span>@endif</div><p class="mt-0.5 text-xs text-[#44474c]">{{ $position->seats }} seat{{ $position->seats === 1 ? '' : 's' }} • {{ $position->rule === 'multi' ? 'Multi-seat' : 'Single seat' }} • {{ $position->max_selections }} maximum selection{{ $position->max_selections === 1 ? '' : 's' }}</p></div><span class="flex items-center gap-1 text-[10px] font-bold {{ $position->is_completed ? 'text-[#115cb9]' : ($isCurrent ? 'text-[#115cb9]' : 'text-[#75777d]') }}"><span class="material-symbols-outlined text-base">{{ $position->is_completed ? 'check_circle' : ($isCurrent ? 'arrow_forward' : 'lock') }}</span><span class="hidden sm:inline">{{ $position->is_completed ? 'COMPLETED' : ($isCurrent ? 'CURRENT' : 'LOCKED') }}</span></span>@if (! $position->is_completed)<form method="POST" action="{{ route('admin.position-management.destroy', $position) }}" onsubmit="return confirm('Erase this position and all of its candidates, votes, and abstentions?');">@csrf @method('DELETE')<button class="flex h-8 w-8 items-center justify-center rounded text-[#ba1a1a] transition hover:bg-red-100" type="submit" aria-label="Erase {{ $position->name }}"><span class="material-symbols-outlined text-lg">close</span></button></form>@endif</div>
                @empty
                    <div class="rounded-lg border border-dashed border-[#c5c6cd] bg-[#f9f9ff] p-8 text-center"><span class="material-symbols-outlined text-4xl text-[#75777d]">format_list_numbered</span><p class="mt-2 font-[Outfit] text-lg font-bold">No positions created</p><p class="mt-1 text-sm text-[#44474c]">The ballot sequence will appear here after an administrator creates the first position.</p></div>
                @endforelse
                @if (false) @foreach ([['01', 'PRESIDENT', '1 Seat allocated • Balloting closed', 'COMPLETED', 'check_circle', 'done'], ['02', 'VICE PRESIDENT', 'Next sequential office scheduled for election', 'CURRENT', 'arrow_forward', 'current'], ['03', 'SECRETARY', 'Pending completion of Vice President', 'LOCKED', 'lock', 'locked'], ['04', 'TREASURER', 'Awaiting turn in sequence', 'LOCKED', 'lock', 'locked'], ['05', 'AUDITOR', 'Awaiting turn in sequence', 'LOCKED', 'lock', 'locked'], ['06', 'BOARD OF DIRECTORS', 'Multiple positions • Awaiting turn', 'LOCKED', 'lock', 'locked'], ['07', 'INSPECTOR', 'Awaiting turn in sequence', 'LOCKED', 'lock', 'locked'], ['08', 'DIRECTOR OF MEDIUM', 'Final scheduled office', 'LOCKED', 'lock', 'locked']] as $position)
                    <div class="flex items-center gap-3 rounded-lg border p-3 {{ $position[5] === 'current' ? 'border-[#659dfe] bg-[#e7eeff]' : 'border-[#eef1fa] bg-[#f9f9ff]' }}"><span class="w-8 text-xs font-bold text-[#75777d]">{{ $position[0] }}</span><div class="min-w-0 flex-1"><div class="flex flex-wrap items-center gap-2"><h3 class="font-[Outfit] text-sm font-bold sm:text-base">{{ $position[1] }}</h3>@if ($position[5] === 'current')<span class="rounded-full bg-[#115cb9] px-2 py-0.5 text-[10px] font-bold text-white">CURRENT</span>@endif</div><p class="mt-0.5 text-xs text-[#44474c]">{{ $position[2] }}</p></div><span class="flex items-center gap-1 text-[10px] font-bold {{ $position[5] === 'done' ? 'text-[#115cb9]' : ($position[5] === 'current' ? 'text-[#115cb9]' : 'text-[#75777d]') }}"><span class="material-symbols-outlined text-base">{{ $position[4] }}</span><span class="hidden sm:inline">{{ $position[3] }}</span></span></div>
                @endforeach @endif
            </div></section>
            <section class="rounded-xl bg-white p-5 shadow-sm sm:p-6"><div class="mb-5"><div><h2 class="font-[Outfit] text-2xl font-bold">CREATE NEXT POSITION</h2><p class="mt-1 text-xs text-[#44474c]">Only the next position in the election sequence can be activated.</p></div></div><form id="position-form" class="space-y-5" onsubmit="createPosition(event)"><label class="block"><span class="mb-2 block text-xs font-bold">Position Name</span><div class="relative"><span class="material-symbols-outlined absolute left-3 top-2.5 text-lg text-[#75777d]">badge</span><input name="position_name" value="{{ old('position_name', $nextPositionName) }}" class="w-full rounded border border-[#c5c6cd] py-2.5 pl-10 pr-3 text-sm outline-none focus:border-[#115cb9] focus:ring-2 focus:ring-[#d8e3fb]" required></div><span class="mt-1 block text-[11px] text-[#75777d]">Pre-filled to match official District sequence order.</span></label><div><span class="mb-2 block text-xs font-bold">Number of Seats</span><div class="flex items-center gap-3"><button type="button" onclick="changeSeats(-1)" class="flex h-10 w-10 items-center justify-center rounded border border-[#c5c6cd] text-xl">−</button><input id="seat-count" name="seats" type="number" min="1" max="10" value="1" class="h-10 w-20 rounded border border-[#c5c6cd] text-center text-sm font-bold"><button type="button" onclick="changeSeats(1)" class="flex h-10 w-10 items-center justify-center rounded border border-[#c5c6cd] text-xl">+</button></div><span class="mt-1 block text-[11px] text-[#75777d]">Determines how many candidates will be elected.</span></div><fieldset><legend class="mb-2 text-xs font-bold">Election Rules</legend><div class="space-y-2"><label class="flex items-center gap-2 text-sm"><input type="radio" name="rule" value="single" checked onchange="toggleRule(false)" class="accent-[#115cb9]">Single seat</label><label class="flex items-center gap-2 text-sm"><input type="radio" name="rule" value="multi" onchange="toggleRule(true)" class="accent-[#115cb9]">Multi-seat</label></div></fieldset><label class="flex items-center gap-2 text-sm"><input id="allow-abstain" type="checkbox" checked class="h-4 w-4 accent-[#115cb9]">Allow Abstain <span class="text-xs text-[#75777d]">Permits voters to submit an unmarked ballot</span></label><label class="block"><span class="mb-2 block text-xs font-bold">Maximum selections <span class="font-normal text-[#75777d]">Per ballot</span></span><input id="max-selections" type="number" min="1" max="10" value="1" class="w-full rounded border border-[#c5c6cd] px-3 py-2.5 text-sm outline-none focus:border-[#115cb9]"></label><button type="submit" class="flex w-full items-center justify-center gap-2 rounded bg-[#0e1c2f] px-4 py-3 text-xs font-bold text-white transition hover:bg-[#115cb9]"><span class="material-symbols-outlined text-lg">how_to_vote</span>CREATE POSITION</button><p class="text-center text-[11px] text-[#75777d]">Opens voting preparations for the Committee.</p></form></section>
        </div>
    </div></main>
    <div id="notice" class="fixed bottom-6 right-6 z-50 hidden items-center gap-2 rounded-lg bg-[#0e1c2f] px-5 py-3 text-sm font-semibold text-white shadow-xl"><span class="material-symbols-outlined text-lg">check</span>Position successfully created and activated.<button type="button" onclick="hideNotice()" class="ml-3 text-[#d8e3fb]"><span class="material-symbols-outlined text-base">close</span></button></div>
    <script>
        document.querySelector('#position-form fieldset')?.remove();
        function syncPositionConfig() {
            const seats = document.getElementById('seat-count');
            const max = document.getElementById('max-selections');
            let seatCount = Math.min(10, Math.max(1, Number(seats.value) || 1));
            seats.value = seatCount;
            max.max = '5';
            max.value = Math.min(5, Math.max(1, Number(max.value) || 1));
        }
        function changeSeats(amount) {
            const input = document.getElementById('seat-count');
            input.value = Number(input.value || 1) + amount;
            document.getElementById('position-form')?.setAttribute('data-dirty', 'true');
            syncPositionConfig();
        }
        function createPosition(event) {
            event.preventDefault();
            const form = event.target;
            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = '{{ csrf_token() }}';
            form.appendChild(token);

            const abstain = document.getElementById('allow-abstain');
            abstain.name = 'allow_abstain';
            abstain.value = '1';

            const maxSelections = document.getElementById('max-selections');
            maxSelections.name = 'max_selections';
            form.action = '{{ route('admin.position-management.store') }}';
            form.method = 'POST';
            form.onsubmit = null;
            syncPositionConfig();
            form.submit();
        }
        function hideNotice() { const notice = document.getElementById('notice'); notice.classList.add('hidden'); notice.classList.remove('flex'); }
    </script>
<script>
    const runPositionPageReady = (callback) => {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback, { once: true });
        } else {
            callback();
        }
    };

    runPositionPageReady(() => {
        const positionForm = document.getElementById('position-form');
        positionForm?.addEventListener('input', () => positionForm.dataset.dirty = 'true');
        positionForm?.addEventListener('change', () => positionForm.dataset.dirty = 'true');
        const positionInput = document.querySelector('input[name="position_name"]');
        if (positionInput) positionInput.value = @json($nextPositionName);
        document.getElementById('seat-count')?.addEventListener('input', () => syncPositionConfig());
        document.getElementById('seat-count')?.addEventListener('change', () => syncPositionConfig());
        document.getElementById('max-selections')?.addEventListener('change', () => syncPositionConfig());
        syncPositionConfig();
    });

    if (false) runPositionPageReady(() => {
        let draggedId = null;
        let selectedId = null;
        const sequence = document.querySelector('main .grid > section:first-child');
        const swapBoxes = async (sourceId, targetId) => {
            if (!sourceId || !targetId || sourceId === targetId) return;
            const response = await fetch(`/admin/position-management/${sourceId}/reorder`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token()), Accept: 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify({ target_position_id: targetId }),
            });
            if (response.ok) window.location.reload();
        };
        if (!sequence) return;

        const getBox = (target) => target?.closest?.('.position-box[draggable="true"]');
        const clearSelection = () => {
            sequence.querySelectorAll('.position-box.ring-2').forEach((box) => {
                box.classList.remove('ring-2', 'ring-[#115cb9]');
            });
            selectedId = null;
        };

        // Delegate from the stable sequence container because the first page
        // initializer replaces its innerHTML with the current position boxes.
        const selectOrSwap = async (event) => {
            if (draggedId) return;
            const box = getBox(event.target);
            if (!box || event.target.closest('button, a, form')) return;
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

        sequence.addEventListener('pointerup', selectOrSwap);
        sequence.addEventListener('keydown', async (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                await selectOrSwap(event);
            }
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
            const box = getBox(event.target);
            draggedId = null;
            box?.classList.remove('opacity-50');
        });

        sequence.addEventListener('dragover', (event) => {
            const box = getBox(event.target);
            if (!box) return;
            event.preventDefault();
            box.classList.add('ring-2', 'ring-[#115cb9]');
        });

        sequence.addEventListener('dragleave', (event) => {
            const box = getBox(event.target);
            box?.classList.remove('ring-2', 'ring-[#115cb9]');
        });

        sequence.addEventListener('drop', async (event) => {
            const box = getBox(event.target);
            if (!box) return;
            event.preventDefault();
            box.classList.remove('ring-2', 'ring-[#115cb9]');
            const sourceId = draggedId || event.dataTransfer?.getData('text/plain');
            await swapBoxes(sourceId, box.dataset.positionId);
        });

        sequence.querySelectorAll('.position-box[draggable="true"]').forEach((box) => box.classList.add('cursor-grab'));
    });

    setInterval(() => {
        const active = document.activeElement;
        const editing = active && ['INPUT', 'TEXTAREA', 'SELECT'].includes(active.tagName);
        const modalOpen = document.querySelector('.fixed.inset-0.flex, dialog[open]');
        const positionFormEditing = document.getElementById('position-form')
            && (document.getElementById('position-form').contains(active)
                || document.getElementById('position-form').dataset.dirty === 'true');

        if (!document.hidden && !editing && !modalOpen && !positionFormEditing) {
            window.refreshLivePage?.();
        }
    }, 1000);
</script>
<script src="{{ asset('js/admin-position-swap.js') }}?v={{ filemtime(public_path('js/admin-position-swap.js')) }}"></script>
</body>
</html>








<script src="{{ asset('js/admin-navigation-cleanup.js') }}?v={{ filemtime(public_path('js/admin-navigation-cleanup.js')) }}"></script>
