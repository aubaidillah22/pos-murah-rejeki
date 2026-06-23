<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} @isset($title) - {{ $title }} @endisset</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }

        /* Card utilities */
        .card { @apply bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700; }
        .card-hover { @apply card transition-all duration-200 hover:shadow-md hover:-translate-y-0.5; }

        /* Badge utilities */
        .badge { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium; }
        .badge-green { @apply badge bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300; }
        .badge-red { @apply badge bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300; }
        .badge-emerald { @apply badge bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300; }
        .badge-blue { @apply badge bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300; }
        .badge-amber { @apply badge bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300; }
        .badge-gray { @apply badge bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300; }

        /* Status dot */
        .status-dot { @apply w-2 h-2 rounded-full inline-block; }
        .status-dot-green { @apply status-dot bg-green-500; }
        .status-dot-red { @apply status-dot bg-red-500; }
        .status-dot-amber { @apply status-dot bg-amber-500; }

        /* Table cell utilities */
        .cell-primary { @apply px-4 py-3 text-sm font-medium text-gray-800 dark:text-gray-100; }
        .cell-secondary { @apply px-4 py-3 text-sm text-gray-500 dark:text-gray-400; }
        .cell-number { @apply px-4 py-3 text-sm font-medium tabular-nums; }
        .cell-amount { @apply cell-number text-emerald-600 dark:text-emerald-400 text-right; }
        .cell-amount-negative { @apply cell-number text-red-600 dark:text-red-400 text-right; }
        .cell-action { @apply px-4 py-3 text-right text-sm; }

        /* Table utility */
        .table-wrap { @apply card overflow-hidden; }
        .table-header { @apply bg-gray-50 dark:bg-gray-700/50; }
        .table-header th { @apply px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider; }
        .table-row { @apply hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors; }

        /* Empty state */
        .empty-state { @apply px-4 py-12 text-center; }
        .empty-state-icon { @apply text-4xl mb-2 text-gray-300 dark:text-gray-600; }
        .empty-state-text { @apply text-sm text-gray-400 dark:text-gray-500; }
        .empty-state-sub { @apply text-xs text-gray-300 dark:text-gray-600 mt-1; }

        /* Number styling */
        .tabular-nums { font-variant-numeric: tabular-nums; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #10b981; }
        .dark ::-webkit-scrollbar-thumb { background: #374151; }
        .dark ::-webkit-scrollbar-thumb:hover { background: #10b981; }
        * { scrollbar-width: thin; scrollbar-color: #d1d5db transparent; }
        .dark * { scrollbar-color: #374151 transparent; }

        /* Page transition */
        .page-enter { opacity: 0; }
        .page-enter-active { opacity: 1; transition: opacity .25s ease-out; }

        /* Dashboard card stagger animation */
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in-up 0.4s ease-out both;
        }
        @keyframes loading-bar { 0% { left: -30%; } 50% { left: 50%; } 100% { left: 130%; } }
        .animate-loading { animation: loading-bar 1.2s ease-in-out infinite; }
        #loading-bar { position: fixed; top: 0; left: 0; z-index: 9999; width: 100%; height: 3px; display: none; }
    </style>
    <script>
        function ensureLoadingBar() {
            if (document.getElementById('loading-bar')) return;
            var bar = document.createElement('div');
            bar.id = 'loading-bar';
            bar.innerHTML = '<div class="animate-loading absolute inset-0 bg-gradient-to-r from-emerald-400 via-emerald-600 to-emerald-400 rounded-full"></div>';
            document.documentElement.appendChild(bar);
        }
        function showLoadingBar() {
            ensureLoadingBar();
            document.getElementById('loading-bar').style.display = '';
        }
        function hideLoadingBar() {
            var bar = document.getElementById('loading-bar');
            if (bar) bar.style.display = 'none';
        }
        function applyDarkMode() {
            const isDark = localStorage.getItem('darkMode') === 'true' ||
                (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', isDark);
        }
        function animatePageIn() {
            const el = document.getElementById('page-content');
            if (!el) return;
            el.classList.remove('page-enter-active');
            void el.offsetWidth;
            el.classList.add('page-enter-active');
        }
        document.addEventListener('livewire:navigating', showLoadingBar);
        document.addEventListener('livewire:navigated', function() {
            hideLoadingBar();
            applyDarkMode();
            animatePageIn();
        });
        document.addEventListener('DOMContentLoaded', function() {
            hideLoadingBar();
            applyDarkMode();
            animatePageIn();
        });
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased">
    <div x-data="{ sidebarOpen: false, sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true', dark: localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches) }" class="min-h-screen">
        <!-- Mobile overlay -->
        <div x-show="sidebarOpen" x-cloak
             class="fixed inset-0 z-40 bg-black/50 lg:hidden"
             @@click="sidebarOpen = false">
        </div>

        <!-- Sidebar -->
        @php
            $settingsStoreName = \App\Models\Setting::getValue('store_name', config('app.name'));
        @endphp
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-emerald-800 dark:bg-gray-950 border-r border-emerald-700 dark:border-gray-800 transform transition-all duration-200"
               :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', sidebarCollapsed ? 'lg:-translate-x-full' : 'lg:translate-x-0']">
            <div class="flex items-center justify-between h-16 px-5 border-b border-emerald-700 dark:border-gray-800">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-7 h-7 rounded-lg bg-emerald-700 dark:bg-emerald-900/50 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-white dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                        </svg>
                    </div>
                    <h1 class="text-sm font-bold text-white truncate">{{ $settingsStoreName }}</h1>
                </div>
                <button @@click="sidebarOpen = false" class="lg:hidden p-1 rounded-lg text-emerald-200 dark:text-gray-500 hover:bg-emerald-700 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5">
                @php
                    $menuItems = [
                        ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                        ['name' => 'POS / Kasir', 'route' => 'pos', 'icon' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
                        ['name' => 'Produk', 'route' => 'products', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                        ['name' => 'Kategori', 'route' => 'categories', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
                        ['name' => 'Pelanggan', 'route' => 'customers', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
                        ['name' => 'Supplier', 'route' => 'suppliers', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                        ['name' => 'Pembelian', 'route' => 'purchases', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
                        ['name' => 'Stok Opname', 'route' => 'stock-opname', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                        ['name' => 'Transaksi', 'route' => 'transactions', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                        ['name' => 'Laporan', 'route' => 'reports', 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['name' => 'Pengeluaran', 'route' => 'expenses', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ];

                    if(auth()->user()->hasRole('Admin')) {
                        $menuItems[] = ['name' => 'Pengguna', 'route' => 'users', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-2.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z'];
                        $menuItems[] = ['name' => 'Outlet', 'route' => 'outlets', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'];
                        $menuItems[] = ['name' => 'Pengaturan', 'route' => 'settings', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'];
                    }
                @endphp

                @foreach($menuItems as $item)
                @php
                    $isActive = request()->routeIs($item['route'] . '*');
                @endphp
                <a href="{{ route($item['route']) }}" wire:navigate
                   class="group flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-full transition-all duration-150
                          {{ $isActive
                              ? 'bg-white dark:bg-emerald-900/25 text-emerald-800 dark:text-emerald-300 shadow-sm'
                              : 'text-emerald-100 dark:text-gray-400 hover:bg-emerald-700 dark:hover:bg-gray-800 hover:text-white dark:hover:text-gray-200' }}">
                    <svg class="w-5 h-5 flex-shrink-0 transition-all duration-150
                              {{ $isActive ? 'text-emerald-700 dark:text-emerald-400' : 'text-emerald-200 dark:text-gray-500 group-hover:text-white dark:group-hover:text-gray-300' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                    </svg>
                    <span>{{ $item['name'] }}</span>
                </a>
                @endforeach
            </nav>

        </aside>

        <!-- Main content -->
        <div :class="sidebarCollapsed ? 'lg:pl-0' : 'lg:pl-64'" class="transition-all duration-200">
            <!-- Top bar -->
            <header x-data="{ scrolled: false }"
                    x-init="let t; window.addEventListener('scroll', () => { t && cancelAnimationFrame(t); t = requestAnimationFrame(() => scrolled = window.scrollY > 24); }, { passive: true })"
                    :class="scrolled ? 'shadow-lg bg-white/90 dark:bg-gray-900/90' : 'shadow-sm bg-white/75 dark:bg-gray-900/80'"
                    class="sticky top-0 z-30 h-14 md:h-16 backdrop-blur-xl border-b border-gray-200/50 dark:border-gray-700/50 transition-all duration-200">
                <div class="flex items-center justify-between h-full px-4 md:px-6">
                    <div class="flex items-center gap-1.5">
                        <button @@click="sidebarOpen = true"
                                class="flex lg:hidden p-1.5 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <button @@click="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('sidebarCollapsed', sidebarCollapsed)"
                                class="hidden lg:flex items-center gap-1.5 px-2 py-1.5 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                :title="sidebarCollapsed ? 'Buka Sidebar' : 'Tutup Sidebar'">
                            <template x-if="!sidebarCollapsed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                                </svg>
                            </template>
                            <template x-if="sidebarCollapsed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                                </svg>
                            </template>
                        </button>
                        <div class="ml-1.5">
                            <h2 class="text-sm md:text-base font-semibold text-gray-800 dark:text-gray-100 leading-tight">@yield('page-title', $title ?? 'Dashboard')</h2>
                            <time x-show="!scrolled" x-cloak x-transition:enter.opacity.duration.200ms class="text-[11px] text-gray-400 dark:text-gray-500 hidden md:block">{{ now()->translatedFormat('l, d F Y') }}</time>
                        </div>
                    </div>

                    <div class="flex items-center gap-1">
                        <button @@click="dark = !dark; localStorage.setItem('darkMode', dark); document.documentElement.classList.toggle('dark')"
                                class="p-1.5 md:p-2 rounded-lg text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                :title="dark ? 'Mode Terang' : 'Mode Gelap'">
                            <template x-if="!dark">
                                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                            </template>
                            <template x-if="dark">
                                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </template>
                        </button>

                        <span class="mx-1 w-px h-5 bg-gray-200 dark:bg-gray-700"></span>

                        <div x-data="{ open: false }" class="relative">
                            <button @@click="open = !open" @@click.outside="open = false"
                                    class="flex items-center gap-2 pl-2 pr-2.5 py-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                <span class="w-7 h-7 rounded-full bg-emerald-600 flex items-center justify-center text-xs font-bold text-white shadow-sm">
                                    {{ substr(auth()->user()->name, 0, 2) }}
                                </span>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-200 hidden md:block">{{ auth()->user()->name }}</span>
                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="open" x-cloak
                                 @@click.outside="open = false"
                                 class="absolute right-0 mt-2 w-52 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-1.5 z-50">
                                <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-400 truncate mt-0.5">{{ auth()->user()->email }}</p>
                                    @if(auth()->user()->roles->isNotEmpty())
                                    <span class="inline-block mt-1.5 text-[10px] font-medium px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                        {{ auth()->user()->roles->first()->name }}
                                    </span>
                                    @endif
                                </div>
                                <a href="{{ route('settings') }}"
                                   class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Pengaturan
                                </a>
                                <hr class="border-gray-100 dark:border-gray-700">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main id="page-content" class="p-4 md:p-6 page-enter">
                <div class="max-w-7xl mx-auto">
                    @if(session('success'))
                    <div class="bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-300 dark:border-emerald-700 text-emerald-700 dark:text-emerald-300 px-4 py-3 rounded-lg mb-4 flex items-center justify-between">
                        <span>{{ session('success') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="bg-red-50 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg mb-4 flex items-center justify-between">
                        <span>{{ session('error') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">&times;</button>
                    </div>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
