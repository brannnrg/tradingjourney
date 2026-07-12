<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Trading Journey') }} — @yield('title', 'Dashboard')</title>
    <meta name="description" content="Personal crypto trading journal — track your trades, equity curve, and performance stats.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: { 400: '#4ade80', 500: '#22c55e', 600: '#16a34a' },
                        dark: { 900: '#0a0d14', 800: '#111827', 700: '#1f2937', 600: '#374151' }
                    }
                }
            }
        }
    </script>

    <style>
        body { background-color: #0a0d14; color: #f1f5f9; font-family: 'Inter', sans-serif; }
        .glass { background: rgba(17,24,39,0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.07); }
        .glass-light { background: rgba(31,41,55,0.5); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.05); }
        .gradient-text { background: linear-gradient(135deg, #22c55e, #16a34a); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .sidebar-link { transition: all 0.2s; border-radius: 0.5rem; }
        .sidebar-link:hover { background: rgba(34,197,94,0.1); color: #4ade80; }
        .sidebar-link.active { background: rgba(34,197,94,0.15); color: #4ade80; border-left: 3px solid #22c55e; }
        .btn-primary { background: linear-gradient(135deg, #22c55e, #16a34a); transition: all 0.2s; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 20px rgba(34,197,94,0.4); }
        .card { background: #111827; border: 1px solid rgba(255,255,255,0.06); border-radius: 1rem; }
        .profit { color: #4ade80; }
        .loss { color: #f87171; }
        .badge-strategy { background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3); }
        .badge-emotion  { background: rgba(250,204,21,0.15); color: #fbbf24; border: 1px solid rgba(250,204,21,0.3); }
        .badge-mistake  { background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.3); }
        .input-field { background: #1f2937; border: 1px solid rgba(255,255,255,0.1); color: #f1f5f9; border-radius: 0.5rem; transition: border-color 0.2s; }
        .input-field:focus { outline: none; border-color: #22c55e; box-shadow: 0 0 0 2px rgba(34,197,94,0.2); }
        .input-field::placeholder { color: #6b7280; }
        select.input-field option { background: #1f2937; }
        ::-webkit-scrollbar { width: 5px; } ::-webkit-scrollbar-track { background: #111827; } ::-webkit-scrollbar-thumb { background: #374151; border-radius: 99px; }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen flex">

    {{-- Sidebar --}}
    <aside class="w-64 min-h-screen glass flex flex-col fixed left-0 top-0 z-30" style="border-right: 1px solid rgba(255,255,255,0.07);">
        {{-- Logo --}}
        <div class="p-6 border-b border-white/5">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl btn-primary flex items-center justify-center text-white font-bold text-lg">₿</div>
                <div>
                    <div class="font-bold text-white text-sm leading-tight">Trading Journey</div>
                    <div class="text-xs text-gray-500">Crypto Tracker</div>
                </div>
            </a>
        </div>

        {{-- User Info --}}
        <div class="px-4 py-3 border-b border-white/5">
            <div class="flex items-center gap-3 p-2">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-white text-sm font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</div>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 p-4 space-y-1">
            <p class="text-xs text-gray-600 uppercase tracking-widest px-3 mb-2 mt-1">Menu</p>

            <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            <a href="{{ route('trading-accounts.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('trading-accounts.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Akun Trading
            </a>

            <a href="{{ route('trades.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('trades.*') ? 'active' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Trade Log
            </a>

            <div class="pt-3 border-t border-white/5 mt-3">
                <p class="text-xs text-gray-600 uppercase tracking-widest px-3 mb-2">Akun</p>
                <a href="{{ route('profile.edit') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 text-sm text-gray-400 hover:text-red-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    {{-- Main Content --}}
    <main class="flex-1 ml-64 min-h-screen">
        {{-- Top Bar --}}
        <header class="sticky top-0 z-20 glass px-8 py-4 flex items-center justify-between" style="border-bottom: 1px solid rgba(255,255,255,0.07);">
            <div>
                <h1 class="text-lg font-semibold text-white">@yield('header', 'Dashboard')</h1>
                @hasSection('subheader')
                    <p class="text-xs text-gray-500 mt-0.5">@yield('subheader')</p>
                @endif
            </div>
            <div class="flex items-center gap-3">
                @yield('header-actions')
            </div>
        </header>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mx-8 mt-4 px-4 py-3 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('info'))
            <div class="mx-8 mt-4 px-4 py-3 rounded-lg bg-blue-500/10 border border-blue-500/20 text-blue-400 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('info') }}
            </div>
        @endif

        {{-- Page Content --}}
        <div class="p-8">
            @yield('content')
        </div>
    </main>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @stack('scripts')
</body>
</html>
