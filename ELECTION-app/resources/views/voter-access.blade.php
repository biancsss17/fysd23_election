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

        <form class="mt-6 space-y-4" method="POST" action="{{ route('voter-access.verify') }}">
            @csrf
            <label class="block text-sm font-bold" for="email">Registered email address
                <input class="mt-2 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 py-3 font-normal outline-none transition focus:border-[#115cb9] focus:ring-2 focus:ring-[#115cb9]/20" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="name@example.com" required autofocus>
            </label>
            @error('email')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            <button class="flex w-full items-center justify-center gap-2 rounded-lg bg-violet-700 px-5 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-violet-800" type="submit">Submit</button>
        </form>

        <a class="mt-4 block text-center text-sm font-semibold text-[#115cb9]" href="{{ route('home') }}">Back to overview</a>
    </main>
</body>
</html>
