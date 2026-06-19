<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Preview Guru' }} | {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            line-height: 1;
            vertical-align: middle;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        .student-preview-surface {
            background:
                radial-gradient(circle at top left, rgba(195, 72, 45, 0.08), transparent 28%),
                radial-gradient(circle at top right, rgba(217, 116, 82, 0.10), transparent 24%),
                linear-gradient(180deg, #fffaf6 0%, #f7eee8 100%);
        }
    </style>
</head>
<body class="student-preview-surface min-h-screen text-slate-900 antialiased">
    <div class="flex min-h-screen flex-col">
        <header class="sticky top-0 z-40 border-b border-slate-200/70 bg-white/90 backdrop-blur">
            <div class="flex h-16 items-center gap-4 px-4 sm:px-6 lg:px-8">
                <a href="{{ route('guru.dashboard') }}" class="flex items-center gap-3">
                    <span class="inline-flex h-10 min-w-10 items-center justify-center rounded-2xl bg-[#c84a2f] px-2 text-xs font-semibold text-white">GML</span>
                    <div>
                        <h1 class="text-lg font-semibold leading-none text-[#b64027] break-all">{{ config('app.name') }}</h1>
                        <p class="text-xs text-slate-500">Platform pembelajaran geografi digital</p>
                    </div>
                </a>

                <span class="hidden rounded-full bg-[#f8ded2] px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-[#b64027] sm:inline-flex">
                    Preview Guru
                </span>

                <div class="ml-auto flex items-center gap-2 sm:gap-3">
                    <a href="{{ route('guru.courses.index') }}" class="hidden rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-[#db8b73] hover:text-[#b64027] sm:inline-flex">
                        Kembali ke Guru
                    </a>

                    <div class="group relative">
                        <button class="flex items-center gap-2 rounded-full px-2 py-1.5 transition hover:bg-slate-100" type="button">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-[#a33a24] text-sm font-semibold text-white shadow-sm">
                                {{ strtoupper(mb_substr(auth()->user()->name ?? 'G', 0, 1)) }}
                            </span>
                            <span class="hidden max-w-[10rem] truncate text-sm font-medium text-slate-800 sm:inline">{{ auth()->user()->name ?? 'Guru' }}</span>
                            <span class="material-symbols-outlined text-[20px] text-slate-500">expand_more</span>
                        </button>

                        <div class="invisible absolute right-0 top-full z-50 mt-2 w-64 rounded-[18px] border border-slate-200 bg-white p-2 opacity-0 shadow-lg shadow-slate-200/70 transition-all duration-150 group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100">
                            <div class="rounded-[14px] bg-slate-50 px-4 py-3">
                                <p class="text-sm font-semibold text-slate-900">{{ auth()->user()->name ?? 'Guru' }}</p>
                                <p class="mt-1 text-xs text-slate-500 break-all">{{ auth()->user()->username ?? '' }}</p>
                            </div>
                            <a href="{{ route('guru.courses.index') }}" class="mt-2 flex items-center gap-3 rounded-[14px] px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-[#b64027]">
                                <span class="material-symbols-outlined text-[20px] text-slate-500">dashboard</span>
                                Area guru
                            </a>
                            <form action="{{ route('guru.logout') }}" method="POST" class="mt-1">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-3 rounded-[14px] px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-red-50 hover:text-red-700">
                                    <span class="material-symbols-outlined text-[20px] text-slate-500">logout</span>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto flex w-full max-w-[1600px] flex-col gap-6">
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
