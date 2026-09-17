<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trading Journey Tracker — Personal Crypto Journal</title>
    <meta name="description" content="Catat, analisis, dan kuasai perjalanan trading crypto Anda dari modal $100. Free, private, powerful.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #0a0d14; color: #f1f5f9; font-family: 'Inter', sans-serif; }
        .gradient-text { background: linear-gradient(135deg, #22c55e 0%, #16a34a 50%, #15803d 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .btn-primary { background: linear-gradient(135deg, #22c55e, #16a34a); transition: all 0.2s; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(34,197,94,0.4); }
        .card { background: #111827; border: 1px solid rgba(255,255,255,0.06); border-radius: 1rem; }
        .glass { background: rgba(17,24,39,0.6); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.07); }
        .profit { color: #4ade80; }
        .loss { color: #f87171; }
        .glow-green { box-shadow: 0 0 40px rgba(34,197,94,0.15); }
    </style>
</head>
<body class="min-h-screen">

    {{-- Navbar --}}
    <nav class="glass sticky top-0 z-50 px-6 py-4 flex items-center justify-between" style="border-bottom: 1px solid rgba(255,255,255,0.07);">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl btn-primary flex items-center justify-center text-white font-bold">₿</div>
            <span class="font-bold text-white text-sm">Trading Journey</span>
        </div>
        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}"
                   class="btn-primary px-4 py-2 rounded-lg text-white text-sm font-semibold">
                    Dashboard →
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="px-4 py-2 rounded-lg text-gray-300 text-sm hover:text-white transition-colors">
                    Login
                </a>
                <a href="{{ route('register') }}"
                   class="btn-primary px-4 py-2 rounded-lg text-white text-sm font-semibold">
                    Mulai Gratis
                </a>
            @endauth
        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="max-w-5xl mx-auto px-6 pt-20 pb-24 text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium mb-8"
             style="background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.2); color: #4ade80;">
            🚀 Personal Crypto Trading Journal
        </div>

        <h1 class="text-5xl md:text-6xl font-black text-white mb-6 leading-tight">
            Track <span class="gradient-text">$100 Challenge</span><br>
            sampai profit konsisten
        </h1>

        <p class="text-gray-400 text-lg max-w-2xl mx-auto mb-10 leading-relaxed">
            Catat setiap trade, analisis equity curve, temukan setup terbaik Anda, dan pantau psikologi trading — semua dalam satu dashboard yang simpel dan powerful.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            @auth
                <a href="{{ route('dashboard') }}"
                   class="btn-primary px-8 py-4 rounded-xl text-white font-bold text-base glow-green">
                    Buka Dashboard →
                </a>
            @else
                <a href="{{ route('register') }}"
                   class="btn-primary px-8 py-4 rounded-xl text-white font-bold text-base glow-green">
                    Mulai Sekarang — Gratis
                </a>
                <a href="{{ route('login') }}"
                   class="px-8 py-4 rounded-xl text-gray-300 font-medium text-base"
                   style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                    Sudah punya akun
                </a>
            @endauth
        </div>
    </section>

    {{-- Stats Preview --}}
    <section class="max-w-5xl mx-auto px-6 pb-16">
        <div class="card p-6 glow-green">
            <div class="flex items-center justify-between mb-4 pb-4" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                <div class="flex items-center gap-2 text-sm font-semibold text-gray-300">
                    <div class="w-6 h-6 rounded btn-primary flex items-center justify-center text-xs text-white">₿</div>
                    Binance Spot — USDT
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full" style="background: rgba(34,197,94,0.1); color: #4ade80; border: 1px solid rgba(34,197,94,0.2);">Live Preview</span>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-6 gap-3 mb-5">
                @foreach([
                    ['Saldo', '$147.32', 'profit', 'Modal $100.00'],
                    ['Net P/L', '+$47.32', 'profit', '+47.32% growth'],
                    ['Win Rate', '68.4%', 'text-white', '13W / 6L (19 total)'],
                    ['Profit Factor', '2.14', 'profit', 'Sistem sehat'],
                    ['Avg Win/Loss', '+$6.80 / -$4.20', '', 'Rasio 1.62x'],
                    ['Expectancy', '+$2.18', 'profit', 'Harapan per trade'],
                ] as [$label, $value, $cls, $sub])
                <div style="background: #1f2937; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem;" class="p-3.5">
                    <div class="text-[10px] text-gray-500 uppercase tracking-wider mb-1">{{ $label }}</div>
                    <div class="text-sm font-bold font-mono {{ $cls }}">{{ $value }}</div>
                    <div class="text-[10px] text-gray-500 mt-0.5">{{ $sub }}</div>
                </div>
                @endforeach
            </div>

            {{-- Fake Equity Line --}}
            <div class="relative h-24 rounded-lg overflow-hidden" style="background: rgba(34,197,94,0.03); border: 1px solid rgba(34,197,94,0.08);">
                <svg viewBox="0 0 800 100" class="w-full h-full" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="eq" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#22c55e" stop-opacity="0.3"/>
                            <stop offset="100%" stop-color="#22c55e" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                    <path d="M0,80 L40,75 L80,72 L120,68 L160,74 L200,65 L240,60 L280,55 L320,58 L360,50 L400,42 L440,45 L480,38 L520,30 L560,33 L600,25 L640,20 L680,15 L720,12 L760,8 L800,5"
                          fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M0,80 L40,75 L80,72 L120,68 L160,74 L200,65 L240,60 L280,55 L320,58 L360,50 L400,42 L440,45 L480,38 L520,30 L560,33 L600,25 L640,20 L680,15 L720,12 L760,8 L800,5 L800,100 L0,100 Z"
                          fill="url(#eq)"/>
                </svg>
                <div class="absolute top-2 left-3 text-[10px] text-gray-500 font-mono">Equity Curve (Modal: $100 → Saldo: $147.32)</div>
                <div class="absolute bottom-2 right-3 text-xs profit font-bold font-mono">+$47.32 ↑</div>
            </div>
        </div>
    </section>

    {{-- Features Grid --}}
    <section class="max-w-5xl mx-auto px-6 pb-20">
        <h2 class="text-2xl font-bold text-white text-center mb-3">Semua yang trader butuhkan</h2>
        <p class="text-gray-500 text-center text-sm mb-10">Tidak perlu spreadsheet. Tidak perlu apps berbayar.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach([
                ['📈', 'Equity Curve Real-time', 'Grafik pertumbuhan saldo setiap kali trade di-close. Lihat journey Anda dari hari pertama.'],
                ['📊', 'Statistik Lengkap', 'Win Rate, Profit Factor, Expectancy, Average Win/Loss, Growth % — semua dihitung otomatis.'],
                ['🏷️', 'Tags Strategi & Psikologi', 'Tag setiap trade dengan strategi yang dipakai dan kondisi emosi. Temukan pola keberhasilan.'],
                ['📷', 'Screenshot Chart', 'Upload bukti chart setup per trade. Evaluasi visual lebih mudah.'],
                ['🔍', 'Filter & Cari Trade', 'Filter berdasarkan pair, status, arah, tag, atau rentang tanggal. Analisis lebih spesifik.'],
                ['📥', 'Export CSV', 'Download seluruh data trade dalam format CSV yang kompatibel Excel kapan saja.'],
                ['📅', 'Kalender P/L Harian', 'Heatmap aktivitas trading harian. Lihat hari mana Anda paling profit atau paling merugi.'],
                ['⚡', 'Quick Close Position', 'Tutup posisi open langsung dari halaman detail tanpa harus edit form panjang.'],
                ['📋', 'Laporan Journey', 'Ringkasan bulanan + full trade log yang bisa dicetak atau disimpan sebagai PDF.'],
            ] as [$icon, $title, $desc])
            <div class="card p-5 hover:border-green-500/20 transition-all duration-300">
                <div class="text-2xl mb-3">{{ $icon }}</div>
                <h3 class="text-sm font-bold text-white mb-1.5">{{ $title }}</h3>
                <p class="text-xs text-gray-500 leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </section>

    {{-- CTA --}}
    <section class="max-w-2xl mx-auto px-6 pb-24 text-center">
        <div class="card p-10 glow-green">
            <div class="text-3xl mb-2">₿</div>
            <h2 class="text-2xl font-black text-white mb-3">Siap mulai $100 Challenge?</h2>
            <p class="text-gray-400 text-sm mb-6">Buat akun gratis, catat trade pertama Anda, dan mulai perjalanan menuju konsistensi.</p>
            @auth
                <a href="{{ route('dashboard') }}" class="btn-primary px-8 py-3.5 rounded-xl text-white font-bold inline-block">
                    Buka Dashboard →
                </a>
            @else
                <a href="{{ route('register') }}" class="btn-primary px-8 py-3.5 rounded-xl text-white font-bold inline-block">
                    Daftar Gratis Sekarang →
                </a>
            @endauth
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-white/5 py-6 text-center text-xs text-gray-600">
        Trading Journey Tracker — Built with Laravel {{ app()->version() }} · Open source · Personal use
    </footer>

</body>
</html>
