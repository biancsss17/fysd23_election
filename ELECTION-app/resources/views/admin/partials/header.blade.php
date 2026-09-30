<style data-admin-shell-styles>
    @media screen and (min-width: 1024px) {
        body > header { height: 80px; }
        body > header > div { height: 80px; min-height: 80px; }
        body > aside { top: 80px; width: 16rem; }
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
    }
    @media screen and (max-width: 480px) {
        body > header > div { gap: .5rem; }
        body > header .standard-admin-logo { width: 40px; height: 40px; }
        body > header .standard-admin-title { font-size: clamp(.66rem, 3.7vw, .9rem); }
        body > header form button { padding: .45rem .5rem; font-size: .62rem; }
        body > main { padding-left: .75rem; padding-right: .75rem; }
    }
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
<script>
    window.setInterval(() => {
        fetch('{{ route('admin.session.keepalive') }}', {
            credentials: 'same-origin',
            headers: { Accept: 'application/json' },
        }).catch(() => {});
    }, 5 * 60 * 1000);
</script>
