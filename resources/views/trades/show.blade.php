@extends('layouts.app')

@section('title', 'Detail Trade ' . $trade->pair)
@section('header', 'Detail Trade')
@section('subheader', $trade->tradingAccount->account_name . ' · ' . $trade->pair . ' · ' . strtoupper($trade->direction))

@section('header-actions')
    <a href="{{ route('trades.index', ['account_id' => $trade->trading_account_id]) }}"
       class="px-4 py-2 rounded-lg glass-light text-gray-300 text-sm font-medium hover:text-white transition-colors flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>
    <a href="{{ route('trades.edit', $trade) }}"
       class="px-4 py-2 rounded-lg bg-blue-500/20 border border-blue-500/30 text-blue-400 text-sm font-medium hover:bg-blue-500/30 transition-colors flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        Edit Trade
    </a>
    <form method="POST" action="{{ route('trades.destroy', $trade) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus trade ini?')">
        @csrf @method('DELETE')
        <button type="submit" class="p-2 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm hover:bg-red-500/20 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </button>
    </form>
@endsection

@section('content')

{{-- Top Result Banner --}}
<div class="card p-6 mb-6 border {{ $trade->status === 'closed' ? ($trade->profit_loss >= 0 ? 'border-green-500/30 bg-green-500/5' : 'border-red-500/30 bg-red-500/5') : 'border-yellow-500/30 bg-yellow-500/5' }}">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center font-bold text-lg
                {{ $trade->direction === 'long' ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30' }}">
                {{ $trade->direction === 'long' ? '↑ LONG' : '↓ SHORT' }}
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-bold text-white font-mono">{{ $trade->pair }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                        {{ $trade->status === 'closed' ? 'bg-gray-500/20 text-gray-300' : 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 animate-pulse' }}">
                        {{ $trade->status === 'closed' ? 'CLOSED' : 'OPEN POSITION' }}
                    </span>
                </div>
                <p class="text-xs text-gray-400 mt-1">
                    Entry: {{ $trade->entry_time->format('d M Y, H:i') }}
                    @if($trade->exit_time)
                        · Exit: {{ $trade->exit_time->format('d M Y, H:i') }}
                    @endif
                    @if($trade->duration)
                        · Durasi: <span class="text-gray-300 font-mono">{{ $trade->duration }}</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="text-left md:text-right">
            @if($trade->status === 'closed')
                <div class="text-xs text-gray-400 uppercase tracking-wider mb-1">Hasil Bersih (P/L)</div>
                <div class="text-3xl font-extrabold font-mono {{ $trade->profit_loss >= 0 ? 'profit' : 'loss' }}">
                    {{ $trade->formatted_pnl }}
                </div>
                <div class="text-sm font-semibold mt-0.5 {{ $trade->profit_loss_percent >= 0 ? 'profit' : 'loss' }}">
                    {{ $trade->profit_loss_percent >= 0 ? '+' : '' }}{{ number_format($trade->profit_loss_percent, 2) }}% dari modal posisi
                </div>
            @else
                <div class="text-xs text-yellow-400 uppercase tracking-wider mb-1">Status Posisi</div>
                <div class="text-2xl font-bold text-yellow-400">Sedang Berjalan</div>
                <div class="text-xs text-gray-400 mt-0.5">P/L akan dihitung saat posisi ditutup</div>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Main Column (2 spans) --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Execution & Price Metrics --}}
        <div class="card p-6">
            <h3 class="text-sm font-semibold text-gray-300 mb-5 flex items-center gap-2">
                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                Detail Eksekusi & Harga
            </h3>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="glass-light p-3.5 rounded-xl">
                    <div class="text-xs text-gray-500 mb-1">Entry Price</div>
                    <div class="text-base font-bold text-white font-mono">{{ number_format($trade->entry_price, 4) }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">{{ $trade->tradingAccount->currency }}</div>
                </div>

                <div class="glass-light p-3.5 rounded-xl">
                    <div class="text-xs text-gray-500 mb-1">Exit Price</div>
                    <div class="text-base font-bold font-mono {{ $trade->exit_price ? 'text-white' : 'text-gray-600' }}">
                        {{ $trade->exit_price ? number_format($trade->exit_price, 4) : 'Belum exit' }}
                    </div>
                    <div class="text-xs text-gray-500 mt-0.5">{{ $trade->tradingAccount->currency }}</div>
                </div>

                <div class="glass-light p-3.5 rounded-xl">
                    <div class="text-xs text-gray-500 mb-1">Quantity / Size</div>
                    <div class="text-base font-bold text-white font-mono">{{ $trade->quantity }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Kontrak / Koin</div>
                </div>

                <div class="glass-light p-3.5 rounded-xl">
                    <div class="text-xs text-gray-500 mb-1">Nilai Posisi</div>
                    <div class="text-base font-bold text-white font-mono">{{ number_format($trade->position_value, 2) }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">{{ $trade->tradingAccount->currency }}</div>
                </div>
            </div>

            {{-- Risk Management Details --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 pt-4 border-t border-white/5">
                <div class="p-3 rounded-lg border border-red-500/20 bg-red-500/5">
                    <div class="text-xs text-red-400 font-medium mb-1">Stop Loss (SL)</div>
                    <div class="text-sm font-bold font-mono text-white">
                        {{ $trade->stop_loss ? number_format($trade->stop_loss, 4) : '—' }}
                    </div>
                    @if($trade->stop_loss && $trade->entry_price)
                        <div class="text-xs text-red-400/80 mt-0.5">
                            Risk: {{ number_format(abs($trade->entry_price - $trade->stop_loss), 4) }}
                            ({{ number_format((abs($trade->entry_price - $trade->stop_loss) / $trade->entry_price) * 100, 2) }}%)
                        </div>
                    @endif
                </div>

                <div class="p-3 rounded-lg border border-green-500/20 bg-green-500/5">
                    <div class="text-xs text-green-400 font-medium mb-1">Take Profit (TP)</div>
                    <div class="text-sm font-bold font-mono text-white">
                        {{ $trade->take_profit ? number_format($trade->take_profit, 4) : '—' }}
                    </div>
                    @if($trade->take_profit && $trade->entry_price)
                        <div class="text-xs text-green-400/80 mt-0.5">
                            Reward: {{ number_format(abs($trade->take_profit - $trade->entry_price), 4) }}
                            ({{ number_format((abs($trade->take_profit - $trade->entry_price) / $trade->entry_price) * 100, 2) }}%)
                        </div>
                    @endif
                </div>

                <div class="p-3 rounded-lg border border-blue-500/20 bg-blue-500/5">
                    <div class="text-xs text-blue-400 font-medium mb-1">Risk to Reward (R:R)</div>
                    <div class="text-sm font-bold font-mono text-white">
                        {{ $trade->risk_reward ? '1 : ' . $trade->risk_reward : '—' }}
                    </div>
                    <div class="text-xs text-gray-500 mt-0.5">
                        {{ $trade->risk_reward && $trade->risk_reward >= 2 ? 'Ideal setup (≥ 1:2)' : 'Rasio risiko' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Notes & Psychology & Lesson Learned --}}
        <div class="card p-6">
            <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Catatan & Evaluasi Psikologi
            </h3>

            @if(!empty($trade->notes))
                <div class="p-4 rounded-xl glass-light text-sm text-gray-200 whitespace-pre-wrap leading-relaxed">
                    {{ $trade->notes }}
                </div>
            @else
                <div class="text-center py-6 text-gray-600 text-sm italic">
                    Belum ada catatan atau evaluasi untuk trade ini.
                    <a href="{{ route('trades.edit', $trade) }}" class="text-green-400 hover:underline ml-1">Tambah catatan</a>
                </div>
            @endif
        </div>

        {{-- Screenshot Section --}}
        <div class="card p-6">
            <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Screenshot Chart
            </h3>

            @if($trade->screenshot_path)
                <div class="overflow-hidden rounded-xl border border-white/10 group relative">
                    <img src="{{ Storage::url($trade->screenshot_path) }}"
                         alt="Chart {{ $trade->pair }}"
                         class="w-full h-auto object-cover max-h-[500px] hover:scale-[1.01] transition-transform duration-300">
                    <div class="p-3 bg-dark-800/80 backdrop-blur-sm border-t border-white/5 flex items-center justify-between text-xs text-gray-400">
                        <span>Setup Screenshot</span>
                        <a href="{{ Storage::url($trade->screenshot_path) }}" target="_blank" class="text-green-400 hover:underline flex items-center gap-1">
                            Buka Ukuran Asli
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                </div>
            @else
                <div class="p-8 text-center text-gray-600 glass-light rounded-xl text-sm">
                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Belum ada screenshot yang di-upload.
                    <a href="{{ route('trades.edit', $trade) }}" class="text-green-400 hover:underline ml-1">Upload screenshot</a>
                </div>
            @endif
        </div>

    </div>

    {{-- Right Sidebar --}}
    <div class="space-y-6">

        {{-- Quick Close Action (If Open) --}}
        @if($trade->status === 'open')
            <div class="card p-6 border border-yellow-500/30 bg-yellow-500/5">
                <h3 class="text-sm font-bold text-yellow-400 mb-2 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 animate-ping"></span>
                    Tutup Posisi (Close Trade)
                </h3>
                <p class="text-xs text-gray-400 mb-4">Masukkan harga exit untuk mengunci hasil trade ini.</p>

                <form method="POST" action="{{ route('trades.close', $trade) }}" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-300 mb-1">Exit Price <span class="text-red-400">*</span></label>
                        <input type="number" name="exit_price" step="any" min="0" required
                               placeholder="Harga saat exit"
                               class="input-field w-full px-3.5 py-2 text-sm font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-300 mb-1">Exit Time</label>
                        <input type="datetime-local" name="exit_time"
                               value="{{ now()->format('Y-m-d\TH:i') }}"
                               class="input-field w-full px-3.5 py-2 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-300 mb-1">Catatan Exit (Opsional)</label>
                        <textarea name="close_note" rows="2" placeholder="Alasan exit / TP / SL hit"
                                  class="input-field w-full px-3.5 py-2 text-xs resize-none"></textarea>
                    </div>

                    <button type="submit" class="w-full btn-primary py-2.5 rounded-lg text-white text-sm font-semibold shadow-lg shadow-green-500/20">
                        Selesaikan & Tutup Trade
                    </button>
                </form>
            </div>
        @endif

        {{-- Tags Breakdown Card --}}
        <div class="card p-6">
            <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Tags & Kategori
            </h3>

            {{-- Strategy Tags --}}
            <div class="mb-4">
                <div class="text-xs font-semibold uppercase tracking-wider text-blue-400 mb-2">📐 Strategi</div>
                <div class="flex flex-wrap gap-1.5">
                    @forelse($trade->tags->where('type', 'strategy') as $tag)
                        <span class="px-2.5 py-1 rounded-full text-xs badge-strategy">{{ $tag->name }}</span>
                    @empty
                        <span class="text-xs text-gray-600 italic">Tidak ada tag strategi</span>
                    @endforelse
                </div>
            </div>

            {{-- Emotion Tags --}}
            <div class="mb-4">
                <div class="text-xs font-semibold uppercase tracking-wider text-yellow-400 mb-2">💭 Psikologi / Emosi</div>
                <div class="flex flex-wrap gap-1.5">
                    @forelse($trade->tags->where('type', 'emotion') as $tag)
                        <span class="px-2.5 py-1 rounded-full text-xs badge-emotion">{{ $tag->name }}</span>
                    @empty
                        <span class="text-xs text-gray-600 italic">Tidak ada tag emosi</span>
                    @endforelse
                </div>
            </div>

            {{-- Mistake Tags --}}
            <div>
                <div class="text-xs font-semibold uppercase tracking-wider text-red-400 mb-2">❌ Evaluasi Kesalahan</div>
                <div class="flex flex-wrap gap-1.5">
                    @forelse($trade->tags->where('type', 'mistake') as $tag)
                        <span class="px-2.5 py-1 rounded-full text-xs badge-mistake">{{ $tag->name }}</span>
                    @empty
                        <span class="text-xs text-gray-600 italic">Bebas dari kesalahan</span>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Account Info Card --}}
        <div class="card p-6">
            <h3 class="text-sm font-semibold text-gray-300 mb-3">Akun Terkait</h3>
            <div class="glass-light p-3.5 rounded-xl">
                <div class="text-sm font-bold text-white">{{ $trade->tradingAccount->account_name }}</div>
                <div class="text-xs text-gray-400 mt-0.5">
                    {{ $trade->tradingAccount->exchange ?? 'Exchange' }} · {{ $trade->tradingAccount->currency }}
                </div>
                <div class="mt-3 pt-3 border-t border-white/5 flex items-center justify-between">
                    <span class="text-xs text-gray-500">Saldo Akun:</span>
                    <span class="text-xs font-bold font-mono text-green-400">
                        {{ number_format($trade->tradingAccount->current_balance, 2) }} {{ $trade->tradingAccount->currency }}
                    </span>
                </div>
            </div>
            <a href="{{ route('trading-accounts.show', $trade->tradingAccount) }}" class="mt-3 inline-block text-xs text-green-400 hover:underline">
                Lihat akun & statistik lengkap →
            </a>
        </div>

    </div>

</div>

@endsection
