<style data-admin-shell-styles>
    @media screen and (min-width: 1024px) {
        body > header { height: 80px; }
        body > header > div { height: 80px; min-height: 80px; }
        body > header .standard-admin-logo { width: 56px; height: 56px; border-radius: 9999px; }
        body > header .standard-admin-title { font-size: 24px; line-height: 1.15; font-weight: 700; letter-spacing: -.01em; text-transform: uppercase; }
        body > aside { top: 80px; width: 16rem; }
        body > aside nav { display: flex; flex-direction: column; gap: .25rem; padding: 0 .5rem; }
        body > aside nav a { display: flex !important; align-items: center !important; gap: .75rem !important; border-radius: .25rem !important; padding: .75rem 1rem !important; font-size: 1rem !important; line-height: 1.25 !important; color: #263143 !important; text-decoration: none !important; transition: background-color .15s ease, color .15s ease !important; }
        body > aside nav a:hover { background: #dee8ff !important; color: #111c2d !important; }
        body > aside nav a[aria-current="page"], body > aside nav a.active { border-left: 4px solid #ffdf98 !important; background: #dee8ff !important; color: #115cb9 !important; padding-left: .75rem !important; font-weight: 700 !important; }
        body > aside nav a .material-symbols-outlined { flex: 0 0 auto; font-size: 22px !important; line-height: 1 !important; }
        body > main { margin-left: 0; padding-top: 6rem; padding-left: 18rem; padding-right: 2rem; }
    }
    @media screen and (max-width: 1023px) {
        html, body { min-height: 100%; overflow-x: hidden; overflow-y: auto; }
        body { padding-bottom: 4.75rem; }
        body > header { height: auto; min-height: 5rem; }
        body > header > div { min-height: 5rem; height: auto; padding: .65rem .75rem; align-items: center; }
        body > header > div > div:first-child { min-width: 0; flex: 1 1 auto; }
        body > header .standard-admin-logo { width: 44px; height: 44px; }
        body > header .standard-admin-title { display: block; min-width: 0; max-width: none; overflow-wrap: anywhere; white-space: normal; font-size: clamp(.72rem, 3.9vw, 1.1rem); line-height: 1.12; }
        body > header form { display: block; }
        body > header form button { padding: .55rem .65rem; font-size: .7rem; white-space: nowrap; }
        body > header > div > div:last-child > span { display: none; }
        body > aside { display: none; }
        body > main { margin-left: 0; padding-top: 6rem; padding-left: 1rem; padding-right: 1rem; min-width: 0; width: 100%; max-width: 100%; overflow-wrap: anywhere; }
        body > main > div, body > main section, body > main article { max-width: 100%; min-width: 0; }
        body > main h1 { font-size: clamp(1.35rem, 6vw, 2rem); line-height: 1.15; }
        body > main h2 { font-size: clamp(1.15rem, 5vw, 1.5rem); line-height: 1.2; }
        .mobile-admin-nav { position: fixed; inset: auto 0 0; z-index: 60; display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: .25rem; padding: .45rem .5rem calc(.45rem + env(safe-area-inset-bottom)); background: #0e1c2f; box-shadow: 0 -2px 12px rgba(0,0,0,.18); }
        .mobile-admin-nav a { min-width: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: .15rem; min-height: 3.2rem; border-radius: .5rem; color: #d8e3fb; font-size: .68rem; line-height: 1; text-decoration: none; }
        .mobile-admin-nav a:hover, .mobile-admin-nav a:focus-visible, .mobile-admin-nav a.active { background: #1d3557; color: #fff; outline: none; }
        .mobile-admin-nav .material-symbols-outlined { font-size: 1.3rem; }
    }
    @media screen and (max-width: 480px) {
        body > header > div { gap: .5rem; }
        body > header .standard-admin-logo { width: 40px; height: 40px; }
        body > header .standard-admin-title { font-size: clamp(.66rem, 3.7vw, .9rem); }
        body > header form button { padding: .45rem .5rem; font-size: .62rem; }
        body > main { padding-left: .75rem; padding-right: .75rem; }
        .mobile-admin-nav a { font-size: .6rem; }
        .mobile-admin-nav .material-symbols-outlined { font-size: 1.15rem; }
    }
    @media screen and (min-width: 1024px) { .mobile-admin-nav { display: none; } }
</style>
<header class="fixed inset-x-0 top-0 z-50 h-20 bg-[#0e1c2f] shadow-lg">
    <div class="flex h-20 items-center justify-between gap-3 px-4 sm:px-6">
        <div class="flex min-w-0 flex-1 items-center gap-3">
            <img src="{{ asset('images/uecfi-logo.png') }}" alt="UECFI logo" class="standard-admin-logo h-14 w-14 shrink-0 rounded-full object-contain">
            <div class="standard-admin-title min-w-0 text-[24px] font-bold uppercase leading-[1.15] tracking-tight text-white">
                DISTRICT 23 FYS - ELECTION OF OFFICERS FOR 2027–2030
            </div>
        </div>
        <form class="shrink-0" method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button class="rounded bg-[#f0f3ff] px-3 py-2 text-xs font-bold text-[#111c2d] transition hover:bg-[#ffdad6]" type="submit">Sign Out</button>
        </form>
    </div>
</header>
<nav class="mobile-admin-nav" aria-label="Admin navigation">
    <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><span class="material-symbols-outlined">dashboard</span><span>Dashboard</span></a>
    <a class="{{ request()->routeIs('admin.voter-management*') ? 'active' : '' }}" href="{{ route('admin.voter-management') }}"><span class="material-symbols-outlined">badge</span><span>Voters</span></a>
    <a class="{{ request()->routeIs('admin.position-management*') ? 'active' : '' }}" href="{{ route('admin.position-management') }}"><span class="material-symbols-outlined">work</span><span>Positions</span></a>
    <a class="{{ request()->routeIs('admin.results-document-preview*') ? 'active' : '' }}" href="{{ route('admin.results-document-preview') }}"><span class="material-symbols-outlined">description</span><span>Results</span></a>
    <a class="{{ request()->routeIs('admin.audit-log*') ? 'active' : '' }}" href="{{ route('admin.audit-log') }}"><span class="material-symbols-outlined">history_edu</span><span>Audit</span></a>
</nav>
<script>
    window.setInterval(() => {
        fetch('{{ route('admin.session.keepalive') }}', {
            credentials: 'same-origin',
            headers: { Accept: 'application/json' },
        }).catch(() => {});
    }, 5 * 60 * 1000);
</script>
