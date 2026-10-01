<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0b192c">
    <title>Review Your Vote · District 23 FYS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: {
            colors: { surface: '#f9f9ff', 'surface-container-low': '#f0f3ff', 'primary-container': '#0e1c2f', secondary: '#115cb9' },
            fontFamily: { display: ['Outfit'], sans: ['Inter'] }
        } } };
    </script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body class="min-h-screen bg-surface font-sans text-[#111c2d] antialiased">
    <header class="fixed inset-x-0 top-0 z-50 bg-[#0b192c] text-white shadow-md pt-[env(safe-area-inset-top)]">
        <div class="mx-auto flex h-20 max-w-screen-2xl items-center justify-between gap-3 px-4 sm:px-6 lg:px-10">
            <div class="flex min-w-0 items-center gap-1"><a href="{{ route('ballot') }}" aria-label="Go back" class="-ml-1 flex h-11 w-11 items-center justify-center rounded-lg text-white/90 transition hover:bg-white/10"><span class="material-symbols-outlined text-2xl">arrow_back</span></a><img alt="District 23 Seal" class="h-11 w-11 shrink-0 rounded-full bg-white/10 object-contain p-0.5" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBeEmd3IsLLFpL3lDRSJOI7PLVc1PN7E7w-6gH4GzJ2PcprcFiWXms13mEHJ-B2q8EVV1SMfcbzsuCO2IQf8k2x07kfYEGodKiHjwkW--h6eaigH2pssMo2hQKqIXVEJYK3QFBywDKs_uxE2RzemRBhLhN51D6PoTgzReAY_kX1iIMEsMUFcTg2VmZaojl1x_NBc4_3nGw5l08b-olwagiRUhQEq2G65qEXxlc6vP0O1ybp0yO3KbuLn0L6bYJWsf8L"><div class="min-w-0 pl-1"><h1 class="truncate font-display text-[17px] font-semibold leading-tight">Review Your Vote</h1><p class="truncate text-[11px] uppercase tracking-wider text-blue-200/80">District 23 FYS · 2027–2030</p></div></div><a href="{{ route('admin.login') }}" aria-label="Administrator sign in" class="flex h-9 w-9 items-center justify-center rounded-full border border-white/20 bg-white/10"><span class="material-symbols-outlined text-xl">person</span></a>
        </div>
    </header>

    <main class="mx-auto min-h-screen max-w-screen-2xl bg-surface px-4 pb-10 pt-24 sm:px-6 lg:px-10">
        <div class="mx-auto max-w-2xl">
            <section class="relative mt-2 overflow-hidden rounded-2xl bg-gradient-to-br from-[#0b192c] via-[#10233d] to-[#0b192c] p-5 text-white shadow-lg">
                <div class="absolute -bottom-8 -right-8 flex h-36 w-36 items-center justify-center rounded-full bg-white/5"><span class="material-symbols-outlined text-[90px] text-white/10">how_to_vote</span></div>
                <div class="relative z-10 flex items-center justify-between gap-2"><span class="inline-flex items-center gap-1.5 rounded-full border border-blue-400/30 bg-blue-500/20 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wider text-blue-200"><span class="material-symbols-outlined text-sm text-yellow-400">verified</span>Step 3 of 3 · Final step</span><span class="inline-flex items-center gap-1 rounded-full border border-emerald-500/30 bg-emerald-950/60 px-2.5 py-0.5 text-[11px] font-medium text-emerald-300"><span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>Active voter</span></div>
                <div class="relative z-10 mt-4"><span class="text-xs font-semibold uppercase tracking-wider text-blue-200/70">Ballot position</span><h2 class="mt-0.5 font-display text-[26px] font-bold tracking-tight">President</h2></div>
            </section>
            <div class="{{ $remainingSeconds > 0 ? '' : 'hidden' }} mt-4 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-center text-sm font-bold text-[#115cb9]" id="ballot-countdown-wrap">Voting window: <span id="ballot-countdown">{{ $remainingSeconds }}</span>s remaining. Review and verify your vote before time expires.</div>

            <section class="mt-4 flex flex-col gap-3 rounded-xl border border-slate-200/80 bg-white p-5 shadow-sm"><div class="flex items-center justify-between border-b border-slate-100 pb-3"><span class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-secondary"><span class="material-symbols-outlined text-lg">how_to_reg</span>Recorded balloting option</span><span class="rounded bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-400">{{ $reviewChoices->count() }} Selected</span></div><div class="flex items-center justify-between rounded-lg border border-blue-100 bg-blue-50/60 p-3.5"><div><span class="flex items-center gap-1 text-[11px] font-semibold uppercase tracking-wider text-blue-700"><span class="material-symbols-outlined text-sm">check_circle</span>Your nominee</span><strong class="mt-0.5 block font-display text-xl tracking-tight text-[#0b192c]">{{ $reviewChoices->isNotEmpty() ? $reviewChoices->join(', ') : 'No candidate selected' }}</strong></div><div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-600 text-white shadow-sm"><span class="material-symbols-outlined text-xl">done_all</span></div></div></section>

            <section class="mt-4 flex items-start gap-3 rounded-xl border border-amber-500/25 bg-amber-500/10 p-4"><div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-500/20 text-amber-800"><span class="material-symbols-outlined text-xl">lock</span></div><div><h2 class="font-bold tracking-tight text-amber-950">Irrevocable submission</h2><p class="mt-0.5 text-xs leading-relaxed text-amber-900/90">Once submitted, your vote for this position is cryptographically signed and cannot be amended or cast again.</p></div></section>

            @if (session('email_error'))
                <div class="auto-dismiss-5s mt-4 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-red-900" role="alert"><span class="material-symbols-outlined mt-0.5 text-xl">error</span><div><strong class="block text-sm">Email verification failed</strong><p class="mt-0.5 text-xs leading-relaxed">{{ session('email_error') }}</p></div></div>
            @endif
            @if (session('email_success'))
                <div class="mt-4 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900" role="status"><span class="material-symbols-outlined mt-0.5 text-xl">verified</span><div><strong class="block text-sm">Email verified</strong><p class="mt-0.5 text-xs leading-relaxed">{{ session('email_success') }}</p></div></div>
            @endif
            @if (! $votingOpen)
                <div class="mt-4 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-red-900" role="alert"><span class="material-symbols-outlined mt-0.5 text-xl">lock</span><div><strong class="block text-sm">Voting is currently locked</strong><p class="mt-0.5 text-xs leading-relaxed">The administrator locked this ballot before it was submitted.</p></div></div>
            @endif
            @if (session('vote_success'))
                <div class="mt-4 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900" role="status"><span class="material-symbols-outlined mt-0.5 text-xl">check_circle</span><div><strong class="block text-sm">Vote submitted</strong><p class="mt-0.5 text-xs leading-relaxed">{{ session('vote_success') }}</p></div></div>
            @endif

            <form id="review-vote-form" class="mt-4 rounded-xl border border-slate-200/80 bg-white p-5 shadow-sm" method="POST" action="{{ route('review-vote.verify-email') }}">
                @csrf
                <div class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-secondary"><span class="material-symbols-outlined text-lg">mark_email_read</span>Confirm voter registration</div>
                <p class="mt-2 text-xs leading-relaxed text-slate-600">Enter the email address registered by an election administrator. You must verify it before the final vote can be submitted.</p>
                <label class="mt-3 block text-sm font-bold text-[#0b192c]" for="voter-email">Registered email address<input class="mt-1.5 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-3 font-normal outline-none transition focus:border-secondary focus:ring-2 focus:ring-secondary/20" id="voter-email" name="email" type="email" value="{{ old('email', session('verified_email')) }}" required autocomplete="email" placeholder="you@example.com"></label>
                @error('email')<p class="mt-2 text-xs text-red-700">{{ $message }}</p>@enderror
                @foreach ($pendingChoices as $choice)
                    <input name="choices[]" type="hidden" value="{{ $choice }}">
                @endforeach
                <button class="mt-3 flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-secondary px-4 text-sm font-bold text-white transition hover:bg-[#0e4d9c]" type="submit"><span class="material-symbols-outlined text-lg">verified_user</span>Verify email</button>
            </form>

            <form id="review-vote-submit-form" class="mt-6 flex flex-col gap-2.5" method="POST" action="{{ route('review-vote.submit') }}">@csrf @foreach ($pendingChoices as $choice)<input name="choices[]" type="hidden" value="{{ $choice }}">@endforeach<button class="relative z-10 flex h-14 w-full cursor-pointer select-none touch-manipulation items-center justify-center gap-2 rounded-xl font-display text-base font-bold text-white shadow-md transition active:scale-[.99] {{ $isVerified && $votingOpen ? 'bg-[#0b192c] ring-4 ring-blue-200/70 hover:bg-[#10233d]' : 'cursor-not-allowed bg-slate-400 opacity-70' }}" {{ $isVerified && $votingOpen ? '' : 'disabled aria-disabled=true' }} data-email-verified="{{ $isVerified && $votingOpen ? 'true' : 'false' }}" id="submit-final-vote" type="submit"><span class="material-symbols-outlined text-xl {{ $isVerified && $votingOpen ? 'text-yellow-400' : 'text-slate-200' }}">{{ $isVerified && $votingOpen ? 'lock_open' : 'lock' }}</span>{{ ! $votingOpen ? 'VOTING LOCKED' : ($isVerified ? 'SUBMIT FINAL VOTE' : 'VERIFY EMAIL TO SUBMIT') }}</button><a class="flex h-12 w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-slate-100 text-sm font-semibold text-[#0b192c] transition hover:bg-slate-200" href="{{ route('ballot') }}"><span class="material-symbols-outlined text-lg">edit</span>GO BACK &amp; EDIT</a></form>
            <div class="mt-6 flex items-center justify-center gap-2 text-slate-400"><span class="material-symbols-outlined text-base">gavel</span><span class="text-[11px] font-semibold uppercase tracking-wider">Official District 23 Triennial Election</span></div>
        </div>
    </main>
    <script>window.electionPosition = @json($position); window.ballotRemainingSeconds = @json($remainingSeconds); window.ballotCloseUrl = @json(route('vote-countdown.close')); window.voteCsrfToken = @json(csrf_token()); window.resultsUrl = @json(route('results')); window.homeUrl = @json(route('home')); window.electionDataUrl = @json(route('election.data'));</script>
    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
</body>
</html>
