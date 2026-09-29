<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Final Election Document · District 23 FYS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { background: white !important; padding: 0 !important; }
            [data-final-document] { box-shadow: none !important; margin: 0 !important; max-width: none !important; width: 100% !important; }
            [data-print-button] { display: none !important; }
        }
        /* This route is the printable document, not an admin shell. */
        body > header,
        body > aside,
        .mobile-admin-nav { display: none !important; }
    </style>
</head>
<body class="min-h-screen bg-white font-sans text-[#111c2d]">
    <main data-final-document class="mx-auto max-w-5xl bg-white p-8 shadow-none sm:p-12">
        <div class="mb-6 flex justify-end" data-print-button>
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded bg-[#115cb9] px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#0e1c2f]">
                <span aria-hidden="true">🖨</span> PRINT DOCUMENT
            </button>
        </div>
        <div class="text-center">
            <div class="flex items-center justify-center gap-4">
                <img src="{{ asset('images/uecfi-logo.png') }}" alt="UECFI logo" class="h-20 w-20 object-contain">
                <div>
                    <h1 class="text-xl font-bold uppercase">Union Espiritista Cristiana de Filipinas, Inc.</h1>
                    <p class="text-sm">Founded February 19, 1905, SEC. Registered January 23, 1909</p>
                    <p class="text-sm">SEC Registry No. 15147</p>
                    <p class="text-sm">District 23</p>
                </div>
            </div>
            <h2 class="mt-6 text-xl font-bold uppercase">Result of District 23 Federation of Youth Spiritist Officers</h2>
            <h3 class="text-xl font-bold">FOR 2027–2030</h3>
        </div>
        <table class="mt-8 w-full border-collapse border border-slate-500 text-sm">
            <tbody>
            @forelse ($positions as $result)
                <tr>
                    <th class="w-1/3 border border-slate-500 bg-slate-200 p-2 text-left align-top text-base font-bold">{{ $result['position']->name }}</th>
                    <td class="border border-slate-500 p-2 align-top">
                        <div class="grid grid-cols-1 gap-x-8 gap-y-1 sm:grid-cols-2">
                            @foreach ($result['winners'] as $winner)
                                <div class="flex min-h-6 items-start gap-1"><span class="shrink-0 font-normal">{{ $result['position']->seats > 1 ? ($loop->iteration).'.' : '' }}</span><span>{{ $winner->display_candidate_name }}</span></div>
                            @endforeach
                            @for ($seat = $result['winners']->count(); $seat < $result['position']->seats; $seat++)
                                <div class="flex min-h-6 items-start gap-1"><span class="shrink-0 font-normal">{{ $result['position']->seats > 1 ? ($seat + 1).'.' : '' }}</span><span></span></div>
                            @endfor
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td class="border border-slate-500 p-3" colspan="2">No positions have been created.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="mt-12 grid grid-cols-1 gap-12 text-center sm:grid-cols-2">
            <div><div class="mx-auto h-10 max-w-xs border-b border-slate-700"></div><p class="mt-2 font-bold uppercase">HMNA. PAOLA CONCUBIERTA</p><p class="text-sm uppercase">FYS District 23 President</p></div>
            <div><div class="mx-auto h-10 max-w-xs border-b border-slate-700"></div><p class="mt-2 font-bold uppercase">HMNA. BIANCA VANESSA F. SALADA</p><p class="text-sm uppercase">FYS District 23 Secretary</p></div>
        </div>
    </main>
</body>
</html>
