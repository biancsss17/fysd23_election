<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Results pending · District 23 FYS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex min-h-screen items-center justify-center bg-[#f9f9ff] px-4 font-sans text-[#111c2d]">
    <main class="w-full max-w-lg rounded-2xl bg-white p-10 text-center shadow-xl">
        <img src="{{ asset('images/uecfi-logo.png') }}" alt="UECFI logo" class="mx-auto h-20 w-20 rounded-full object-contain">
        <p class="mt-6 text-sm font-bold uppercase tracking-[0.2em] text-[#115cb9]">Vote submitted</p>
        <h1 class="mt-3 text-3xl font-bold">Results pending</h1>
        <p class="mt-3 text-sm text-[#44474c]">The result is temporarily blurred while this position is finalized.</p>
        <div class="relative mt-7 overflow-hidden rounded-xl bg-[#eef2ff] px-6 py-5">
            <div class="select-none blur-md" aria-hidden="true">
                <div class="h-4 w-2/5 rounded bg-[#115cb9]/40"></div>
                <div class="mt-4 h-3 w-full rounded bg-[#115cb9]/20"></div>
                <div class="mt-2 h-3 w-4/5 rounded bg-[#115cb9]/20"></div>
            </div>
            <div class="absolute inset-0 flex items-center justify-center text-sm font-bold text-[#115cb9]">Results will be revealed soon</div>
        </div>
        <div id="countdown" class="mt-6 text-7xl font-bold text-[#115cb9]">30</div>
        <p class="mt-2 text-xs font-bold uppercase tracking-widest text-[#75777d]">Please wait</p>
    </main>
    <script>
        let seconds = 30;
        const countdown = document.getElementById('countdown');
        const timer = window.setInterval(async () => {
            seconds -= 1;
            countdown.textContent = seconds;

            if (seconds <= 0) {
                window.clearInterval(timer);
                await fetch(@json(route('ballot.auto-close')), {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': @json(csrf_token()), Accept: 'application/json' },
                    credentials: 'same-origin',
                });
                window.location.assign(@json(route('results')));
            }
        }, 1000);
    </script>
</body>
</html>
