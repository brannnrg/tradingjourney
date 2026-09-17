@extends('layouts.app')

@section('title', 'Tags & Setup')
@section('header', 'Tags & Setup')
@section('subheader', 'Kelola tag strategi, psikologi, dan evaluasi kesalahan trading Anda')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left: Tag Form --}}
    <div class="card p-6">
        <h3 class="text-sm font-semibold text-gray-300 mb-5 flex items-center gap-2">
            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Tag Baru
        </h3>

        <form method="POST" action="{{ route('tags.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Nama Tag <span class="text-red-400">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}"
                       placeholder="Contoh: Breakout, FOMO, Overtrading"
                       class="input-field w-full px-4 py-2.5 text-sm" required>
                @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Tipe Tag <span class="text-red-400">*</span></label>
                <div class="space-y-2">
                    <label class="flex items-center gap-3 p-3 rounded-lg cursor-pointer glass-light hover:bg-white/5 transition-colors">
                        <input type="radio" name="type" value="strategy" class="text-blue-400"
                               {{ old('type', 'strategy') === 'strategy' ? 'checked' : '' }}>
                        <div>
                            <div class="text-sm font-medium text-white">📐 Strategi</div>
                            <div class="text-xs text-gray-500">Setup trading, timeframe, teknikal</div>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 rounded-lg cursor-pointer glass-light hover:bg-white/5 transition-colors">
                        <input type="radio" name="type" value="emotion" class="text-yellow-400"
                               {{ old('type') === 'emotion' ? 'checked' : '' }}>
                        <div>
                            <div class="text-sm font-medium text-white">💭 Emosi / Psikologi</div>
                            <div class="text-xs text-gray-500">State of mind saat entry/exit</div>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 rounded-lg cursor-pointer glass-light hover:bg-white/5 transition-colors">
                        <input type="radio" name="type" value="mistake" class="text-red-400"
                               {{ old('type') === 'mistake' ? 'checked' : '' }}>
                        <div>
                            <div class="text-sm font-medium text-white">❌ Kesalahan / Evaluasi</div>
                            <div class="text-xs text-gray-500">Hal yang perlu diperbaiki</div>
                        </div>
                    </label>
                </div>
                @error('type') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="w-full btn-primary py-2.5 rounded-lg text-white text-sm font-semibold">
                Simpan Tag
            </button>
        </form>
    </div>

    {{-- Right: Tag List by Type --}}
    <div class="lg:col-span-2 space-y-5">

        @foreach(['strategy' => ['label' => '📐 Strategi / Setup', 'class' => 'badge-strategy', 'title_color' => 'text-blue-400'],
                  'emotion'  => ['label' => '💭 Emosi / Psikologi', 'class' => 'badge-emotion', 'title_color' => 'text-yellow-400'],
                  'mistake'  => ['label' => '❌ Kesalahan / Evaluasi', 'class' => 'badge-mistake', 'title_color' => 'text-red-400']]
                as $type => $config)

        <div class="card p-5">
            <h3 class="text-sm font-semibold {{ $config['title_color'] }} mb-4 flex items-center justify-between">
                <span>{{ $config['label'] }}</span>
                <span class="text-xs text-gray-500 font-normal">
                    {{ isset($tags[$type]) ? $tags[$type]->count() : 0 }} tag
                </span>
            </h3>

            @if(isset($tags[$type]) && $tags[$type]->isNotEmpty())
                <div class="flex flex-wrap gap-2">
                    @foreach($tags[$type] as $tag)
                    <div class="flex items-center gap-1 group">
                        <span class="px-2.5 py-1 rounded-full text-xs {{ $config['class'] }}">
                            {{ $tag->name }}
                            @if($tag->trades_count > 0)
                                <span class="opacity-60 ml-1">({{ $tag->trades_count }}x)</span>
                            @endif
                        </span>
                        <form method="POST" action="{{ route('tags.destroy', $tag) }}"
                              onsubmit="return confirm('Hapus tag \'{{ $tag->name }}\'? Tag ini akan dilepas dari semua trade.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="opacity-0 group-hover:opacity-100 transition-opacity w-4 h-4 rounded-full bg-red-500/20 text-red-400 hover:bg-red-500/40 flex items-center justify-center"
                                    title="Hapus tag">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6 text-gray-600 text-xs">
                    Belum ada tag {{ $config['label'] }}.
                </div>
            @endif
        </div>

        @endforeach
    </div>

</div>

@endsection
