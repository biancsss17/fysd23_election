<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Vote submitted · District 23 FYS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#f7f8ff] text-[#0b192c]">
    <main class="flex min-h-screen items-center justify-center px-5 py-10">
        <section class="w-full max-w-xl rounded-3xl bg-white px-8 py-10 text-center shadow-[0_18px_45px_rgba(11,25,44,.16)] sm:px-12">
            <img alt="District 23 FYS logo" class="mx-auto h-24 w-24 object-contain" src="{{ asset('images/uecfi-logo.png') }}">
            <p class="mt-6 text-sm font-bold uppercase tracking-[.28em] text-[#115cb9]">Vote submitted</p>
            <h1 class="mt-3 text-4xl font-bold">Please wait for the result</h1>
            <p class="mx-auto mt-4 max-w-md text-base leading-6 text-slate-600">Your vote was recorded. The result will be available when the countdown ends.</p>
            <div class="relative mt-8 overflow-hidden rounded-2xl bg-[#eef2ff] px-6 py-10">
                <div aria-hidden="true" class="absolute inset-0 scale-110 bg-white/50 blur-xl"></div>
                <p class="relative text-lg font-bold text-[#115cb9]">Please wait for the result</p>
            </div>
            <p class="mt-8 text-6xl font-bold text-[#115cb9]" id="remaining-seconds">{{ $remainingSeconds }}</p>
            <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Seconds remaining before results</p>
            <a class="mt-8 inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-[#0b192c] px-6 py-3 text-sm font-bold text-white transition hover:bg-[#132742]" href="{{ route('home') }}">RETURN TO OVERVIEW</a>
        </section>
    </main>
    <script>
        let remaining = Number(@json($remainingSeconds));
        const counter = document.getElementById('remaining-seconds');
        const closeUrl = @json(route('vote-countdown.close'));
        const resultsUrl = @json(route('results'));
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const finish = async () => {
            try {
                await fetch(closeUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' }, credentials: 'same-origin' });
            } finally {
                window.location.replace(resultsUrl);
            }
        };
        if (remaining <= 0) finish();
        else {
            const timer = window.setInterval(() => {
                remaining -= 1;
                if (counter) counter.textContent = String(Math.max(remaining, 0));
                if (remaining <= 0) {
                    window.clearInterval(timer);
                    finish();
                }
            }, 1000);
        }
    </script>
</body>
</html>
