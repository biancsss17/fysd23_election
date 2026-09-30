<header class="fixed inset-x-0 top-0 z-50 h-16 bg-[#0e1c2f] shadow-lg">
    <div class="flex h-full items-center justify-between px-4 sm:px-6">
        <div class="flex min-w-0 items-center gap-3">
            <img src="{{ asset('images/uecfi-logo.png') }}" alt="UECFI logo" class="h-10 w-10 shrink-0 rounded-lg object-contain">
            <div class="truncate text-sm font-bold text-white sm:text-base">
                DISTRICT 23 FYS - ELECTION OF OFFICERS FOR 2027–2030
            </div>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}">
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
    }, 10 * 60 * 1000);
</script>
