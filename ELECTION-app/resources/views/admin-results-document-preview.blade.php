<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Results &amp; Document Preview · District 23 FYS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@600;700&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { surface: '#f9f9ff', 'surface-container-low': '#f0f3ff', 'surface-container-high': '#dee8ff', 'primary-container': '#0e1c2f', secondary: '#115cb9', 'tertiary-fixed': '#ffdf98' }, fontFamily: { display: ['Outfit'], sans: ['Inter'] } } } };</script>
<style>
    main > div > section:nth-of-type(4) { display: none !important; }
    body > [data-live-results-document] { margin-inline: auto; }
    @media (min-width: 1024px) {
        body > [data-live-results-document] {
            margin-left: calc(16rem + max((100vw - 16rem - 64rem) / 2, 1rem));
            margin-right: max((100vw - 16rem - 64rem) / 2, 1rem);
        }
    }
    @media print {
        *, *::before, *::after {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body > header,
        body > aside,
        body > main { display: none !important; }
        [data-live-results-document] { display: block !important; }
        [data-live-results-document] table,
        [data-live-results-document] th,
        [data-live-results-document] td {
            border-color: #64748b !important;
        }
        [data-live-results-document] th {
            background-color: #e2e8f0 !important;
        }
    }
</style>
</head>
<body class="min-h-screen bg-surface font-sans text-[#111c2d] antialiased">
@include('admin.partials.header')
<aside class="fixed bottom-0 left-0 top-16 hidden w-64 bg-surface-container-low py-6 lg:flex lg:flex-col"><div class="px-6 pb-4 text-[11px] font-bold uppercase tracking-wider text-[#44474c]">Navigation</div><nav class="flex flex-col gap-1 px-2"><a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 rounded px-4 py-3 text-base text-[#263143]"><span class="material-symbols-outlined">dashboard</span>Dashboard</a><a href="{{ route('admin.voter-management') }}" class="flex items-center gap-2 rounded px-4 py-3 text-base text-[#263143]"><span class="material-symbols-outlined">badge</span>Voter Management</a><a href="{{ route('admin.position-management') }}" class="flex items-center gap-2 rounded px-4 py-3 text-base text-[#263143]"><span class="material-symbols-outlined">work</span>Position Management</a><a href="{{ route('admin.results-document-preview') }}" class="flex items-center gap-2 rounded bg-surface-container-high px-4 py-3 text-base font-bold text-secondary"><span class="material-symbols-outlined">description</span>Results &amp; Document Preview</a></nav></aside>
<main class="min-h-screen px-5 pb-12 pt-24 lg:ml-64"><div class="mx-auto max-w-7xl space-y-6">
    <section class="rounded-xl bg-white p-6 shadow-sm"><p class="text-[11px] font-bold uppercase tracking-wider text-secondary">Administrative Control Room</p><h1 class="mt-1 font-display text-4xl font-bold">Results &amp; Document Preview</h1></section>
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-3"><div class="rounded-xl bg-white p-5 shadow-sm"><span class="text-[11px] font-bold uppercase tracking-wider text-[#44474c]">Created positions</span><strong class="mt-2 block font-display text-3xl">{{ $positions->count() }}</strong></div><div class="rounded-xl bg-white p-5 shadow-sm"><span class="text-[11px] font-bold uppercase tracking-wider text-[#44474c]">Ballots cast</span><strong class="mt-2 block font-display text-3xl">{{ $ballotsCast }}</strong></div><div class="rounded-xl bg-white p-5 shadow-sm"><span class="text-[11px] font-bold uppercase tracking-wider text-[#44474c]">Participation</span><strong class="mt-2 block font-display text-3xl text-secondary">{{ $participationRate }}%</strong></div></section>
<section class="rounded-xl bg-white p-6 shadow-sm"><div class="flex items-center justify-between gap-3"><div><p class="text-[11px] font-bold uppercase tracking-wider text-secondary">Live result</p><h2 class="mt-1 font-display text-2xl font-semibold">Results by ballot position</h2></div><span class="inline-flex items-center gap-2 rounded-full bg-surface-container-high px-3 py-2 text-xs font-bold text-secondary"><span class="h-2 w-2 animate-pulse rounded-full bg-emerald-500"></span>Realtime</span></div><div class="mt-5 space-y-4">@forelse ($positions as $result)<article class="rounded-xl bg-surface-container-low p-5"><div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center\"><div><h3 class="font-display text-2xl font-bold">{{ strtoupper($result['position']->name) }}</h3><p class="text-sm text-[#44474c]">{{ $result['position']->seats }} seat{{ $result['position']->seats === 1 ? '' : 's' }} · {{ $result['ballots_cast'] }} ballot{{ $result['ballots_cast'] === 1 ? '' : 's' }} cast</p></div><span class="rounded-full px-3 py-2 text-xs font-bold {{ $result['is_final'] ? 'bg-emerald-100 text-emerald-700' : 'bg-white text-[#44474c]' }}">{{ $result['is_final'] ? 'FINAL RESULT' : 'LIVE RESULT' }}</span></div><div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-{{ min($result['position']->seats, 3) }}">@forelse ($result['winners'] as $winner)<div class="rounded-lg bg-white p-4"><p class="text-[11px] font-bold uppercase tracking-wider text-secondary">{{ $loop->iteration === 1 ? 'Current winner' : 'Elected seat '.$loop->iteration }}</p><strong class="winner-name mt-1 block cursor-text rounded font-display text-xl outline-none hover:bg-yellow-100 focus:bg-yellow-100" contenteditable="true" spellcheck="false" data-winner-id="{{ $winner->id }}" data-update-url="{{ route('admin.results-document-preview.winner.update', $winner) }}">{{ $winner->display_candidate_name }}</strong><span class="text-sm text-[#44474c]">{{ $winner->votes_count }} vote{{ $winner->votes_count === 1 ? '' : 's' }}</span></div>@empty<div class="rounded-lg bg-white p-4 text-sm text-[#44474c]">No winner recorded yet. Results will appear automatically after votes are submitted.</div>@endforelse</div><div class="mt-4 border-t border-white pt-3">@forelse ($result['candidates'] as $candidate)<div class="flex items-center justify-between border-b border-white py-2 text-sm last:border-0"><span>{{ $candidate->display_candidate_name }}</span><strong class="text-secondary">{{ $candidate->votes_count }}</strong></div>@empty<p class="text-sm text-[#44474c]">No candidates registered for this position.</p>@endforelse</div></article>@empty<div class="rounded-xl bg-surface-container-low p-6 text-sm text-[#44474c]">No positions have been created yet.</div>@endforelse</div></section>
    <section class="rounded-xl bg-white p-6 shadow-sm"><div class="flex flex-col gap-2 border-b border-slate-200 pb-5 text-center"><img src="{{ asset('images/uecfi-logo.png') }}" alt="District 23 FYS Commission emblem" class="mx-auto h-20 w-20 rounded-lg object-contain"><p class="text-[11px] font-bold uppercase tracking-widest text-[#44474c]">Office of the Electoral Board</p><h2 class="font-display text-2xl font-bold">DISTRICT 23 FYS — OFFICIAL ELECTION RESULTS</h2><p class="text-sm font-semibold uppercase text-secondary">Certified roster of elected officers · 2027–2030</p></div><div class="mt-6 overflow-x-auto"><table class="w-full text-left text-sm"><thead><tr class="border-b-2 border-slate-200 text-xs uppercase tracking-wider"><th class="p-3">Position</th><th class="p-3">Winner / elected officer</th><th class="p-3 text-right">Status</th></tr></thead><tbody>@forelse ($positions as $result) @forelse ($result['winners'] as $winner)<tr class="border-b border-slate-100"><td class="p-3 font-bold">{{ $result['position']->name }} · Seat {{ $loop->iteration }}</td><td class="p-3 font-semibold">{{ $winner->candidate_name }}</td><td class="p-3 text-right font-bold text-secondary">{{ $result['is_final'] ? 'ELECTED' : 'LEADING' }}</td></tr>@empty<tr class="border-b border-slate-100"><td class="p-3 font-bold">{{ $result['position']->name }}</td><td class="p-3 text-[#44474c]">Pending result</td><td class="p-3 text-right text-[#44474c]">PENDING</td></tr>@endforelse @empty<tr><td class="p-3" colspan="3">No positions have been created yet.</td></tr>@endforelse</tbody></table></div><p class="mt-5 text-center text-xs text-[#44474c]">This document is generated from the current ballot sequence and updates with each recorded vote or abstention.</p></section>
    <section class="rounded-xl bg-white p-6 shadow-sm"><p class="text-[11px] font-bold uppercase tracking-wider text-secondary">Neutral ballot outcomes</p><h2 class="mt-1 font-display text-2xl font-semibold">Abstentions by position</h2><div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">@forelse ($positions as $result)<div class="rounded-lg bg-surface-container-low p-4"><p class="font-bold">{{ $result['position']->name }}</p><p class="mt-1 text-sm text-[#44474c]"><strong class="text-xl text-secondary">{{ $result['abstentions'] }}</strong> abstention{{ $result['abstentions'] === 1 ? '' : 's' }}</p></div>@empty<p class="text-sm text-[#44474c]">No positions created yet.</p>@endforelse</div></section>
</div></main>
<script src="{{ asset('js/admin-navigation-cleanup.js') }}?v={{ filemtime(public_path('js/admin-navigation-cleanup.js')) }}"></script>
<script>
    window.resultAbstentions = @json($positions->pluck('abstentions')->values());
    window.updateResultLabels = () => {
        document.querySelectorAll('span').forEach((element) => {
            if (element.textContent.trim() === 'FINAL RESULT') element.remove();
        });
        document.querySelectorAll('main section').forEach((section) => {
            if (section.querySelector('h2')?.textContent.trim() === 'Abstentions by position') section.remove();
        });
        document.querySelectorAll('main article').forEach((resultCard, index) => {
            resultCard.querySelectorAll('strong').forEach((element) => {
                element.textContent = element.textContent.replace(/\s*\(\d+%\)/g, '');
            });
            const abstentionCount = Number(window.resultAbstentions?.[index] || 0);
            let abstainRow = resultCard.querySelector('[data-abstain-result]');
            if (!abstainRow) {
                abstainRow = document.createElement('div');
                abstainRow.dataset.abstainResult = 'true';
                abstainRow.className = 'mt-2 flex items-center justify-between border-t border-white pt-2 text-sm';
                resultCard.appendChild(abstainRow);
            }
            abstainRow.innerHTML = '<span class="font-semibold">ABSTAIN</span><strong class="text-secondary">' + abstentionCount + '</strong>';
        });
        document.querySelectorAll('article p').forEach((label) => {
            if (label.textContent.trim() !== 'Current winner') return;
            const resultCard = label.closest('article');
            if (resultCard?.textContent.includes('FINAL RESULT')) label.textContent = 'Winner';
        });
    };
    window.updateResultLabels();
</script>
<section data-live-results-document class="mx-auto mb-12 w-[calc(100%-2rem)] max-w-5xl bg-white p-10 text-black shadow-sm print:shadow-none">
    @if (session('winner_name_updated'))
        <div class="mb-4 rounded-lg bg-emerald-100 px-4 py-3 text-sm font-semibold text-emerald-800 print:hidden">{{ session('winner_name_updated') }}</div>
    @endif
    <div class="mb-4 flex justify-end print:hidden">
        <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded bg-[#115cb9] px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-[#0e1c2f]">
            <span class="material-symbols-outlined text-lg">print</span>
            PRINT DOCUMENT
        </button>
    </div>
    <div class="text-center">
        <div class="flex items-center justify-center gap-4">
            <img src="{{ asset('images/uecfi-logo.png') }}" alt="UECFI logo" class="h-20 w-20 object-contain">
            <div>
                <h2 class="text-xl font-bold uppercase">Union Espiritista Cristiana de Filipinas, Inc.</h2>
                <p class="text-sm">Founded February 19, 1905, SEC. Registered January 23, 1909</p>
                <p class="text-sm">SEC Registry No. 15147</p>
                <p class="text-sm">District 23</p>
            </div>
        </div>
        <h1 class="mt-6 text-xl font-bold uppercase">Result of District 23 Federation of Youth Spiritist Officers</h1>
        <h2 class="text-xl font-bold">FOR 2027–2030</h2>
    </div>
    <table class="mt-8 w-full border-collapse border border-slate-500 text-sm">
        <tbody>
        @forelse ($positions as $result)
            <tr>
                <th class="w-1/3 border border-slate-500 bg-slate-200 p-2 text-left align-top text-base font-bold">{{ $result['position']->name }}</th>
                <td class="border border-slate-500 p-2 align-top">
                    <div class="grid grid-cols-1 gap-x-8 gap-y-1 sm:grid-cols-2">
                        @for ($seat = 0; $seat < $result['position']->seats; $seat++)
                            <div class="flex min-h-6 items-start gap-1">
                                <span class="shrink-0 font-normal">{{ $result['position']->seats > 1 ? ($seat + 1).'.' : '' }}</span>
                                @if ($result['winners']->get($seat))
                                    <span class="winner-name cursor-text rounded px-1 outline-none hover:bg-yellow-100 focus:bg-yellow-100" contenteditable="true" spellcheck="false" data-winner-id="{{ $result['winners']->get($seat)->id }}" data-update-url="{{ route('admin.results-document-preview.winner.update', $result['winners']->get($seat)) }}">{{ $result['winners']->get($seat)->display_candidate_name }}</span>
                                @else
                                    <span class="winner-name winner-name-empty inline-block min-h-6 min-w-full cursor-text rounded px-1 outline-none hover:bg-yellow-100 focus:bg-yellow-100 print:hidden" contenteditable="true" spellcheck="false" title="Click to add winner" aria-label="Add winner" data-position-id="{{ $result['position']->id }}" data-seat="{{ $seat + 1 }}" data-add-url="{{ route('admin.results-document-preview.winner.add', $result['position']) }}"></span>
                                @endif
                            </div>
                        @endfor
                    </div>
                </td>
            </tr>
        @empty
            <tr><td class="border border-slate-500 p-3" colspan="2">No positions have been created.</td></tr>
        @endforelse
        <tr class="new-position-row hidden print:hidden">
            <th class="border border-slate-500 bg-slate-200 p-2 text-left align-top text-base font-bold"><span class="new-position-name inline-block min-h-6 min-w-full cursor-text rounded px-1 text-slate-400 outline-none hover:bg-yellow-100 focus:bg-yellow-100" contenteditable="true" spellcheck="false" title="Click to add position">Add position</span></th>
            <td class="border border-slate-500 p-2 align-top"><span class="new-position-winner inline-block min-h-6 min-w-full cursor-text rounded px-1 text-slate-400 outline-none hover:bg-yellow-100 focus:bg-yellow-100" contenteditable="true" spellcheck="false" title="Click to add winner">Add winner</span></td>
        </tr>
        </tbody>
    </table>
    <div class="group relative flex h-8 items-center justify-center print:hidden">
        <button type="button" class="flex h-7 w-7 items-center justify-center rounded-full bg-[#115cb9] text-xl font-bold leading-none text-white opacity-0 shadow-sm transition-opacity group-hover:opacity-100" aria-label="Add position" title="Add position">+</button>
    </div>
    <div class="mt-12 grid grid-cols-1 gap-12 text-center sm:grid-cols-2">
        <div>
            <div class="mx-auto h-10 max-w-xs border-b border-slate-700"></div>
            <p class="mt-2 font-bold uppercase">HMNA. PAOLA CONCUBIERTA</p>
            <p class="text-sm uppercase">FYS District 23 President</p>
        </div>
        <div>
            <div class="mx-auto h-10 max-w-xs border-b border-slate-700"></div>
            <p class="mt-2 font-bold uppercase">HMNA. BIANCA VANESSA F. SALADA</p>
            <p class="text-sm uppercase">FYS District 23 Secretary</p>
        </div>
    </div>
</section>
<script>
    const syncWinnerName = (nameCell, name) => {
        if (!nameCell.dataset.winnerId) return;
        document.querySelectorAll('[data-winner-id="' + nameCell.dataset.winnerId + '"]').forEach((cell) => {
            if (cell !== nameCell) cell.textContent = name;
        });
    };

    const saveWinnerName = async (nameCell) => {
        const name = nameCell.textContent.trim();
        const originalName = nameCell.dataset.originalName ?? name;
        if (!nameCell.dataset.winnerId || name === originalName) {
            return;
        }

        try {
            const response = await fetch(nameCell.dataset.updateUrl || nameCell.dataset.addUrl, {
                method: nameCell.dataset.updateUrl ? 'PATCH' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ candidate_name: name }),
            });

            if (!response.ok) throw new Error('Winner name could not be saved.');

            const saved = await response.json();
            nameCell.dataset.originalName = saved.candidate_name;
            if (saved.candidate_id) {
                nameCell.dataset.winnerId = saved.candidate_id;
                nameCell.dataset.updateUrl = '{{ url('/admin/results-document-preview/winner') }}/' + saved.candidate_id;
                nameCell.classList.remove('winner-name-empty', 'text-slate-400');
                nameCell.removeAttribute('data-add-url');
            }
            syncWinnerName(nameCell, saved.candidate_name);
        } catch {
            nameCell.classList.add('bg-red-100');
            setTimeout(() => nameCell.classList.remove('bg-red-100'), 1500);
        }
    };

    document.querySelectorAll('.winner-name').forEach((nameCell) => {
        if (nameCell.closest('section')?.querySelector('p')?.textContent.trim() === 'Live result') {
            nameCell.removeAttribute('contenteditable');
            nameCell.classList.remove('cursor-text', 'hover:bg-yellow-100', 'focus:bg-yellow-100');
            return;
        }
        nameCell.addEventListener('pointerdown', (event) => {
            window.isEditingWinner = true;
            if (document.activeElement !== nameCell) {
                event.preventDefault();
                nameCell.focus();
                const selection = window.getSelection();
                const range = document.createRange();
                range.selectNodeContents(nameCell);
                selection.removeAllRanges();
                selection.addRange(range);
            }
        });
        nameCell.addEventListener('focus', () => {
            window.isEditingWinner = true;
            nameCell.dataset.originalName = nameCell.textContent.trim();
        });
        nameCell.addEventListener('input', () => {
            syncWinnerName(nameCell, nameCell.textContent.trim());
        });
        nameCell.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                saveWinnerName(nameCell);
            }
        });
        nameCell.addEventListener('blur', () => {
            saveWinnerName(nameCell).finally(() => { window.isEditingWinner = false; });
        });
    });

    const newPositionRow = document.querySelector('.new-position-row');
    const newPositionName = newPositionRow?.querySelector('.new-position-name');
    const newPositionWinner = newPositionRow?.querySelector('.new-position-winner');
    const addPositionButton = document.querySelector('[aria-label="Add position"]');
    let newPositionSaveStarted = false;

    addPositionButton?.addEventListener('click', () => {
        newPositionRow?.classList.remove('hidden');
        newPositionName?.focus();
    });

    const saveNewPosition = async () => {
        const positionName = newPositionName?.textContent.trim();
        const candidateName = newPositionWinner?.textContent.trim();
        if (!positionName || !candidateName || newPositionSaveStarted) return;
        newPositionSaveStarted = true;

        try {
            const response = await fetch('{{ route('admin.results-document-preview.position.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ position_name: positionName, candidate_name: candidateName }),
            });
            if (!response.ok) throw new Error('Position could not be saved.');

            newPositionRow.classList.remove('print:hidden');
            newPositionRow.classList.add('saved-position-row');
            newPositionName.textContent = positionName;
            newPositionWinner.textContent = candidateName;
            newPositionName.contentEditable = 'true';
            newPositionWinner.contentEditable = 'true';
            newPositionName.classList.remove('text-slate-400');
            newPositionWinner.classList.remove('text-slate-400');
        } catch {
            newPositionSaveStarted = false;
            newPositionRow.classList.add('bg-red-50');
            setTimeout(() => newPositionRow.classList.remove('bg-red-50'), 1500);
        }
    };

    [newPositionName, newPositionWinner].filter(Boolean).forEach((cell) => {
        cell.addEventListener('focus', () => {
            if (cell.textContent.trim() === (cell === newPositionName ? 'Add position' : 'Add winner')) cell.textContent = '';
        });
        cell.addEventListener('blur', saveNewPosition);
        cell.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                cell.blur();
            }
        });
    });
</script>
</body>
</html>
