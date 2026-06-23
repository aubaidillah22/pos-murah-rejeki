<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ config('app.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css'])
    <script>
        var savedTheme = localStorage.getItem('themeColor') || 'green';
        document.documentElement.classList.add('theme-' + savedTheme);
        if (localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .login-pattern { background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,.08) 1px, transparent 0); background-size: 24px 24px; }
        .dark .login-pattern { background-image: radial-gradient(circle at 1px 1px, rgba(0,0,0,.15) 1px, transparent 0); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-emerald-700 via-emerald-600 to-teal-500 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <div class="login-pattern fixed inset-0 pointer-events-none"></div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8 w-full max-w-md relative">
        <!-- Theme & Dark mode toggles -->
        <div class="absolute top-4 right-4 flex items-center gap-1">
            <button onclick="var t={green:'red',red:'blue',blue:'purple',purple:'orange',orange:'pink',pink:'green'},n=t[localStorage.getItem('themeColor')||'green'];localStorage.setItem('themeColor',n);document.documentElement.classList.remove('theme-green','theme-red','theme-blue','theme-purple','theme-orange','theme-pink');document.documentElement.classList.add('theme-'+n);this.querySelector('svg').style.transform='rotate('+Math.random()*360+'deg)'"
                    class="p-1.5 rounded-lg text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                    title="Ganti Tema">
                <svg class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                </svg>
            </button>
            <button onclick="document.documentElement.classList.toggle('dark');localStorage.setItem('darkMode',document.documentElement.classList.contains('dark'))"
                    class="p-1.5 rounded-lg text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                    title="Mode Gelap/Terang">
                <svg class="w-4 h-4 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
                <svg class="w-4 h-4 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </button>
        </div>

        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/50 rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner">
                <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ \App\Models\Setting::getValue('store_name', config('app.name')) }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sistem POS Toko Bangunan</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4" onsubmit="document.getElementById('login-btn').disabled=true;document.getElementById('login-btn').innerHTML='<svg class=\'animate-spin w-5 h-5 mx-auto\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\'/></svg>'">
            @csrf

            @if($errors->any())
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="input">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                <div class="relative">
                    <input id="password" type="password" name="password" required
                           class="input pr-10">
                    <button type="button" onclick="var p=document.getElementById('password');var i=this.querySelector('svg:first-child');var e=this.querySelector('svg:last-child');if(p.type==='password'){p.type='text';i.classList.add('hidden');e.classList.remove('hidden')}else{p.type='password';i.classList.remove('hidden');e.classList.add('hidden')}"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 dark:border-gray-600 text-emerald-600 focus:ring-emerald-500 dark:bg-gray-700">
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Ingat Saya</span>
                </label>
            </div>

            <button id="login-btn" type="submit" class="btn-primary w-full flex items-center justify-center gap-2 px-4 py-3 text-sm">
                Masuk
            </button>
        </form>

        <div class="mt-6 text-center text-[11px] text-gray-400 dark:text-gray-600">
            Sistem Kasir (Point of Sale) Toko Bangunan
        </div>
    </div>
</body>
</html>