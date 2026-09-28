<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0b192c">
    <title>Ballot Marking · District 23 FYS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: {
            colors: { surface: '#f9f9ff', 'surface-container-low': '#f0f3ff', 'surface-container-high': '#dee8ff', 'primary-container': '#0e1c2f', secondary: '#115cb9', 'secondary-fixed': '#d7e2ff', 'tertiary-fixed': '#ffdf98' },
            fontFamily: { display: ['Outfit'], sans: ['Inter'] }
        } } };
    </script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="min-h-screen bg-slate-50 font-sans text-[#111c2d] antialiased">
    <header class="fixed inset-x-0 top-0 z-40 bg-[#0b192c] pt-[env(safe-area-inset-top)] shadow-md">
        <div class="mx-auto flex h-20 max-w-screen-2xl items-center gap-3 px-4 sm:px-6 lg:px-10">
            <a href="{{ route('home') }}" aria-label="Go back" class="flex h-9 w-9 items-center justify-center rounded-lg text-white/80 transition hover:bg-white/10 hover:text-white"><span class="material-symbols-outlined text-[22px]">arrow_back</span></a>
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDGZh2fEK5Pz27UEvegSQdR1JYa9WxmsLRRnwjjeqGbmcXh6C8M_2RJhevg_VBpixQLM43KRbe2EGx1BC0-YdC242XOyU1PmIpDm6WYdwokR0ILZ5Nh6GCtFqrajCgQzVvdRkmc-1ihrGlenST-nSJFUmbja3mChrmTT3xWKkpC-e7jh-o_wXh_GX4xyF5emBhE9lErGyXE94MY3DQKe6GGYaY1s_BUZwfUvW58az4sgsDpAFkBc0ur1HE3C_HlKhHA" alt="Official Emblem" class="h-10 w-10 shrink-0 rounded-full bg-white/10 object-contain p-0.5">
            <div class="min-w-0"><p class="truncate text-[10px] font-bold uppercase tracking-wider text-[#ffde59]">District 23 FYS · 2027–2030</p><h1 class="truncate font-display text-[17px] font-bold leading-tight text-white">Ballot Marking</h1></div>
            <a class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white" href="{{ route('admin.login') }}" aria-label="Administrator sign in"><span class="material-symbols-outlined text-lg">person</span></a>
        </div>
    </header>

    @if (! $position || ! $position->is_unlocked || $position->is_closed)
        <div class="fixed inset-x-0 bottom-0 top-20 z-30 flex items-center justify-center bg-slate-900/45 px-5 backdrop-blur-sm" id="ballot-lock-overlay">
            <div class="w-full max-w-md rounded-2xl bg-white p-8 text-center shadow-2xl">
                <span class="material-symbols-outlined text-5xl text-[#115cb9]">{{ $position?->is_closed ? 'event_busy' : 'lock' }}</span>
                <h2 class="mt-4 font-display text-2xl font-bold">{{ $position?->is_closed ? 'Voting closed' : 'Ballot locked' }}</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $position?->is_closed ? 'Voting is closed. This ballot can no longer be submitted.' : 'Please wait for the administrators to unlock this position before voting.' }}</p>
                @if ($position?->is_closed)
                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-[#0b192c] transition hover:bg-slate-50">Back to overview</a>
                        <a href="{{ route('results') }}" class="inline-flex items-center justify-center rounded-xl bg-[#115cb9] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#0b4d9d]">Show results</a>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <main class="mx-auto min-h-screen max-w-screen-2xl px-4 pb-10 pt-28 sm:px-6 lg:px-10">
        <div class="mx-auto max-w-5xl">
            <section class="relative mb-4 overflow-hidden rounded-2xl bg-[#0b192c] p-5 text-white shadow-lg">
                <div class="mb-3 flex items-center justify-between gap-2"><span class="inline-flex items-center gap-1.5 rounded-full bg-[#ffde59] px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider text-[#0b192c]"><span class="h-1.5 w-1.5 rounded-full bg-[#0b192c]"></span>Current position</span><span class="rounded-full bg-white/10 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wider text-white/80">1 Seat Available</span></div>
                <h2 class="font-display text-3xl font-extrabold uppercase tracking-tight">President</h2>
                <div class="mt-3 flex flex-col gap-1 border-t border-white/10 pt-3"><p class="flex items-center gap-1.5 text-xs text-white/80"><span class="material-symbols-outlined text-base text-[#ffde59]">check_circle</span>Select 1 Candidate · Secret &amp; Certified Ballot</p><p class="text-[11px] text-white/60">Single-choice vote · Abstain option available below</p></div>
            </section>

            <form class="grid gap-3 lg:grid-cols-2" id="ballot-form">
                @if ($candidateSubmissions->isNotEmpty())
                    @foreach ($candidateSubmissions as $index => $candidate)
                        <label class="candidate-option group relative block cursor-pointer transition-all duration-200"><input class="peer sr-only" name="presidential_ballot" type="radio" value="candidate-{{ $candidate->id }}"><div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200/80 bg-white p-4 shadow-sm transition-all peer-checked:border-[#0b192c] peer-checked:bg-slate-50/60 peer-checked:ring-2 peer-checked:ring-[#0b192c]/10"><div class="flex min-w-0 items-center gap-3"><div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-slate-100 text-sm font-bold text-[#0b192c]">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div><span class="truncate font-display text-base font-bold text-[#0b192c]">{{ $candidate->candidate_name }}</span></div><div class="radio-indicator flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2 border-slate-300 transition-all"><div class="dot h-2 w-2 scale-0 rounded-full bg-white transition-transform duration-200"></div></div></div></label>
                    @endforeach
                @else
                    <p class="lg:col-span-2 rounded-xl border border-dashed border-slate-300 bg-white p-5 text-center text-sm text-slate-500">No candidates are registered for this position yet.</p>
                @endif

                <div class="lg:col-span-2"><div class="mb-2 px-1"><span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Neutral option</span></div><label class="candidate-option group relative block cursor-pointer transition-all duration-200"><input class="peer sr-only" name="presidential_ballot" type="radio" value="abstain"><div class="flex items-center justify-between gap-3 rounded-xl border border-dashed border-slate-300 bg-white p-4 shadow-sm transition-all peer-checked:border-[#0b192c] peer-checked:bg-slate-50/60"><div class="min-w-0 flex-1"><span class="font-display text-base font-bold uppercase text-[#0b192c]">Abstain</span><p class="mt-0.5 text-xs text-slate-500">I choose to abstain from voting for this position.</p></div><div class="radio-indicator flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2 border-slate-300 transition-all"><div class="dot h-2 w-2 scale-0 rounded-full bg-white transition-transform duration-200"></div></div></div></label></div>
            </form>

            <div class="sticky bottom-4 z-20 mt-6"><button class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#0b192c] px-6 py-4 text-base font-bold tracking-wide text-white shadow-xl transition-all hover:bg-[#132742] active:scale-[.99] disabled:cursor-not-allowed disabled:opacity-50" data-next-url="{{ route('vote-countdown') }}" disabled id="submit-ballot-btn" type="button">SUBMIT MY VOTE <span class="material-symbols-outlined text-xl">arrow_forward</span></button></div>
        </div>
    </main>
    <script>window.electionPosition = @json($position); window.candidateSubmissions = @json($candidateSubmissions); window.electionDataUrl = @json(route('election.data')); window.voteSubmitUrl = @json(route('vote.submit')); window.voteCsrfToken = @json(csrf_token()); window.homeUrl = @json(route('home')); window.resultsUrl = @json(route('results'));</script>
    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
</body>
</html>
