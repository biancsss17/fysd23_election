<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Election Results · District 23 FYS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#f9f9ff] px-4 py-10 font-sans text-[#111c2d]">
    <main class="mx-auto max-w-3xl">
        <section class="rounded-2xl bg-[#0b192c] p-8 text-center text-white shadow-xl">
            <img src="{{ asset('images/uecfi-logo.png') }}" alt="UECFI logo" class="mx-auto h-20 w-20 rounded-full object-contain">
            <h1 class="mt-5 text-4xl font-bold">{{ $currentPosition?->name ?? 'Election' }} Results</h1>
            <p class="mt-2 text-sm text-blue-100">Official counted results for the submitted ballots</p>
        </section>
        <section class="mt-6 rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="text-2xl font-bold">Winner</h2>
            @if ($winners->isNotEmpty())
                <div class="mt-4 space-y-3">
                    @foreach ($winners as $winner)
                        <div class="rounded-xl bg-[#e7eeff] p-5"><p class="text-xs font-bold uppercase tracking-widest text-[#115cb9]">{{ $winners->count() > 1 ? 'Elected winner '.$loop->iteration : 'Elected winner' }}</p><strong class="mt-1 block text-3xl">{{ $winner->display_candidate_name }}</strong><p class="mt-1 text-sm text-[#44474c]">{{ $winner->votes_count }} vote{{ $winner->votes_count === 1 ? '' : 's' }}</p></div>
                    @endforeach
                </div>
            @else
                <div class="mt-4 rounded-xl bg-slate-100 p-5 text-sm text-[#44474c]">No counted votes yet.</div>
            @endif
            <h2 class="mt-8 text-2xl font-bold">Vote Count</h2>
            <div class="mt-4 space-y-3">
                @forelse ($results as $candidate)
                    <div class="flex items-center justify-between rounded-lg border border-slate-200 px-4 py-3"><span class="font-semibold">{{ $candidate->candidate_name }}</span><span class="rounded-full bg-[#e7eeff] px-3 py-1 text-sm font-bold text-[#115cb9]">{{ $candidate->votes_count }}</span></div>
                @empty
                    <p class="text-sm text-[#44474c]">No candidates are listed.</p>
                @endforelse
                <div class="flex items-center justify-between rounded-lg border border-slate-200 px-4 py-3"><span class="font-semibold">ABSTAIN</span><span class="rounded-full bg-[#e7eeff] px-3 py-1 text-sm font-bold text-[#115cb9]">{{ $abstentions }}</span></div>
            </div>
            @if ($nextPosition)
                <a href="{{ route('ballot', ['positionId' => $nextPosition->id]) }}" class="mt-8 flex h-12 items-center justify-center rounded-xl bg-[#0b192c] font-bold text-white">Continue to {{ $nextPosition->name }}</a>
            @else
                <a href="{{ route('home') }}" class="mt-8 flex h-12 items-center justify-center rounded-xl bg-[#0b192c] font-bold text-white">Return to election home</a>
            @endif
        </section>
    </main>
</body>
</html>
