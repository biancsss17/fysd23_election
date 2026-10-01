<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0b192c">
    <title>Election of Officers · Overview</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: {
                    surface: '#f9f9ff', 'surface-container-low': '#f0f3ff', 'surface-container-high': '#dee8ff',
                    'surface-container-highest': '#d8e3fb', 'primary-container': '#0e1c2f', secondary: '#115cb9',
                    'secondary-fixed': '#d7e2ff', 'tertiary-fixed': '#ffdf98', 'tertiary-fixed-dim': '#eec14b',
                    'on-surface': '#111c2d', 'on-surface-variant': '#44474c'
                },
                fontFamily: { display: ['Outfit'], sans: ['Inter'] }
            }}
        };
    </script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    <style>
        html:has(body.home-page),
        body.home-page { overflow-x: hidden; }

        @media (min-width: 1024px) {
            html:has(body.home-page),
            body.home-page { overflow-y: hidden; }
        }

        @media (max-width: 1023px) {
            html:has(body.home-page),
            body.home-page {
                min-height: 100%;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }
        }

        @media screen and (max-width: 767px) {
            body.home-page [role="alert"],
            body.home-page [role="status"] {
                align-items: flex-start;
                overflow-wrap: anywhere;
                word-break: break-word;
                padding: .8rem;
            }

            body.home-page [role="alert"] > div,
            body.home-page [role="status"] > div {
                min-width: 0;
            }

            body.home-page #overview-active-actions section,
            body.home-page #overview-voter-access,
            body.home-page #overview-complete-actions {
                min-width: 0;
            }

            body.home-page #overview-active-actions section h2,
            body.home-page #overview-voter-access h2,
            body.home-page #overview-complete-actions h2,
            body.home-page #overview-voter-access p,
            body.home-page #overview-complete-actions p {
                overflow-wrap: anywhere;
                word-break: break-word;
            }

            body.home-page .modal {
                align-items: flex-start;
                overflow-y: auto;
                padding: max(1rem, env(safe-area-inset-top)) .75rem max(1rem, env(safe-area-inset-bottom));
                -webkit-overflow-scrolling: touch;
            }

            body.home-page .modal > div {
                max-height: calc(100dvh - 2rem);
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }

            body.home-page .modal-form {
                padding: 1.25rem !important;
            }

            body.home-page .modal-form > div.flex {
                flex-direction: column-reverse;
            }

            body.home-page .modal-form > div.flex > button {
                width: 100%;
                justify-content: center;
            }

            body.home-page #ballot-lock-overlay {
                padding: 1rem;
            }

            body.home-page #ballot-lock-overlay > div {
                max-height: calc(100dvh - 2rem);
                overflow-y: auto;
                padding: 1.25rem;
                -webkit-overflow-scrolling: touch;
            }
        }
    </style>
</head>
<body class="home-page bg-surface font-sans text-on-surface antialiased">
    <header class="fixed inset-x-0 top-0 z-40 bg-[#0b192c] pt-[env(safe-area-inset-top)] shadow-lg">
        <div class="mx-auto flex h-16 max-w-screen-2xl items-center justify-between gap-3 px-6 lg:px-10">
            <div class="flex min-w-0 items-center gap-2">
                <img class="h-10 w-10 shrink-0 rounded-full bg-white/10 object-contain p-0.5" src="{{ asset('images/uecfi-logo.png') }}" alt="Official Seal">
                <div class="min-w-0"><p class="truncate text-[10px] font-bold uppercase tracking-wider text-tertiary-fixed">District 23 FYS · Election of Officers for 2027–2030</p><p class="truncate font-display text-lg font-semibold text-white">Overview</p></div>
            </div>
            <a class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-black text-white" href="{{ route('admin.login') }}" aria-label="Administrator sign in"><span class="material-symbols-outlined text-lg">person</span></a>
        </div>
    </header>

    <main class="mx-auto min-h-screen max-w-screen-2xl px-4 pb-24 pt-20 sm:px-6 lg:px-10">
        @if (session('candidacy_error'))
            <div class="auto-dismiss-5s mb-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-red-900 shadow-sm" role="alert"><span class="material-symbols-outlined mt-0.5 text-xl">error</span><div><strong class="block text-sm">Candidacy not eligible</strong><p class="mt-0.5 text-xs leading-relaxed">{{ session('candidacy_error') }}</p></div></div>
        @endif
        @if (session('candidacy_success'))
            <div class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900 shadow-sm" role="status"><span class="material-symbols-outlined mt-0.5 text-xl">check_circle</span><div><strong class="block text-sm">Candidacy submitted</strong><p class="mt-0.5 text-xs leading-relaxed">{{ session('candidacy_success') }}</p></div></div>
        @endif
        @if (session('nomination_error'))
            <div class="auto-dismiss-5s mb-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-red-900 shadow-sm" role="alert"><span class="material-symbols-outlined mt-0.5 text-xl">error</span><div><strong class="block text-sm">Nomination not eligible</strong><p class="mt-0.5 text-xs leading-relaxed">{{ session('nomination_error') }}</p></div></div>
        @endif
        @if (session('nomination_success'))
            <div class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900 shadow-sm" role="status"><span class="material-symbols-outlined mt-0.5 text-xl">check_circle</span><div><strong class="block text-sm">Nomination submitted</strong><p class="mt-0.5 text-xs leading-relaxed">{{ session('nomination_success') }}</p></div></div>
        @endif
        @if (session('submission_error'))
            <div class="auto-dismiss-5s mb-6 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-950 shadow-sm" role="alert"><span class="material-symbols-outlined mt-0.5 text-xl">lock</span><div><strong class="block text-sm">Election actions locked</strong><p class="mt-0.5 text-xs leading-relaxed">{{ session('submission_error') }}</p></div></div>
        @endif
        <section class="relative overflow-hidden rounded-xl bg-primary-container p-4 text-white shadow-md">
            <div class="pointer-events-none absolute -bottom-6 -right-6 flex h-32 w-32 items-center justify-center rounded-full bg-blue-400/10"><span class="material-symbols-outlined text-[96px] text-blue-300/20">how_to_vote</span></div>
            <div class="relative z-10 mb-3 flex items-center justify-between gap-2"><span class="inline-flex items-center gap-1.5 rounded-full bg-tertiary-fixed px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-[#251a00] shadow-sm"><span class="h-2 w-2 animate-pulse rounded-full bg-[#251a00]"></span>Current election</span><span class="text-xs font-bold uppercase tracking-wider text-tertiary-fixed-dim">2027–2030</span></div>
            <div class="relative z-10"><h1 class="font-display text-3xl font-bold tracking-tight text-surface" id="current-position-name">{{ $allPositionsComplete ? 'Election complete' : ($position?->name ?? 'Election position') }}</h1><p class="mt-1 flex items-center gap-1 text-sm font-semibold text-tertiary-fixed" id="current-position-status"><span class="material-symbols-outlined text-lg">{{ $allPositionsComplete ? 'verified' : 'lock_open' }}</span>{{ $allPositionsComplete ? 'All ballot positions completed' : 'Candidacy & nomination open' }}</p></div>
            <div class="-mx-4 -mb-4 mt-4 flex items-center justify-between gap-2 bg-black/20 px-4 py-2 text-xs"><span class="flex items-center gap-2 text-slate-200"><span class="material-symbols-outlined text-lg text-tertiary-fixed">verified</span>District 23 FYS official protocol</span><span class="font-bold text-tertiary-fixed">CYCLE ACTIVE</span></div>
        </section>

        <section class="mt-6 rounded-xl bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between gap-2"><span class="text-[11px] font-bold uppercase tracking-wider text-on-surface-variant">Election timeline guide</span><span class="text-[11px] font-bold text-secondary">5 steps</span></div>
            <div class="relative mt-4 flex items-start justify-between"><div class="absolute left-3 right-3 top-3.5 -z-0 h-0.5 bg-surface-container-highest"></div>
                @foreach ([['edit_document', 'Candidacy'], ['fact_check', 'Verification'], ['ballot', 'Final ballot'], ['how_to_vote', 'Voting'], ['bar_chart', 'Results']] as $index => $step)
                    @php($stepNumber = $index + 1)
                    <div class="z-10 flex w-16 flex-col items-center gap-1 text-center"><div class="flex h-7 w-7 items-center justify-center rounded-full bg-surface-container-highest text-on-surface-variant"><span class="material-symbols-outlined text-base">{{ $step[0] }}</span></div><span class="text-[10px] leading-tight text-on-surface-variant">{{ $stepNumber }}. {{ $step[1] }}</span></div>
                @endforeach
            </div>
        </section>

        <div class="mt-6 grid gap-4 lg:grid-cols-2 {{ $allPositionsComplete ? 'hidden' : '' }}" id="overview-active-actions">
            <section class="relative flex flex-col gap-4 overflow-hidden rounded-xl bg-white p-4 shadow-sm"><div class="absolute inset-y-0 left-0 w-1.5 bg-secondary"></div><div class="pl-2"><p class="mb-1 flex items-center gap-1 text-xs font-bold uppercase tracking-wider text-secondary"><span class="material-symbols-outlined text-xl">badge</span>Self-declaration</p><h2 class="font-display text-xl font-bold">File candidacy</h2><p class="mt-1 text-sm leading-6 text-on-surface-variant">Submit yourself as a candidate for the current position.</p></div><button class="modal-open flex w-full items-center justify-center gap-2 rounded-lg bg-primary-container px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-secondary active:scale-[.99] disabled:cursor-not-allowed disabled:opacity-50" type="button" data-modal="modal-candidacy" @disabled(! $position || ! $position->candidacy_open)><span data-action-label>{{ $position?->candidacy_open ? 'File candidacy' : 'Candidacy locked' }}</span> <span class="material-symbols-outlined text-lg" data-action-icon>{{ $position?->candidacy_open ? 'arrow_forward' : 'lock' }}</span></button></section>
            <section class="relative flex flex-col gap-4 overflow-hidden rounded-xl bg-white p-4 shadow-sm"><div class="absolute inset-y-0 left-0 w-1.5 bg-tertiary-fixed-dim"></div><div class="pl-2"><p class="mb-1 flex items-center gap-1 text-xs font-bold uppercase tracking-wider text-[#5a4300]"><span class="material-symbols-outlined text-xl">recommend</span>Sponsor a peer</p><h2 class="font-display text-xl font-bold">Submit nomination</h2><p class="mt-1 text-sm leading-6 text-on-surface-variant">Nominate a qualified voting member for open leadership roles.</p></div><button class="modal-open flex w-full items-center justify-center gap-2 rounded-lg bg-tertiary-fixed px-4 py-3 text-sm font-bold text-[#251a00] shadow-sm transition hover:bg-tertiary-fixed-dim active:scale-[.99] disabled:cursor-not-allowed disabled:opacity-50" type="button" data-modal="modal-nomination" @disabled(! $position || ! $position->nomination_open)><span data-action-label>{{ $position?->nomination_open ? 'Submit nomination' : 'Nomination locked' }}</span> <span class="material-symbols-outlined text-lg" data-action-icon>{{ $position?->nomination_open ? 'arrow_forward' : 'lock' }}</span></button></section>
        </div>
        <section class="mt-2 rounded-xl border border-violet-200 bg-white p-4 shadow-sm {{ $allPositionsComplete ? 'hidden' : '' }}" id="overview-voter-access"><a class="block rounded-lg bg-gradient-to-r from-violet-700 to-indigo-700 p-5 text-white shadow-sm transition hover:from-violet-800 hover:to-indigo-800 {{ $position ? '' : 'pointer-events-none cursor-not-allowed opacity-60' }}" style="background-image: linear-gradient(90deg, #6d28d9 0%, #4338ca 100%);" href="{{ $position ? route('voter-access') : '#' }}" data-original-href="{{ route('voter-access') }}" data-voter-access-link aria-disabled="{{ $position ? 'false' : 'true' }}"><div class="flex flex-col items-center justify-between gap-4 text-center sm:flex-row sm:text-left"><div><p class="flex items-center justify-center gap-2 text-xs font-bold uppercase tracking-wider text-violet-100 sm:justify-start"><span class="material-symbols-outlined text-xl">how_to_vote</span>Voter access</p><h2 class="mt-1 font-display text-xl font-bold" data-voter-access-title>{{ $position ? 'Ready to vote?' : 'Voting locked' }}</h2><p class="mt-1 text-sm text-violet-100" data-voter-access-description>{{ $position ? 'Continue directly to the ' . $position->name . ' ballot.' : 'Create an election position before voting.' }}</p></div></div></a></section>
        <section class="mt-2 rounded-xl border border-violet-200 bg-white p-4 shadow-sm {{ $allPositionsComplete ? '' : 'hidden' }}" id="overview-complete-actions"><a class="block rounded-lg bg-gradient-to-r from-violet-700 to-indigo-700 p-5 text-white shadow-sm transition hover:from-violet-800 hover:to-indigo-800" href="{{ route('final-document') }}"><div class="flex flex-col items-center justify-between gap-4 text-center sm:flex-row sm:text-left"><div><p class="flex items-center justify-center gap-2 text-xs font-bold uppercase tracking-wider text-violet-100 sm:justify-start"><span class="material-symbols-outlined text-xl">verified</span>Election complete</p><h2 class="mt-1 font-display text-xl font-bold">All positions are complete</h2><p class="mt-1 text-sm text-violet-100">View and print the official final document.</p></div><span class="inline-flex items-center gap-2 rounded-lg bg-white px-5 py-3 text-sm font-bold text-violet-800">Show results <span class="material-symbols-outlined text-lg">arrow_forward</span></span></div></a></section>

        <section class="mt-4 rounded-xl bg-white p-4 shadow-sm"><div class="border-b border-surface-container-highest pb-3"><p class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-secondary"><span class="material-symbols-outlined text-lg">help_outline</span>Guide &amp; guidelines</p><h2 class="mt-1 font-display text-xl font-bold">How the election works</h2><p class="mt-1 text-xs leading-5 text-on-surface-variant">A simple four-step guide to participating in the District 23 FYS Election of Officers.</p></div><div class="mt-4 space-y-4">
            @foreach ([['Enter your email', 'Enter your email to access the election feature. Only present and counted members are eligible.'], ['Nomination & candidacy', 'Submit your candidacy or sponsor a qualified member during the active nomination window.'], ['Cast your ballot', 'Review candidate credentials, select your preferred choices, and submit securely.'], ['Live verification & results', 'Track turnout progress and view certified official results after polls close.']] as $index => $guide)
                <div class="flex items-start gap-2"><div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-secondary-fixed text-sm font-bold text-[#001a40]">{{ $index + 1 }}</div><div><h3 class="text-sm font-bold">{{ $guide[0] }}</h3><p class="mt-0.5 text-xs leading-5 text-on-surface-variant">{{ $guide[1] }}</p></div></div>
            @endforeach
        </div></section>
    </main>

    @foreach ([['candidacy', 'File candidacy', 'Candidate full name', 'Email address', 'Submit candidacy'], ['nomination', 'Submit nomination', 'Nominee full name', 'Nominator email', 'Submit nomination']] as $modal)
        <div id="modal-{{ $modal[0] }}" class="modal fixed inset-0 z-50 hidden items-center justify-center bg-[#0b192c]/70 p-4 opacity-0 backdrop-blur-sm transition-opacity duration-200" role="dialog" aria-modal="true">
            <div class="w-full max-w-xl overflow-hidden rounded-2xl bg-white shadow-2xl">
                <div class="flex items-center justify-between px-5 py-4 {{ $modal[0] === 'candidacy' ? 'text-white' : 'text-[#251a00]' }}" style="background: {{ $modal[0] === 'candidacy' ? 'linear-gradient(135deg, #6d28d9 0%, #3730a3 100%)' : '#ffdf9a' }};">
                    <h2 class="flex items-center gap-2 font-display text-lg font-bold">
                        <span class="material-symbols-outlined text-2xl">{{ $modal[0] === 'candidacy' ? 'how_to_vote' : 'recommend' }}</span>
                        {{ $modal[1] }}
                    </h2>
                    <button class="modal-close rounded-full p-1 transition hover:bg-black/10" type="button" aria-label="Close"><span class="material-symbols-outlined">close</span></button>
                </div>
                <form class="modal-form space-y-5 p-6 sm:p-7">
                    <p class="text-sm leading-6 text-on-surface-variant">{{ $modal[0] === 'candidacy' ? 'Enter your details to register your candidacy for ' . ($position?->name ?? 'the current position') . '.' : 'Nominate a qualified voting member for ' . ($position?->name ?? 'the current position') . '.' }}</p>
                    <label class="block text-sm font-bold">{{ $modal[2] }}<input class="mt-2 w-full rounded-lg border border-surface-container-highest bg-surface-container-low px-4 py-3 font-normal outline-none transition focus:border-secondary focus:ring-2 focus:ring-secondary/30" type="text" required placeholder="Enter full name"></label>
                    <label class="block text-sm font-bold">{{ $modal[3] }}<input class="mt-2 w-full rounded-lg border border-surface-container-highest bg-surface-container-low px-4 py-3 font-normal outline-none transition focus:border-secondary focus:ring-2 focus:ring-secondary/30" type="email" required placeholder="name@example.com"></label>
                    <div class="flex justify-end gap-3 pt-1"><button class="modal-close rounded-lg px-4 py-3 text-sm font-bold text-on-surface-variant transition hover:bg-surface-container-low" type="button">Cancel</button><button class="modal-submit rounded-lg {{ $modal[0] === 'candidacy' ? 'bg-primary-container text-white hover:bg-secondary' : 'bg-tertiary-fixed text-[#251a00] hover:bg-[#ffd27a]' }} px-5 py-3 text-sm font-bold shadow-sm transition" type="submit">{{ $modal[4] }}</button></div>
                </form>
                <div class="modal-success hidden p-8 text-center"><span class="material-symbols-outlined text-5xl text-secondary">check_circle</span><h3 class="mt-2 font-display text-lg font-bold">{{ $modal[0] === 'candidacy' ? 'Candidacy recorded!' : 'Nomination recorded!' }}</h3><p class="mt-1 text-sm text-on-surface-variant">Your submission has been saved to the official ballot register.</p><button class="modal-close mt-4 rounded-lg bg-primary-container px-5 py-2.5 text-sm font-bold text-white" type="button">Close</button></div>
            </div>
        </div>
    @endforeach

    <script>window.electionPosition = @json($position); window.electionDataUrl = @json(route('election.data'));</script>
    <script>
        const guideSection = Array.from(document.querySelectorAll('main section')).find((section) =>
            section.querySelector('h2')?.textContent.includes('How the election works')
        );
        guideSection?.remove();

        const candidacyForm = document.querySelector('#modal-candidacy .modal-form');
        if (candidacyForm) {
            candidacyForm.dataset.serverForm = 'candidacy';
            candidacyForm.method = 'POST';
            candidacyForm.action = @json(route('candidacy.submit'));
            const fields = candidacyForm.querySelectorAll('input');
            if (fields[0]) fields[0].name = 'full_name';
            if (fields[1]) fields[1].name = 'email';
            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = @json(csrf_token());
            candidacyForm.prepend(token);
        }

        const nominationForm = document.querySelector('#modal-nomination .modal-form');
        if (nominationForm) {
            nominationForm.dataset.serverForm = 'nomination';
            nominationForm.method = 'POST';
            nominationForm.action = @json(route('nomination.submit'));
            const fields = nominationForm.querySelectorAll('input');
            if (fields[0]) fields[0].name = 'nominee_name';
            if (fields[1]) fields[1].name = 'email';
            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = @json(csrf_token());
            nominationForm.prepend(token);
        }
    </script>
    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
</body>
</html>


