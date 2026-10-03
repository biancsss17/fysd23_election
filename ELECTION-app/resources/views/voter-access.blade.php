<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Voter Access · District 23 FYS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex min-h-screen items-center justify-center bg-[#f9f9ff] px-4 py-8 font-sans text-[#111c2d]">
    <main class="w-full max-w-lg rounded-2xl bg-white p-8 shadow-xl">
        <div class="text-center">
            <img src="{{ asset('images/uecfi-logo.png') }}" alt="UECFI logo" class="mx-auto h-20 w-20 rounded-full object-contain">
            <p class="mt-5 text-xs font-bold uppercase tracking-widest text-[#115cb9]">Voter access</p>
            <h1 class="mt-2 text-3xl font-bold">Verify your email</h1>
            <p class="mt-3 text-sm text-[#44474c]">Enter the email registered by an election administrator to open the ballot.</p>
        </div>

        @if (session('access_error'))
            <div class="auto-dismiss-5s mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700" role="alert">{{ session('access_error') }}</div>
        @endif

        @php($votingOpen = $position && $position->is_unlocked && ! $position->is_closed)
        @if (! $votingOpen)
            <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-900" role="status">Voting access will open when the administrator unlocks {{ $position?->name ?? 'the ballot' }}.</div>
        @else
            <div class="mt-6 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-center text-sm font-bold text-[#115cb9]" id="access-countdown-wrap">Voting window: <span id="access-countdown">{{ $remainingSeconds }}</span>s remaining</div>
        @endif

        <form class="mt-6 space-y-4" method="POST" action="{{ route('voter-access.verify') }}">
            @csrf
            <label class="block text-sm font-bold" for="email">Registered email address
                <input class="mt-2 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 py-3 font-normal outline-none transition focus:border-[#115cb9] focus:ring-2 focus:ring-[#115cb9]/20" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="name@example.com" required autofocus>
            </label>
            @error('email')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            <button class="flex w-full items-center justify-center gap-2 rounded-lg bg-violet-700 px-5 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-violet-800 disabled:cursor-not-allowed disabled:opacity-50" type="submit" @disabled(! $votingOpen)>{{ $votingOpen ? 'Submit' : 'Voting locked' }}</button>
        </form>

        <a class="mt-4 block text-center text-sm font-semibold text-[#115cb9]" href="{{ route('home') }}">Back to overview</a>
    </main>
    <script>
        const electionDataUrl = @json(route('election.data'));
        const initialVotingOpen = @json($votingOpen);
        let accessRemaining = @json($remainingSeconds);
        const accessCountdown = document.getElementById('access-countdown');
        const accessCountdownTimer = window.setInterval(() => {
            if (!accessCountdown || accessRemaining <= 0) {
                window.clearInterval(accessCountdownTimer);
                return;
            }
            accessRemaining -= 1;
            accessCountdown.textContent = String(accessRemaining);
        }, 1000);
        setInterval(async () => {
            try {
                const response = await fetch(electionDataUrl, { headers: { Accept: 'application/json' }, cache: 'no-store' });
                if (!response.ok) return;
                const data = await response.json();
                const votingOpen = Boolean(data.active_position?.is_unlocked && !data.active_position?.is_closed);
                if (votingOpen !== initialVotingOpen) window.location.reload();
            } catch {}
        }, 1000);
    </script>
</body>
</html>
