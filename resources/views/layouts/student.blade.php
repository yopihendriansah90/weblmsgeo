<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Web LMS SIG') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-neutral-50 text-neutral-950 antialiased">
    <div class="border-b border-neutral-200 bg-white">
        <div class="mx-auto flex max-w-6xl flex-col gap-3 px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ route('student.dashboard') }}" class="text-lg font-semibold">Web LMS SIG</a>
            <nav class="flex flex-wrap items-center gap-2 text-sm">
                <a class="rounded-md px-3 py-2 hover:bg-neutral-100" href="{{ route('student.dashboard') }}">Dashboard</a>
                <a class="rounded-md px-3 py-2 hover:bg-neutral-100" href="{{ route('student.courses') }}">Kursus</a>
                <a class="rounded-md px-3 py-2 hover:bg-neutral-100" href="{{ route('student.learning-history') }}">Riwayat Belajar</a>
                <a class="rounded-md px-3 py-2 hover:bg-neutral-100" href="{{ route('student.quiz-history') }}">Riwayat Kuis</a>
                <a class="rounded-md px-3 py-2 hover:bg-neutral-100" href="{{ route('student.profile') }}">Profil</a>
                <form method="POST" action="{{ route('student.logout') }}">
                    @csrf
                    <button class="rounded-md border border-neutral-300 px-3 py-2 hover:bg-neutral-100" type="submit">Logout</button>
                </form>
            </nav>
        </div>
    </div>

    <main class="mx-auto max-w-6xl px-4 py-6">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
