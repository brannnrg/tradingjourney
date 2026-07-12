<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Trading Journey') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                }
            }
        }
    </script>

    <style>
        body { background-color: #0a0d14; color: #f1f5f9; font-family: 'Inter', sans-serif; }
        .glass { background: rgba(17,24,39,0.8); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.08); }
        .btn-primary { background: linear-gradient(135deg, #22c55e, #16a34a); transition: all 0.2s; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 20px rgba(34,197,94,0.4); }
        .input-field { background: #1f2937; border: 1px solid rgba(255,255,255,0.1); color: #f1f5f9; border-radius: 0.5rem; width: 100%; padding: 0.625rem 1rem; font-size: 0.875rem; transition: border-color 0.2s; }
        .input-field:focus { outline: none; border-color: #22c55e; box-shadow: 0 0 0 2px rgba(34,197,94,0.2); }
        .input-field::placeholder { color: #6b7280; }
        label { display: block; font-size: 0.875rem; font-weight: 500; color: #d1d5db; margin-bottom: 0.375rem; }
        .error-msg { color: #f87171; font-size: 0.75rem; margin-top: 0.25rem; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-4">

    {{-- Background decoration --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-green-500/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-blue-500/5 rounded-full blur-3xl"></div>
    </div>

    {{-- Logo --}}
    <div class="mb-8 text-center">
        <a href="/" class="inline-flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl btn-primary flex items-center justify-center text-white font-bold text-2xl">₿</div>
            <div class="text-left">
                <div class="text-xl font-bold text-white">Trading Journey</div>
                <div class="text-xs text-gray-500">Crypto Tracker</div>
            </div>
        </a>
    </div>

    {{-- Card --}}
    <div class="glass rounded-2xl p-8 w-full max-w-md relative z-10">
        {{ $slot }}
    </div>

    <p class="mt-6 text-xs text-gray-600">Lacak setiap perjalanan trading crypto Anda</p>
</body>
</html>
