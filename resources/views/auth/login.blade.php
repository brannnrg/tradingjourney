<x-guest-layout>
    <h2 class="text-xl font-bold text-white mb-1">Selamat datang kembali 👋</h2>
    <p class="text-gray-500 text-sm mb-6">Masuk untuk melanjutkan trading journey Anda</p>

    <!-- Session Status -->
    @if(session('status'))
        <div class="mb-4 px-4 py-2 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400 text-sm">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="email@example.com" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label class="inline-flex items-center gap-2 cursor-pointer" style="margin-bottom:0">
                <input id="remember_me" type="checkbox" name="remember"
                    class="w-4 h-4 rounded border-gray-600 bg-gray-700 text-green-500 focus:ring-green-500 focus:ring-offset-gray-900">
                <span class="text-sm text-gray-400">Ingat saya</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-green-400 hover:text-green-300 transition-colors">
                    Lupa password?
                </a>
            @endif
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full justify-center py-3">
                Masuk →
            </x-primary-button>
        </div>
    </form>

    <p class="mt-6 text-center text-sm text-gray-500">
        Belum punya akun?
        <a href="{{ route('register') }}" class="text-green-400 hover:text-green-300 font-medium transition-colors">Daftar sekarang</a>
    </p>
</x-guest-layout>
