<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f9f9ff">
    <title>Administrator Sign In · District 23 FYS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: {
            colors: { surface: '#f9f9ff', 'surface-container-high': '#dee8ff', 'surface-container-low': '#f0f3ff', 'surface-container-lowest': '#ffffff', 'primary-container': '#0e1c2f', secondary: '#115cb9' },
            fontFamily: { display: ['Outfit'], sans: ['Inter'] }
        } } };
    </script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body class="min-h-screen bg-[#f9f9ff] font-sans text-[#111c2d] antialiased">
    <header class="fixed inset-x-0 top-0 z-50 h-16 bg-[#0e1c2f] shadow-lg">
        <div class="flex h-full items-center px-4 sm:px-6">
            <img src="{{ asset('images/uecfi-logo.png') }}" alt="UECFI logo" class="h-10 w-10 shrink-0 rounded-lg object-contain">
            <div class="ml-3 truncate text-sm font-bold text-white sm:text-base">DISTRICT 23 FYS - ELECTION OF OFFICERS FOR 2027–2030</div>
        </div>
    </header>
    <aside class="fixed bottom-0 left-0 top-16 z-40 hidden w-64 bg-[#f0f3ff] py-6 lg:flex lg:flex-col">
        <div class="px-6 pb-4 text-[11px] font-bold uppercase tracking-wider text-[#44474c]">Navigation</div>
        <nav class="flex flex-col gap-1 px-2">
            <a class="flex items-center gap-2 rounded px-4 py-2.5 text-sm text-[#44474c] transition hover:bg-[#dee8ff] hover:text-[#111c2d]" href="{{ route('admin.login') }}"><span class="material-symbols-outlined text-xl">dashboard</span>Dashboard</a>
            <a class="flex items-center gap-2 rounded px-4 py-2.5 text-sm text-[#44474c] transition hover:bg-[#dee8ff] hover:text-[#111c2d]" href="{{ route('admin.login') }}"><span class="material-symbols-outlined text-xl">badge</span>Voter Management</a>
            <a class="flex items-center gap-2 rounded px-4 py-2.5 text-sm text-[#44474c] transition hover:bg-[#dee8ff] hover:text-[#111c2d]" href="{{ route('admin.login') }}"><span class="material-symbols-outlined text-xl">work</span>Position Management</a>
            <a class="flex items-center gap-2 rounded px-4 py-2.5 text-sm text-[#44474c] transition hover:bg-[#dee8ff] hover:text-[#111c2d]" href="{{ route('admin.login') }}"><span class="material-symbols-outlined text-xl">description</span>Results &amp; Document Preview</a>
            <a class="flex items-center gap-2 rounded px-4 py-2.5 text-sm text-[#44474c] transition hover:bg-[#dee8ff] hover:text-[#111c2d]" href="{{ route('admin.login') }}"><span class="material-symbols-outlined text-xl">history_edu</span>Audit Log</a>
        </nav>
    </aside>
    <main class="min-h-screen bg-[#f9f9ff] px-4 pb-10 pt-24 sm:px-6 lg:pl-72 lg:pr-8">
        <div class="mx-auto flex w-full max-w-[840px] flex-col">
            <div class="relative flex w-full flex-col">
            <div class="pointer-events-none absolute -left-32 -top-32 h-96 w-96 rounded-full bg-blue-500/5 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-32 -right-32 h-96 w-96 rounded-full bg-amber-300/20 blur-3xl"></div>
            <div class="relative z-10 flex w-full flex-col gap-6 sm:gap-8">
                <header class="flex items-center justify-between px-1">
                    <a href="{{ route('home') }}" class="flex items-center gap-2"><img src="{{ asset('images/uecfi-logo.png') }}" alt="Official Seal" class="h-10 w-10 shrink-0 rounded-full object-contain shadow-md"><span class="text-[11px] font-bold uppercase tracking-widest text-secondary">District 23 FYS</span></a>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-surface-container-high px-3 py-1 text-[10px] font-semibold tracking-wider text-[#44474c]"><span class="h-2 w-2 animate-pulse rounded-full bg-secondary"></span>Official session</span>
                </header>

                <section class="relative rounded-xl bg-white p-6 shadow-xl sm:p-10"><div class="absolute left-0 right-0 top-0 h-1.5 rounded-t-xl bg-secondary"></div>
                    <div class="mt-1 flex flex-col gap-1 text-center"><div class="mx-auto mb-1 flex h-16 w-16 items-center justify-center"><img src="{{ asset('images/uecfi-logo.png') }}" alt="Official Seal" class="h-16 w-16 object-contain"></div><h1 class="font-display text-3xl tracking-tight">Administrator Sign In</h1><p class="text-sm font-semibold text-[#44474c]">Official Access Portal</p></div>
                    @if (session('admin_error'))<div class="flex items-start gap-2 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800" role="alert"><span class="material-symbols-outlined text-lg">error</span><span>{{ session('admin_error') }}</span></div>@endif
                    <form class="mt-8 flex flex-col gap-5" id="admin-login-form" method="POST" action="{{ route('admin.login.submit') }}">
                        @csrf
                        <label class="flex flex-col gap-1.5 text-sm font-semibold" for="admin-email"><span class="flex items-center justify-between">Commission Identifier / Email <span class="text-xs font-normal text-secondary">Supervisory</span></span><span class="relative flex items-center"><span class="material-symbols-outlined pointer-events-none absolute left-3.5 text-xl text-[#44474c]">alternate_email</span><input class="h-11 w-full rounded-lg bg-surface-container-low pl-11 pr-4 text-sm outline-none shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-secondary/20" id="admin-email" name="email" value="{{ old('email') }}" placeholder="admin@district23fys.org" required type="email"></span></label>
                        <label class="flex flex-col gap-1.5 text-sm font-semibold" for="admin-password"><span class="flex items-center justify-between">Supervisory Access Credential <span class="text-xs font-normal text-[#44474c]">Masked</span></span><span class="relative flex items-center"><span class="material-symbols-outlined pointer-events-none absolute left-3.5 text-xl text-[#44474c]">key</span><input class="h-11 w-full rounded-lg bg-surface-container-low pl-11 pr-11 text-sm outline-none shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-secondary/20" id="admin-password" name="password" placeholder="••••••••••••••••" required type="password"><button aria-label="Toggle password visibility" class="absolute right-3 flex items-center justify-center rounded p-1 text-[#44474c] transition hover:text-[#111c2d]" id="toggle-pwd-visibility" type="button"><span class="material-symbols-outlined text-xl" id="pwd-icon">visibility</span></button></span></label>
                        <button class="mt-1 flex h-12 w-full items-center justify-center gap-2 rounded-lg bg-primary-container font-display text-lg tracking-wide text-white shadow-md transition hover:bg-secondary" id="signin-btn" type="submit"><span>SIGN IN</span><span class="material-symbols-outlined text-xl">lock_open</span></button>
                    </form>
                    <a href="{{ route('splash') }}" class="mt-5 block text-center text-sm font-semibold text-secondary transition hover:underline">Go to voter page</a>
                </section>
            </div>
            </div>
        </div>
    </main>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>


