<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0e1c2f">
    <title>Administrative Dashboard · District 23 FYS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { surface: '#f9f9ff', 'surface-container': '#e7eeff', 'surface-container-low': '#f0f3ff', 'surface-container-high': '#dee8ff', 'surface-container-highest': '#d8e3fb', 'primary-container': '#0e1c2f', secondary: '#115cb9', 'tertiary-fixed': '#ffdf98', 'tertiary-fixed-dim': '#eec14b' }, fontFamily: { display: ['Outfit'], sans: ['Inter'] } } } };</script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body class="min-h-screen bg-surface font-sans text-[#111c2d] antialiased">
    @include('admin.partials.header')

<aside class="fixed bottom-0 left-0 top-16 z-40 hidden w-64 bg-surface-container-low py-6 lg:flex lg:flex-col"><div class="px-6 pb-4 text-[11px] font-bold uppercase tracking-wider text-[#44474c]">Navigation</div><nav class="flex flex-col gap-1 px-2"><a aria-current="page" class="flex items-center gap-2 rounded border-l-4 border-tertiary-fixed bg-surface-container-high px-4 py-2.5 pl-5 text-sm font-bold text-secondary" href="{{ route('admin.dashboard') }}"><span class="material-symbols-outlined text-xl">dashboard</span>Dashboard</a><a class="flex items-center gap-2 rounded px-4 py-2.5 text-sm text-[#44474c] transition hover:bg-surface-container-high hover:text-[#111c2d]" href="{{ route('admin.voter-management') }}"><span class="material-symbols-outlined text-xl">badge</span>Voter Management</a><a class="flex items-center gap-2 rounded px-4 py-2.5 text-sm text-[#44474c] transition hover:bg-surface-container-high hover:text-[#111c2d]" href="{{ route('admin.position-management') }}"><span class="material-symbols-outlined text-xl">work</span>Position Management</a><a class="flex items-center gap-2 rounded px-4 py-2.5 text-sm text-[#44474c] transition hover:bg-surface-container-high hover:text-[#111c2d]" href="{{ route('admin.results-document-preview') }}"><span class="material-symbols-outlined text-xl">description</span>Results &amp; Document Preview</a><a class="flex items-center gap-2 rounded px-4 py-2.5 text-sm text-[#44474c] transition hover:bg-surface-container-high hover:text-[#111c2d]" href="{{ route('admin.audit-log') }}"><span class="material-symbols-outlined text-xl">history_edu</span>Audit Log</a></nav></aside>

    <main class="min-h-screen bg-surface px-5 pb-10 pt-24 lg:pl-72 lg:pr-8"><div class="mx-auto flex w-full max-w-[1240px] flex-col gap-6">
        @if (session('election_reset'))
            <div class="rounded-xl bg-emerald-100 px-5 py-4 text-sm font-semibold text-emerald-800">{{ session('election_reset') }}</div>
        @endif
        @if (session('election_restored'))
            <div class="rounded-xl bg-emerald-100 px-5 py-4 text-sm font-semibold text-emerald-800">{{ session('election_restored') }}</div>
        @endif
        @if (session('position_success'))
            <div class="rounded-xl bg-emerald-100 px-5 py-4 text-sm font-semibold text-emerald-800">{{ session('position_success') }}</div>
        @endif
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end"><div><h1 class="font-display text-2xl font-semibold">District 23 FYS Election Overview</h1></div><span class="inline-flex items-center gap-2 self-start rounded-full bg-white px-4 py-2 text-sm font-semibold shadow-sm md:self-auto"><span class="h-2.5 w-2.5 animate-pulse rounded-full bg-secondary"></span>Live Poll Counter</span></div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([['Eligible Voters', $registeredVoterCount, 'officers listed', 'how_to_reg', 'Total registered credential count', 'text-secondary'], ['Completed Votes', $ballotsCast, 'ballots recorded', 'check_circle', 'Successfully received & confirmed', 'text-secondary'], ['Remaining', max(0, $registeredVoterCount - $ballotsCast), 'yet to cast', 'pending_actions', 'Awaiting officer participation', 'text-[#44474c]'], ['Participation', $participationRate . '%', 'turnout', 'pie_chart', 'Live turnout from recorded votes', 'text-[#5a4300]']] as $stat)
                <section class="flex flex-col justify-between rounded-xl bg-white p-6 shadow-sm transition hover:shadow-md"><div class="mb-3 flex items-center justify-between"><span class="text-[11px] font-bold uppercase tracking-wider text-[#44474c]">{{ $stat[0] }}</span><div class="flex h-9 w-9 items-center justify-center rounded-lg {{ $stat[0] === 'Participation' ? 'bg-tertiary-fixed' : 'bg-surface-container-high' }} {{ $stat[5] }}"><span class="material-symbols-outlined text-xl">{{ $stat[3] }}</span></div></div><div class="flex items-baseline gap-1"><span class="font-display text-4xl font-bold">{{ $stat[1] }}</span><span class="text-xs text-[#44474c]">{{ $stat[2] }}</span></div><div class="mt-4 rounded bg-surface-container-low px-2 py-1"><p class="text-[10px] text-[#44474c]">{{ $stat[4] }}</p></div></section>
            @endforeach
        </div>

        <section class="rounded-xl bg-white p-6 shadow-md sm:p-8"><div class="flex items-center justify-between gap-3"><div><p class="text-[11px] font-bold uppercase tracking-wider text-[#44474c]">Live result</p><h2 class="mt-1 font-display text-2xl font-semibold">Current winner</h2></div></div>@if ($winner)<div class="mt-5 flex items-center justify-between rounded-xl bg-surface-container-low p-5"><div><p class="text-xs font-bold uppercase tracking-wider text-secondary">Leading candidate</p><strong class="mt-1 block font-display text-2xl">{{ $winner->candidate_name }}</strong></div><span class="text-right"><strong class="block font-display text-3xl text-secondary">{{ $winner->votes_count }}</strong><span class="text-xs text-[#44474c]">votes</span></span></div>@else<div class="mt-5 rounded-xl bg-surface-container-low p-5 text-sm text-[#44474c]">No votes recorded yet. Results will appear here automatically.</div>@endif<div class="mt-4 space-y-2">@forelse ($candidates as $candidate)<div class="flex items-center justify-between border-b border-slate-100 py-2 text-sm last:border-0"><span class="font-semibold">{{ $candidate->candidate_name }}</span><span class="font-bold text-secondary">{{ $candidate->votes_count }}</span></div>@empty<p class="text-sm text-[#44474c]">No candidates registered.</p>@endforelse</div></section>
        <section class="rounded-xl bg-white p-6 shadow-md sm:p-8">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-[#44474c]">Administrative Operations</p>
                    <h2 class="mt-1 font-display text-2xl font-semibold">Ballot controls</h2>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="flex flex-col items-start rounded-lg bg-surface-container-low p-5">
                    <span class="mb-2 flex h-10 w-10 items-center justify-center rounded bg-white text-secondary shadow-sm"><span class="material-symbols-outlined">how_to_vote</span></span>
                    <span class="font-bold">CANDIDACY</span>
                    <span class="text-sm text-[#44474c]">Allow self-declarations for {{ $position?->name ?? 'the current position' }}</span>
                    @if ($position)
                        <form class="mt-4 w-full" method="POST" action="{{ route('admin.position-management.candidacy', $position) }}">
                            @csrf @method('PATCH')
                            <button class="w-full rounded px-4 py-3 text-sm font-bold text-white transition {{ $position->candidacy_open ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-secondary hover:bg-primary-container' }}" type="submit">{{ $position->candidacy_open ? '✓ CANDIDACY OPEN' : 'START CANDIDACY' }}</button>
                        </form>
                    @else
                        <span class="mt-4 text-sm text-[#44474c]">No active position</span>
                    @endif
                </div>
                <div class="flex flex-col items-start rounded-lg bg-surface-container-low p-5">
                    <span class="mb-2 flex h-10 w-10 items-center justify-center rounded bg-white text-[#5a4300] shadow-sm"><span class="material-symbols-outlined">recommend</span></span>
                    <span class="font-bold">NOMINATION</span>
                    <span class="text-sm text-[#44474c]">Allow peer nominations for {{ $position?->name ?? 'the current position' }}</span>
                    @if ($position)
                        <form class="mt-4 w-full" method="POST" action="{{ route('admin.position-management.nomination', $position) }}">
                            @csrf @method('PATCH')
                            <button class="w-full rounded px-4 py-3 text-sm font-bold text-[#251a00] transition {{ $position->nomination_open ? 'bg-emerald-300 hover:bg-emerald-400' : 'bg-tertiary-fixed hover:bg-tertiary-fixed-dim' }}" type="submit">{{ $position->nomination_open ? '✓ NOMINATION OPEN' : 'START NOMINATION' }}</button>
                        </form>
                    @else
                        <span class="mt-4 text-sm text-[#44474c]">No active position</span>
                    @endif
                </div>
                <div class="flex flex-col items-start rounded-lg bg-surface-container-low p-5">
                    <span class="mb-2 flex h-10 w-10 items-center justify-center rounded bg-white text-secondary shadow-sm"><span class="material-symbols-outlined">lock_open</span></span>
                    <span class="font-bold">UNLOCK BALLOT</span>
                    <span class="text-sm text-[#44474c]">Allow voters to start</span>
                    @if ($position && ! $position->is_unlocked)
                        <form class="mt-4 w-full" method="POST" action="{{ route('admin.position-management.unlock', $position) }}">
                            @csrf @method('PATCH')
                            <button class="w-full rounded bg-violet-700 px-4 py-3 text-sm font-bold text-white transition hover:bg-violet-800" type="submit">UNLOCK {{ strtoupper($position->name) }}</button>
                        </form>
                    @elseif ($position)
                        <div class="mt-4 flex w-full flex-col gap-2">
                            <span class="rounded bg-emerald-100 px-4 py-3 text-center text-sm font-bold text-emerald-700">BALLOT OPEN</span>
                            <form method="POST" action="{{ route('admin.position-management.lock', $position) }}">
                                @csrf @method('PATCH')
                                <button class="w-full rounded bg-slate-700 px-4 py-3 text-sm font-bold text-white transition hover:bg-slate-900" type="submit"><span class="material-symbols-outlined mr-1 align-middle text-sm">lock</span>LOCK BALLOT</button>
                            </form>
                        </div>
                    @else
                        <span class="mt-4 text-sm text-[#44474c]">No active position</span>
                    @endif
                </div>
                @if ($position?->is_closed)
                    <form class="flex flex-col items-start rounded-lg bg-surface-container-low p-5" method="POST" action="{{ route('admin.position-management.reopen', $position) }}" onsubmit="return confirm('Reopen voting for this position? Voters will be allowed to vote again.');">
                        @csrf @method('PATCH')
                        <button class="flex w-full flex-col items-start" type="submit">
                            <span class="mb-2 flex h-10 w-10 items-center justify-center rounded bg-white text-violet-700 shadow-sm"><span class="material-symbols-outlined">lock_open</span></span>
                            <span class="font-bold">REOPEN VOTING</span>
                            <span class="text-sm text-[#44474c]">Allow voters to continue</span>
                        </button>
                    </form>
                @else
                    <form class="flex flex-col items-start rounded-lg bg-surface-container-low p-5" method="POST" action="{{ $position ? route('admin.position-management.close', $position) : '#' }}" onsubmit="return confirm('Close voting for this position? Voters will be blocked immediately.');">
                        @csrf @method('PATCH')
                        <button class="flex w-full flex-col items-start disabled:cursor-not-allowed disabled:opacity-50" type="submit" @disabled(! $position)>
                            <span class="mb-2 flex h-10 w-10 items-center justify-center rounded bg-white text-[#44474c] shadow-sm"><span class="material-symbols-outlined">block</span></span>
                            <span class="font-bold">CLOSE VOTING</span>
                            <span class="text-sm text-[#44474c]">Optional manual close</span>
                        </button>
                    </form>
                @endif
            </div>
        </section>
        @if ($position)
        <section class="rounded-xl bg-white p-6 shadow-md sm:p-8">
            <div class="flex items-center justify-between gap-3"><div><p class="text-[11px] font-bold uppercase tracking-wider text-secondary">Live result</p><h2 class="mt-1 font-display text-2xl font-semibold">Results by ballot position</h2></div><span class="inline-flex items-center gap-2 rounded-full bg-surface-container-high px-3 py-2 text-xs font-bold text-secondary"><span class="h-2 w-2 animate-pulse rounded-full bg-emerald-500"></span>Realtime</span></div>
            <article class="mt-5 rounded-xl bg-surface-container-low p-5"><div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center"><div><h3 class="font-display text-2xl font-bold">{{ strtoupper($position->name) }}</h3><p class="text-sm text-[#44474c]">{{ $position->seats }} seat{{ $position->seats === 1 ? '' : 's' }} · {{ $ballotsCast }} ballot{{ $ballotsCast === 1 ? '' : 's' }} cast</p></div><span class="rounded-full px-3 py-2 text-xs font-bold {{ $position->is_completed || $winner ? 'bg-emerald-100 text-emerald-700' : 'bg-white text-[#44474c]' }}">{{ $position->is_completed || $winner ? 'FINAL RESULT' : 'LIVE RESULT' }}</span></div><div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-{{ min($position->seats, 3) }}">@if ($winner)<div class="rounded-lg bg-white p-4"><p class="text-[11px] font-bold uppercase tracking-wider text-secondary">{{ $position->is_completed || $winner ? 'Winner' : 'Current winner' }}</p><strong class="mt-1 block font-display text-xl">{{ $winner->candidate_name }}</strong><span class="text-sm text-[#44474c]">{{ $winner->votes_count }} vote{{ $winner->votes_count === 1 ? '' : 's' }}</span></div>@else<div class="rounded-lg bg-white p-4 text-sm text-[#44474c]">No winner recorded yet. Results will appear automatically after votes are submitted.</div>@endif</div><div class="mt-4 border-t border-white pt-3">@forelse ($candidates as $candidate)<div class="flex items-center justify-between border-b border-white py-2 text-sm last:border-0"><span>{{ $candidate->candidate_name }}</span><strong class="text-secondary">{{ $candidate->votes_count }}</strong></div>@empty<p class="text-sm text-[#44474c]">No candidates registered for this position.</p>@endforelse<div class="flex items-center justify-between border-b border-white py-2 text-sm last:border-0"><span class="font-semibold">ABSTAIN</span><strong class="text-secondary">{{ $abstentions }}</strong></div></div></article>
        </section>
        @endif
        <section class="rounded-xl border border-red-200 bg-red-50 p-6 shadow-sm">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-red-700">Danger zone</p>
                    <h2 class="mt-1 font-display text-2xl font-semibold text-red-900">Reset election</h2>
                    <p class="mt-1 text-sm text-red-800">Deletes voters, positions, candidates, votes, abstentions, and results.</p>
                </div>
                <form method="POST" action="{{ route('admin.dashboard.reset-election') }}" onsubmit="return confirm('Reset the entire election? This permanently deletes all voters, positions, candidates, votes, abstentions, and results.');" class="flex flex-col gap-2 sm:min-w-80">
                    @csrf
                    <label class="text-xs font-bold uppercase tracking-wide text-red-800" for="archive-name">Name this data</label>
                    <input id="archive-name" name="archive_name" type="text" maxlength="255" value="" class="rounded border border-red-300 bg-white px-3 py-2 text-sm text-[#111c2d] outline-none focus:border-red-700 focus:ring-2 focus:ring-red-200" required>
                    <button type="submit" class="rounded bg-red-700 px-5 py-3 text-sm font-bold text-white transition hover:bg-red-900">RESET ELECTION</button>
                </form>
                <form method="POST" action="{{ route('admin.dashboard.purge-election') }}" onsubmit="return confirm('PERMANENTLY DELETE ALL election data and saved reset archives? The administrator account will be preserved.');" class="flex flex-col gap-2 sm:min-w-80">
                    @csrf
                    <span class="text-xs font-bold uppercase tracking-wide text-red-800">Permanent cleanup</span>
                    <span class="text-xs text-red-800">Keeps the administrator email and password.</span>
                    <button type="submit" class="rounded border border-red-700 bg-white px-5 py-3 text-sm font-bold text-red-800 transition hover:bg-red-100">DELETE ALL DATA · KEEP ADMIN</button>
                </form>
            </div>
        </section>
    </div></main>
<script>
    window.dashboardResultData = {
        abstentions: @json($abstentions),
        ballotsCast: @json($ballotsCast),
        isFinal: @json((bool) ($position?->is_completed || $winner)),
    };
    window.updateDashboardResult = () => {
        document.querySelectorAll('span').forEach((element) => {
            if (element.textContent.trim() === 'FINAL RESULT') element.remove();
        });
        const oldLiveResult = Array.from(document.querySelectorAll('main section')).find((section) => section.querySelector('h2')?.textContent.trim() === 'Current winner');
        oldLiveResult?.remove();
        document.querySelectorAll('main span').forEach((element) => {
            if (element.textContent.trim() === 'ABSTAIN') element.closest('div.flex.items-center.justify-between')?.remove();
        });
        const liveResult = Array.from(document.querySelectorAll('main section')).find((section) => section.querySelector('p')?.textContent.trim() === 'Live result');
        if (!liveResult) return;
        const heading = liveResult.querySelector('h2');
        if (heading) heading.textContent = window.dashboardResultData.isFinal ? 'Winner' : 'Current winner';
        const rows = liveResult.querySelector('.mt-4.space-y-2');
        if (!rows) return;
        rows.querySelectorAll('span').forEach((element) => {
            element.textContent = element.textContent.replace(/\s*\(\d+%\)/g, '');
        });
        let abstainRow = rows.querySelector('[data-dashboard-abstain]');
        if (!abstainRow) {
            abstainRow = document.createElement('div');
            abstainRow.dataset.dashboardAbstain = 'true';
            abstainRow.className = 'flex items-center justify-between border-b border-slate-100 py-2 text-sm last:border-0';
            rows.appendChild(abstainRow);
        }
        const count = Number(window.dashboardResultData.abstentions || 0);
        abstainRow.innerHTML = '<span class="font-semibold">ABSTAIN</span><span class="font-bold text-secondary">' + count + '</span>';
    };
    window.updateDashboardResult();
    setInterval(() => { const active = document.activeElement; const editing = active && ['INPUT', 'TEXTAREA', 'SELECT'].includes(active.tagName); const modalOpen = document.querySelector('.fixed.inset-0.flex, dialog[open]'); if (!document.hidden && !editing && !modalOpen) window.refreshLivePage?.(); }, 1000);
</script>
</body>
</html>












<script src="{{ asset('js/admin-navigation-cleanup.js') }}?v={{ filemtime(public_path('js/admin-navigation-cleanup.js')) }}"></script>
