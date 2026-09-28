<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eligible Voters | District 23 FYS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#f9f9ff] font-[Inter] text-[#111c2d] antialiased">
    @include('admin.partials.header')
    <aside class="fixed bottom-0 left-0 top-16 z-40 hidden w-64 bg-[#f0f3ff] py-6 lg:flex lg:flex-col">
        <div class="px-6 pb-4 text-[11px] font-bold uppercase tracking-wider text-[#44474c]">Navigation</div>
        <nav class="flex flex-col gap-1 px-2">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 rounded px-4 py-2.5 text-sm text-[#44474c] transition hover:bg-[#dee8ff]"><span class="material-symbols-outlined text-xl">dashboard</span>Dashboard</a>
            <a aria-current="page" href="{{ route('admin.voter-management') }}" class="flex items-center gap-2 rounded border-l-4 border-[#ffdf98] bg-[#dee8ff] px-4 py-2.5 pl-5 text-sm font-bold text-[#115cb9]"><span class="material-symbols-outlined text-xl">badge</span>Voter Management</a>
            <a href="{{ route('admin.position-management') }}" class="flex items-center gap-2 rounded px-4 py-2.5 text-sm text-[#44474c] transition hover:bg-[#dee8ff]"><span class="material-symbols-outlined text-xl">work</span>Position Management</a>
            <a href="{{ route('admin.candidates-nominations') }}" class="flex items-center gap-2 rounded px-4 py-2.5 text-sm text-[#44474c] transition hover:bg-[#dee8ff]"><span class="material-symbols-outlined text-xl">groups</span>Candidates &amp; Nominations</a>
            <a href="{{ route('admin.results-document-preview') }}" class="flex items-center gap-2 rounded px-4 py-2.5 text-sm text-[#44474c] transition hover:bg-[#dee8ff]"><span class="material-symbols-outlined text-xl">description</span>Results &amp; Document Preview</a>
        </nav>
    </aside>
    <main class="min-h-screen bg-[#f9f9ff] px-4 pb-8 pt-24 sm:px-6 lg:ml-64">
        <div class="mx-auto flex w-full max-w-6xl flex-col gap-6">
            <section class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end"><div><h1 class="mt-1 font-[Outfit] text-3xl font-bold sm:text-4xl">ELIGIBLE VOTERS</h1><p class="mt-2 text-sm text-[#44474c]">Manage registered voter eligibility and track participation.</p></div><div class="flex gap-2"><button type="button" onclick="openImportVoterModal()" class="flex items-center gap-2 rounded border border-[#c5c6cd] bg-white px-4 py-2.5 text-xs font-bold text-[#111c2d] transition hover:bg-[#f0f3ff]"><span class="material-symbols-outlined text-lg">upload_file</span>IMPORT VOTERS</button><button type="button" onclick="openAddVoterModal()" class="flex items-center gap-2 rounded bg-[#0e1c2f] px-4 py-2.5 text-xs font-bold text-white transition hover:bg-[#115cb9]"><span class="material-symbols-outlined text-lg">person_add</span>ADD VOTER</button></div></section>
            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3"><div class="rounded-xl bg-white p-5 shadow-sm"><span class="text-[11px] font-bold uppercase text-[#44474c]">Total Roster</span><div class="mt-2 flex items-end gap-2"><span class="font-[Outfit] text-4xl font-bold">{{ $voters->count() }}</span><span class="pb-1 text-xs text-[#44474c]">registered</span></div><span class="material-symbols-outlined mt-2 text-[#115cb9]">group</span></div><div class="rounded-xl bg-white p-5 shadow-sm"><span class="text-[11px] font-bold uppercase text-[#44474c]">Awaiting Vote</span><div class="mt-2 flex items-end gap-2"><span class="font-[Outfit] text-4xl font-bold text-[#115cb9]">{{ $voters->where('is_active', true)->count() }}</span><span class="pb-1 text-xs text-[#44474c]">pending</span></div><span class="material-symbols-outlined mt-2 text-[#115cb9]">pending_actions</span></div><div class="rounded-xl bg-white p-5 shadow-sm"><span class="text-[11px] font-bold uppercase text-[#44474c]">Ineligible Count</span><div class="mt-2 flex items-end gap-2"><span class="font-[Outfit] text-4xl font-bold text-[#ba1a1a]">{{ $voters->where('is_active', false)->count() }}</span><span class="pb-1 text-xs text-[#44474c]">blocked</span></div><span class="material-symbols-outlined mt-2 text-[#ba1a1a]">block</span></div></section>
            <section class="overflow-hidden rounded-xl bg-white shadow-sm"><div class="flex flex-wrap gap-2 border-b border-[#d8e3fb] p-4"><button class="rounded-full bg-[#0e1c2f] px-4 py-2 text-xs font-bold text-white">All ({{ $voters->count() }})</button><button class="rounded-full bg-[#f0f3ff] px-4 py-2 text-xs font-semibold text-[#44474c]">Voted (0)</button><button class="rounded-full bg-[#f0f3ff] px-4 py-2 text-xs font-semibold text-[#44474c]">Not Yet ({{ $voters->where('is_active', true)->count() }})</button><button class="rounded-full bg-[#f0f3ff] px-4 py-2 text-xs font-semibold text-[#44474c]">Eligible ({{ $voters->where('is_active', true)->count() }})</button><button class="rounded-full bg-[#f0f3ff] px-4 py-2 text-xs font-semibold text-[#44474c]">Ineligible ({{ $voters->where('is_active', false)->count() }})</button></div><div class="border-b border-[#d8e3fb] p-4"><label class="relative block"><span class="material-symbols-outlined absolute left-3 top-2.5 text-lg text-[#75777d]">search</span><input id="voter-search" type="search" placeholder="Search by email" class="w-full rounded border border-[#c5c6cd] py-2.5 pl-10 pr-3 text-sm outline-none focus:border-[#115cb9] focus:ring-2 focus:ring-[#d8e3fb]"></label></div><div class="overflow-x-auto"><table class="w-full min-w-[720px] text-left text-sm"><thead class="bg-[#f0f3ff] text-[11px] uppercase tracking-wide text-[#44474c]"><tr><th class="px-5 py-3 font-bold">Email</th><th class="px-5 py-3 font-bold">Eligibility</th><th class="px-5 py-3 font-bold">Current Status</th></tr></thead><tbody id="voter-table">@forelse ($voters as $voter)<tr class="voter-row border-t border-[#eef1fa]" data-email="{{ strtolower($voter->email) }}"><td class="px-5 py-4 font-semibold">{{ $voter->email }}</td><td class="px-5 py-4"><form method="POST" action="{{ route('admin.voter-management.eligibility', $voter) }}" class="inline-flex">@csrf @method('PATCH')<select name="is_active" onchange="this.form.submit()" class="rounded border border-[#c5c6cd] bg-white px-2 py-1.5 text-xs font-bold outline-none focus:border-[#115cb9]"><option value="1" @selected($voter->is_active)>Eligible</option><option value="0" @selected(! $voter->is_active)>Ineligible</option></select></form></td><td class="px-5 py-4"><span class="inline-flex items-center gap-1 text-xs text-[#44474c]"><span class="material-symbols-outlined text-base">schedule</span>Not Yet</span></td></tr>@empty<tr><td colspan="3" class="px-5 py-10 text-center text-sm text-[#44474c]">No registered voters found.</td></tr>@endforelse</tbody></table></div><div class="flex items-center justify-between border-t border-[#d8e3fb] px-5 py-4 text-xs text-[#44474c]"><span>Showing {{ $voters->count() ? 1 : 0 }}–{{ $voters->count() }} of {{ $voters->count() }} registered voter records</span><span class="flex items-center gap-3"><button type="button" class="text-[#75777d]"><span class="material-symbols-outlined text-base">chevron_left</span></button><strong>1</strong><button type="button" class="text-[#75777d]"><span class="material-symbols-outlined text-base">chevron_right</span></button></span></div></section>
        </div>
    </main>
    <div id="import-voter-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-[#0e1c2f]/60 px-4"><div class="w-full max-w-xl rounded-xl bg-white p-6 shadow-2xl"><div class="flex items-start justify-between"><div><h2 class="mt-1 font-[Outfit] text-2xl font-bold">IMPORT VOTERS</h2><p class="mt-1 text-sm text-[#44474c]">Edit the roster below. Use one email per line. Removing an email from this list removes it from the voter table.</p></div><button type="button" onclick="closeImportVoterModal()" class="text-[#75777d]"><span class="material-symbols-outlined">close</span></button></div>@if (session('voter_import_error'))<div class="mt-4 rounded-lg bg-[#ffdad6] p-3 text-sm font-semibold text-[#93000a]">{{ session('voter_import_error') }}</div>@endif<form method="POST" action="{{ route('admin.voter-management.delete-email') }}" onsubmit="return confirm('Delete this voter email from the roster?')" class="mt-4 rounded-lg border border-[#ffdad6] bg-[#fff8f7] p-3"><div class="flex flex-col gap-2 sm:flex-row"><input type="email" name="delete_email" value="{{ old('delete_email') }}" required placeholder="Enter email to delete" class="min-w-0 flex-1 rounded border border-[#c5c6cd] bg-white px-3 py-2 text-sm outline-none focus:border-[#ba1a1a]">@csrf<button type="submit" class="rounded bg-[#ba1a1a] px-4 py-2 text-xs font-bold text-white hover:bg-[#93000a]">DELETE EMAIL</button></div></form><form method="POST" action="{{ route('admin.voter-management.import') }}" class="mt-5 space-y-4">@csrf<textarea name="emails" rows="3" required class="w-full rounded border border-[#c5c6cd] p-3 font-mono text-sm outline-none focus:border-[#115cb9] focus:ring-2 focus:ring-[#d8e3fb]">{{ old('emails', $voters->pluck('email')->implode("\n")) }}</textarea><div class="flex gap-3"><button type="button" onclick="closeImportVoterModal()" class="flex-1 rounded border border-[#c5c6cd] px-4 py-3 text-xs font-bold">CANCEL</button><button type="submit" class="flex-1 rounded bg-[#0e1c2f] px-4 py-3 text-xs font-bold text-white hover:bg-[#115cb9]">SAVE ROSTER</button></div></form></div></div><div id="add-voter-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-[#0e1c2f]/60 px-4"><div class="w-full max-w-md rounded-xl bg-white p-6 shadow-2xl"><div class="flex items-start justify-between"><div><h2 class="mt-1 font-[Outfit] text-2xl font-bold">ADD VOTER</h2><p class="mt-1 text-sm text-[#44474c]">Enter the voter email to add it to the eligible voter table.</p></div><button type="button" onclick="closeAddVoterModal()" class="text-[#75777d]"><span class="material-symbols-outlined">close</span></button></div>@if (session('voter_error'))<div class="mt-4 rounded-lg bg-[#ffdad6] p-3 text-sm font-semibold text-[#93000a]">{{ session('voter_error') }}</div>@endif
@if ($errors->any())<div class="mt-4 rounded-lg bg-[#ffdad6] p-3 text-sm text-[#93000a]">{{ $errors->first('email') }}</div>@endif
<form method="POST" action="{{ route('admin.voter-management.store') }}" class="mt-5 space-y-4">@csrf<label class="block"><span class="mb-2 block text-xs font-bold">Voter Email</span><input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="voter@example.com" class="w-full rounded border border-[#c5c6cd] px-3 py-3 text-sm outline-none focus:border-[#115cb9] focus:ring-2 focus:ring-[#d8e3fb]"></label><div class="flex gap-3"><button type="button" onclick="closeAddVoterModal()" class="flex-1 rounded border border-[#c5c6cd] px-4 py-3 text-xs font-bold">CANCEL</button><button type="submit" class="flex-1 rounded bg-[#0e1c2f] px-4 py-3 text-xs font-bold text-white hover:bg-[#115cb9]">ADD TO ROSTER</button></div></form></div></div><div id="notice" class="fixed bottom-6 right-6 z-50 hidden rounded-lg bg-[#0e1c2f] px-5 py-3 text-sm font-semibold text-white shadow-xl">Action completed successfully.</div>
    <script>
        function openImportVoterModal() { const modal = document.getElementById('import-voter-modal'); modal.classList.remove('hidden'); modal.classList.add('flex'); } function closeImportVoterModal() { const modal = document.getElementById('import-voter-modal'); modal.classList.add('hidden'); modal.classList.remove('flex'); } function openAddVoterModal() { const modal = document.getElementById('add-voter-modal'); modal.classList.remove('hidden'); modal.classList.add('flex'); } function closeAddVoterModal() { const modal = document.getElementById('add-voter-modal'); modal.classList.add('hidden'); modal.classList.remove('flex'); } function showNotice(message) { const notice = document.getElementById('notice'); notice.textContent = message; notice.classList.remove('hidden'); setTimeout(() => notice.classList.add('hidden'), 3000); } @if (session('voter_success')) showNotice(@json(session('voter_success'))); @endif @if (session('voter_error') || $errors->any()) openAddVoterModal(); @endif @if (session('voter_import_error')) openImportVoterModal(); @endif
        let latestVoterUpdate = @json($voters->max('updated_at')); setInterval(async () => { const modalOpen = document.querySelectorAll('#add-voter-modal.flex, #import-voter-modal.flex').length > 0; if (modalOpen) return; try { const response = await fetch("{{ route('admin.voter-management.data') }}", { headers: { Accept: 'application/json' }, credentials: 'same-origin' }); if (!response.ok) return; const data = await response.json(); if (data.latest_updated_at !== latestVoterUpdate || data.count !== {{ $voters->count() }}) { latestVoterUpdate = data.latest_updated_at; window.refreshLivePage?.(); } } catch (error) { console.warn('Live voter update check failed.', error); } }, 5000); document.getElementById('voter-search').addEventListener('input', (event) => { const query = event.target.value.toLowerCase(); document.querySelectorAll('.voter-row').forEach((row) => row.classList.toggle('hidden', !row.dataset.email.includes(query))); });
    </script>
</body>
</html>






























<script src="{{ asset('js/admin-navigation-cleanup.js') }}?v={{ filemtime(public_path('js/admin-navigation-cleanup.js')) }}"></script>
