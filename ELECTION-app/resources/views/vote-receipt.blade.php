<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0b192c">
    <title>Ballot Verification Receipt · District 23 FYS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: {
            colors: { surface: '#f9f9ff', 'surface-container-low': '#f0f3ff', 'surface-container-high': '#dee8ff', 'primary-container': '#0e1c2f', secondary: '#115cb9', 'tertiary-fixed': '#ffdf98', 'tertiary-fixed-dim': '#eec14b' },
            fontFamily: { display: ['Outfit'], sans: ['Inter'] }
        } } };
    </script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="min-h-screen bg-surface font-sans text-[#111c2d] antialiased">
    <header class="fixed inset-x-0 top-0 z-50 bg-[#0b192c] text-white shadow-md pt-[env(safe-area-inset-top)]">
        <div class="mx-auto flex h-20 max-w-screen-2xl items-center justify-between gap-3 px-4 sm:px-6 lg:px-10"><div class="flex min-w-0 items-center gap-2"><a href="{{ route('home') }}" aria-label="Go back" class="-ml-1 flex h-11 w-11 items-center justify-center rounded-lg text-white/80 transition hover:bg-white/10 hover:text-white"><span class="material-symbols-outlined text-2xl">arrow_back</span></a><img alt="District 23 Seal" class="h-10 w-10 shrink-0 rounded-full object-contain" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC_REJddrHw_bWgpMI32KSLVrcjrKF5LTp_PeDVEv8Re07EuT1dkhaL2UIoqfGcBdFPS6fM7QSKBVzk79j8B1DjjxDxgt0n5ZyUU0wwYt0u9fcLvCh9W_h4hR-IvPQKIOJI71oiMuzex8M_V0ef2jW9FIpTuMocq8T0TnImhN5-I8lfrUAaEjOBRGbQ8zuc10S8hUZMD2gar6rYqvRicpFXfrrVECouz6ai5142-0hXkATFzhxCV_uJxCoS4pkthqkc"><div class="min-w-0"><h1 class="truncate font-display text-[17px] font-bold leading-tight">Ballot Verification Receipt</h1><p class="truncate text-[11px] font-semibold uppercase tracking-wider text-[#eec14b]">District 23 FYS · 2027–2030</p></div></div><a href="{{ route('admin.login') }}" aria-label="Administrator sign in" class="flex h-9 w-9 items-center justify-center rounded-full border border-white/20 bg-white/10"><span class="material-symbols-outlined text-lg">person</span></a></div>
    </header>

    <main class="mx-auto min-h-screen max-w-screen-2xl bg-surface px-4 pb-10 pt-24 sm:px-6 lg:px-10"><div class="mx-auto max-w-2xl pb-10">
        <section class="relative mb-6 flex flex-col items-center overflow-hidden rounded-xl border-t-4 border-[#eec14b] bg-white p-6 text-center shadow-md"><div class="absolute left-0 top-0 h-1 w-2/3 bg-primary-container"></div><span class="pointer-events-none absolute -bottom-10 -right-10 text-[200px] text-primary opacity-[.03] material-symbols-outlined">verified</span><div class="relative my-4 flex items-center justify-center"><div class="flex h-24 w-24 items-center justify-center rounded-full bg-surface-container-high shadow-sm"><div class="flex h-20 w-20 items-center justify-center rounded-full bg-tertiary-fixed-dim shadow-inner"><div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary-container shadow-md"><span class="material-symbols-outlined text-[36px] text-tertiary-fixed">check</span></div></div></div><div class="absolute -bottom-1 -right-1 flex items-center justify-center rounded-full bg-white p-1 shadow-sm"><span class="material-symbols-outlined text-lg text-secondary">verified_user</span></div></div><h2 class="mt-1 font-display text-3xl font-bold tracking-tight text-primary-container">VOTE SUBMITTED ✓</h2><p class="mt-2 max-w-xs text-sm leading-6 text-[#44474c]">Your vote for {{ session('receipt_position', 'the current position') }} has been successfully recorded and sealed.</p></section>

        <section class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4"><div class="flex items-start gap-3"><span class="material-symbols-outlined mt-0.5 text-xl text-emerald-700">verified</span><div><h3 class="text-sm font-bold text-emerald-950">Submission certified</h3><p class="mt-0.5 text-xs leading-5 text-emerald-900/80">Your ballot has passed the voter verification check and is ready for the official election ledger.</p></div></div></section>

        <section class="mb-6 rounded-xl border border-slate-200/80 bg-white p-5 shadow-sm"><div class="flex items-center gap-2 border-b border-slate-100 pb-3"><span class="material-symbols-outlined text-xl text-secondary">receipt_long</span><h3 class="font-display text-lg font-bold text-primary-container">Cryptographic audit receipt</h3></div><dl class="mt-4 space-y-3 text-sm"><div class="flex items-start justify-between gap-4"><dt class="text-[#44474c]">Ballot position</dt><dd class="font-bold text-primary-container">{{ session('receipt_position', 'Pending') }}</dd></div><div class="flex items-start justify-between gap-4"><dt class="text-[#44474c]">Recorded nominee</dt><dd class="font-bold text-primary-container">{{ session('receipt_nominee', 'Pending') }}</dd></div><div class="flex items-start justify-between gap-4"><dt class="text-[#44474c]">Submitted at</dt><dd class="text-right font-semibold text-primary-container">{{ session('vote_submitted_at') ? \Illuminate\Support\Carbon::parse(session('vote_submitted_at'))->format('M d, Y h:i:s A') : 'Pending' }}</dd></div></dl><div class="mt-4 rounded-lg bg-surface-container-low p-3"><div class="flex items-center justify-between gap-3"><div class="min-w-0"><p class="text-[11px] font-bold uppercase tracking-wider text-[#44474c]">Receipt token</p><p class="mt-1 break-all font-mono text-xs text-primary-container">{{ session('receipt_token', 'receipt-pending') }}</p></div><button class="copy-receipt shrink-0 rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-bold text-secondary" type="button" data-token="{{ session('receipt_token', 'receipt-pending') }}">Copy token</button></div></div></section>

        <a class="flex h-14 w-full items-center justify-center gap-2 rounded-lg bg-[#0b192c] text-sm font-bold uppercase tracking-wider text-white shadow-md transition hover:bg-secondary active:scale-[.99]" href="{{ route('home') }}"><span class="material-symbols-outlined text-xl">home</span>Return to election home</a>
        <p class="mt-6 flex items-center justify-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400"><span class="material-symbols-outlined text-base">gavel</span>Official District 23 Triennial Election</p>
    </div></main>
    <script>window.electionDataUrl = @json(route('election.data'));</script>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
