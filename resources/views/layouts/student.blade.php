<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
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
        .student-surface {
            background:
                radial-gradient(circle at top left, rgba(79, 70, 229, 0.08), transparent 28%),
                radial-gradient(circle at top right, rgba(14, 165, 233, 0.10), transparent 24%),
                linear-gradient(180deg, #f8f9ff 0%, #eef3ff 100%);
        }
        .student-card {
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.05);
        }
        .student-card-hover:hover {
            box-shadow: 0px 8px 30px rgba(79, 70, 229, 0.10);
        }
        .student-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .student-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .student-scrollbar::-webkit-scrollbar-thumb {
            background: #d3e4fe;
            border-radius: 9999px;
        }
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
@php($focusMode = request()->routeIs('student.modules.show', 'student.quizzes.take'))
<body class="student-surface min-h-screen text-slate-900 antialiased">
    <div class="flex min-h-screen">
        <aside class="sticky top-0 hidden h-screen w-[280px] flex-col border-r border-slate-200/80 bg-[#eef3ff] lg:flex {{ $focusMode ? 'lg:hidden' : '' }}">
            <div class="px-6 pt-7">
                <a href="{{ route('student.dashboard') }}" class="inline-flex items-center gap-3">
                    <span class="inline-flex h-11 min-w-11 items-center justify-center rounded-2xl bg-indigo-600 px-3 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20">GML</span>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-indigo-700">Learning Portal</p>
                        <h1 class="text-2xl font-semibold tracking-tight text-indigo-700 break-all">{{ config('app.name') }}</h1>
                        <p class="text-sm text-slate-500">Platform pembelajaran geografi digital</p>
                    </div>
                </a>
            </div>

            <nav class="mt-8 flex-1 space-y-1 px-3">
                @foreach([
                    ['label' => 'Dashboard', 'route' => 'student.dashboard', 'icon' => 'dashboard'],
                    ['label' => 'Materi', 'route' => 'student.courses', 'icon' => 'menu_book'],
                    ['label' => 'Riwayat Belajar', 'route' => 'student.learning-history', 'icon' => 'history'],
                    ['label' => 'Riwayat Kuis', 'route' => 'student.quiz-history', 'icon' => 'quiz'],
                    ['label' => 'Profil', 'route' => 'student.profile', 'icon' => 'person'],
                ] as $item)
                    @php($active = request()->routeIs($item['route'] . '*'))
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-4 rounded-r-2xl px-4 py-3 transition-all {{ $active ? 'border-l-4 border-indigo-600 bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <span class="material-symbols-outlined text-[22px] {{ $active ? 'text-white' : 'text-slate-500' }}">{{ $item['icon'] }}</span>
                        <span class="text-[15px] font-medium">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-slate-200/80 px-3 py-4">
                <form action="{{ route('student.logout') }}" method="POST" class="mt-1">
                    @csrf
                    <button
                        type="submit"
                        class="flex w-full items-center gap-4 rounded-r-2xl px-4 py-3 text-slate-600 transition hover:bg-red-50 hover:text-red-700"
                    >
                        <span class="material-symbols-outlined text-[22px] text-slate-500">logout</span>
                        <span class="text-sm font-medium">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-40 border-b border-slate-200/70 bg-white/90 backdrop-blur">
                <div class="flex h-16 items-center gap-4 px-4 sm:px-6 lg:px-8">
                    @if(! $focusMode)
                        <button class="rounded-2xl p-2 text-slate-700 transition hover:bg-slate-100 lg:hidden" type="button">
                            <span class="material-symbols-outlined">menu</span>
                        </button>
                    @endif

                    <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3 {{ $focusMode ? '' : 'lg:hidden' }}">
                        <span class="inline-flex h-10 min-w-10 items-center justify-center rounded-2xl bg-indigo-600 px-2 text-xs font-semibold text-white">GML</span>
                        <div>
                            <h1 class="text-lg font-semibold leading-none text-indigo-700 break-all">{{ config('app.name') }}</h1>
                            <p class="text-xs text-slate-500">Platform pembelajaran geografi digital</p>
                        </div>
                    </a>

                    <div class="ml-auto flex items-center gap-2 sm:gap-3">
                        <button class="rounded-full p-2 text-slate-700 transition hover:bg-slate-100" type="button" aria-label="Notifications">
                            <span class="material-symbols-outlined">notifications</span>
                        </button>
                        <button class="rounded-full p-2 text-slate-700 transition hover:bg-slate-100" type="button" aria-label="Messages">
                            <span class="material-symbols-outlined">mail</span>
                        </button>

                        <div class="group relative">
                            <button class="flex items-center gap-2 rounded-full px-2 py-1.5 transition hover:bg-slate-100" type="button">
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-indigo-700 text-sm font-semibold text-white shadow-sm">
                                    {{ strtoupper(mb_substr(auth()->user()->name ?? 'S', 0, 1)) }}
                                </span>
                                <span class="hidden max-w-[10rem] truncate text-sm font-medium text-slate-800 sm:inline">{{ auth()->user()->name ?? 'Student' }}</span>
                                <span class="material-symbols-outlined text-[20px] text-slate-500">expand_more</span>
                            </button>

                            <div class="invisible absolute right-0 top-full z-50 mt-2 w-64 rounded-[18px] border border-slate-200 bg-white p-2 opacity-0 shadow-lg shadow-slate-200/70 transition-all duration-150 group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100">
                                <div class="rounded-[14px] bg-slate-50 px-4 py-3">
                                    <p class="text-sm font-semibold text-slate-900">{{ auth()->user()->name ?? 'Student' }}</p>
                                    <p class="mt-1 text-xs text-slate-500 break-all">{{ auth()->user()->username ?? '' }}</p>
                                </div>
                                <a href="{{ route('student.profile') }}" class="mt-2 flex items-center gap-3 rounded-[14px] px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-indigo-700">
                                    <span class="material-symbols-outlined text-[20px] text-slate-500">person</span>
                                    Profil saya
                                </a>
                                <form action="{{ route('student.logout') }}" method="POST" class="mt-1">
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
                <div class="mx-auto flex w-full max-w-[1440px] flex-col gap-6 {{ $focusMode ? 'lg:max-w-[1600px]' : '' }}">
                    {{ $slot }}
                </div>
            </main>

            @unless($focusMode)
                <footer class="border-t border-slate-200/80 bg-[#dce8fd]">
                    <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-6 text-sm text-slate-700 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                        <div>
                            <p class="font-semibold text-slate-900 break-all">{{ config('app.name') }}</p>
                            <p>© 2026 {{ config('app.name') }}. All rights reserved.</p>
                        </div>
                        <div class="flex flex-wrap gap-4 text-slate-600">
                            <a href="#" class="transition hover:text-indigo-700">Privacy Policy</a>
                            <a href="#" class="transition hover:text-indigo-700">Terms of Service</a>
                            <a href="#" class="transition hover:text-indigo-700">Contact Support</a>
                            <a href="#" class="transition hover:text-indigo-700">Alumni Network</a>
                        </div>
                    </div>
                </footer>
            @endunless
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
