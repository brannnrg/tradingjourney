<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-white">
            {{ __('Hapus Akun') }}
        </h2>

        <p class="mt-1 text-sm text-gray-400">
            {{ __('Setelah akun Anda dihapus, semua data dan aset di dalamnya akan dihapus secara permanen. Sebelum menghapus akun, harap cadangkan data penting yang ingin Anda simpan.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Hapus Akun') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-gray-900 border border-white/10 rounded-2xl">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-white">
                {{ __('Apakah Anda yakin ingin menghapus akun?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-400">
                {{ __('Setelah akun Anda dihapus, semua data dan jurnal trading Anda akan dihapus secara permanen. Silakan masukkan password Anda untuk mengonfirmasi tindakan ini.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Password Konfirmasi') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-5 py-2 rounded-lg glass-light text-gray-300 text-sm font-medium hover:text-white transition-colors">
                    {{ __('Batal') }}
                </button>

                <button type="submit" class="px-5 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-medium transition-colors">
                    {{ __('Hapus Akun Permanen') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
