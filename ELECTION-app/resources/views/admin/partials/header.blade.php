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
