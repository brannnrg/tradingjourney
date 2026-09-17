@extends('layouts.app')

@section('title', 'Trade Log')
@section('header', 'Trade Log')
@section('subheader', 'Semua riwayat transaksi crypto Anda')

@section('header-actions')
    @if($account)
        <a href="{{ route('trades.export', request()->query()) }}"
           class="px-3.5 py-2 rounded-lg glass-light text-gray-300 text-sm font-medium hover:text-white transition-colors flex items-center gap-1.5"
           title="Download data trade dalam format CSV / Excel">
            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export CSV
        </a>
    @endif
    <a href="{{ route('trades.create', ['account_id' => $selectedAccountId]) }}"
       class="btn-primary px-4 py-2 rounded-lg text-white text-sm font-medium flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Trade
    </a>
@endsection

@section('content')

{{-- Account Tabs --}}
<div class="mb-5 flex flex-wrap gap-2 items-center">
    @foreach($accounts as $acc)
        <a href="{{ route('trades.index', array_merge(request()->except(['account_id', 'page']), ['account_id' => $acc->id])) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $acc->id == $selectedAccountId ? 'btn-primary text-white shadow-lg shadow-green-500/20' : 'glass-light text-gray-400 hover:text-white' }}">
            {{ $acc->account_name }}
            <span class="text-xs ml-1 opacity-75">({{ $acc->trades()->count() }})</span>
        </a>
    @endforeach
</div>

@if($accounts->isEmpty())
    <div class="text-center py-24 text-gray-500">
        <div class="text-4xl mb-3">📊</div>
        <p class="mb-4 text-base text-gray-300">Belum ada akun trading.</p>
        <a href="{{ route('trading-accounts.create') }}" class="btn-primary px-5 py-2.5 rounded-lg text-white text-sm font-semibold">Buat Akun Trading Pertama</a>
    </div>
@elseif(!$account)
    <div class="card p-12 text-center text-gray-400">
        Pilih salah satu akun trading di atas.
    </div>
@else

    {{-- Filter Toolbar --}}
    <div class="card p-4 mb-6">
        <form method="GET" action="{{ route('trades.index') }}" class="space-y-3">
            <input type="hidden" name="account_id" value="{{ $selectedAccountId }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                {{-- Search Pair --}}
                <div class="lg:col-span-1">
                    <label class="block text-xs text-gray-400 mb-1">Cari Pair</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Contoh: BTC, ETH"
                           class="input-field w-full px-3 py-1.5 text-xs font-mono uppercase">
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Status</label>
                    <select name="status" class="input-field w-full px-3 py-1.5 text-xs">
                        <option value="">Semua Status</option>
                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>🟡 Open</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>✅ Closed</option>
                    </select>
                </div>

                {{-- Direction --}}
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Arah</label>
                    <select name="direction" class="input-field w-full px-3 py-1.5 text-xs">
                        <option value="">Semua Arah</option>
                        <option value="long" {{ request('direction') === 'long' ? 'selected' : '' }}>📈 Long</option>
                        <option value="short" {{ request('direction') === 'short' ? 'selected' : '' }}>📉 Short</option>
                    </select>
                </div>

                {{-- Tag --}}
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Tag / Strategi</label>
                    <select name="tag_id" class="input-field w-full px-3 py-1.5 text-xs">
                        <option value="">Semua Tag</option>
                        @foreach($tags as $tag)
                            <option value="{{ $tag->id }}" {{ request('tag_id') == $tag->id ? 'selected' : '' }}>
                                [{{ strtoupper(substr($tag->type, 0, 4)) }}] {{ $tag->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date From --}}
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="input-field w-full px-3 py-1.5 text-xs">
                </div>

                {{-- Date To --}}
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="input-field w-full px-3 py-1.5 text-xs">
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-between pt-2 border-t border-white/5 text-xs">
                <div class="text-gray-500">
                    Menampilkan <span class="text-white font-mono font-semibold">{{ $trades->total() }}</span> transaksi
                </div>
                <div class="flex items-center gap-2">
                    @if(request()->hasAny(['search', 'status', 'direction', 'tag_id', 'date_from', 'date_to']))
                        <a href="{{ route('trades.index', ['account_id' => $selectedAccountId]) }}"
                           class="px-3 py-1.5 rounded glass-light text-gray-400 hover:text-white transition-colors">
                            Reset Filter
                        </a>
                    @endif
                    <button type="submit" class="btn-primary px-4 py-1.5 rounded text-white font-medium">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Trades List --}}
    @if($trades->isEmpty())
        <div class="card p-12 text-center">
            <div class="text-4xl mb-3">🔍</div>
            <h3 class="text-base font-semibold text-white mb-1">Tidak ada trade yang sesuai</h3>
            <p class="text-gray-500 text-xs mb-5">Coba ubah kriteria filter atau tambahkan transaksi baru.</p>
            <div class="flex justify-center gap-3">
                <a href="{{ route('trades.index', ['account_id' => $selectedAccountId]) }}" class="px-4 py-2 rounded-lg glass-light text-xs text-gray-300">Reset Filter</a>
                <a href="{{ route('trades.create', ['account_id' => $selectedAccountId]) }}" class="btn-primary px-4 py-2 rounded-lg text-white text-xs font-semibold">Tambah Trade</a>
            </div>
        </div>
    @else
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-gray-500 border-b border-white/5 bg-white/2">
                            <th class="text-left px-5 py-3">Pair & Waktu</th>
                            <th class="text-center px-3 py-3">Arah</th>
                            <th class="text-center px-3 py-3">Status</th>
                            <th class="text-right px-4 py-3">Qty</th>
                            <th class="text-right px-4 py-3">Entry</th>
                            <th class="text-right px-4 py-3">Exit</th>
                            <th class="text-right px-4 py-3">Net P/L</th>
                            <th class="text-right px-4 py-3">P/L %</th>
                            <th class="text-left px-4 py-3">Tags</th>
                            <th class="text-right px-5 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($trades as $trade)
                        <tr class="hover:bg-white/2 transition-colors group">
                            {{-- Pair & Waktu --}}
                            <td class="px-5 py-3.5">
                                <a href="{{ route('trades.show', $trade) }}" class="font-mono font-bold text-white hover:text-green-400 transition-colors flex items-center gap-1.5">
                                    {{ $trade->pair }}
                                    @if($trade->screenshot_path)
                                        <svg class="w-3.5 h-3.5 text-gray-500 group-hover:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    @endif
                                </a>
                                <div class="text-xs text-gray-500 font-mono">
                                    {{ $trade->entry_time->format('d M Y H:i') }}
                                    @if($trade->duration)
                                        · <span class="text-gray-400">{{ $trade->duration }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Arah --}}
                            <td class="px-3 py-3.5 text-center">
                                <span class="px-2.5 py-0.5 rounded text-xs font-bold font-mono
                                    {{ $trade->direction === 'long' ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30' }}">
                                    {{ $trade->direction === 'long' ? 'LONG' : 'SHORT' }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="px-3 py-3.5 text-center">
                                @if($trade->status === 'closed')
                                    <span class="px-2 py-0.5 rounded-full text-xs bg-gray-500/20 text-gray-300 font-mono">CLOSED</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-xs bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 font-mono animate-pulse">OPEN</span>
                                @endif
                            </td>

                            {{-- Quantity --}}
                            <td class="px-4 py-3.5 text-right font-mono text-gray-300 text-xs">{{ $trade->quantity }}</td>

                            {{-- Entry Price --}}
                            <td class="px-4 py-3.5 text-right font-mono text-gray-300 text-xs">{{ number_format($trade->entry_price, 4) }}</td>

                            {{-- Exit Price --}}
                            <td class="px-4 py-3.5 text-right font-mono text-xs {{ $trade->exit_price ? 'text-gray-300' : 'text-gray-600' }}">
                                {{ $trade->exit_price ? number_format($trade->exit_price, 4) : '—' }}
                            </td>

                            {{-- P/L --}}
                            <td class="px-4 py-3.5 text-right font-mono font-semibold
                                {{ $trade->profit_loss > 0 ? 'profit' : ($trade->profit_loss < 0 ? 'loss' : 'text-gray-400') }}">
                                @if($trade->profit_loss !== null)
                                    {{ $trade->profit_loss >= 0 ? '+' : '' }}{{ number_format($trade->profit_loss, 4) }}
                                @else
                                    <span class="text-gray-600">—</span>
                                @endif
                            </td>

                            {{-- P/L % --}}
                            <td class="px-4 py-3.5 text-right font-mono text-xs
                                {{ $trade->profit_loss_percent > 0 ? 'text-green-400' : ($trade->profit_loss_percent < 0 ? 'text-red-400' : 'text-gray-500') }}">
                                @if($trade->profit_loss_percent !== null)
                                    {{ $trade->profit_loss_percent >= 0 ? '+' : '' }}{{ number_format($trade->profit_loss_percent, 2) }}%
                                @else
                                    <span class="text-gray-600">—</span>
                                @endif
                            </td>

                            {{-- Tags --}}
                            <td class="px-4 py-3.5">
                                <div class="flex flex-wrap gap-1 max-w-[200px]">
                                    @foreach($trade->tags->take(2) as $tag)
                                        <span class="px-1.5 py-0.5 rounded text-[11px] badge-{{ $tag->type }}">{{ $tag->name }}</span>
                                    @endforeach
                                    @if($trade->tags->count() > 2)
                                        <span class="px-1.5 py-0.5 rounded text-[11px] text-gray-500">+{{ $trade->tags->count() - 2 }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    {{-- Detail Link --}}
                                    <a href="{{ route('trades.show', $trade) }}"
                                       class="p-1.5 rounded text-gray-400 hover:text-green-400 hover:bg-green-500/10 transition-colors"
                                       title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>

                                    {{-- Edit Link --}}
                                    <a href="{{ route('trades.edit', $trade) }}"
                                       class="p-1.5 rounded text-gray-400 hover:text-blue-400 hover:bg-blue-400/10 transition-colors"
                                       title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>

                                    {{-- Delete Form --}}
                                    <form method="POST" action="{{ route('trades.destroy', $trade) }}" onsubmit="return confirm('Hapus trade ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded text-gray-400 hover:text-red-400 hover:bg-red-400/10 transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($trades->hasPages())
            <div class="px-5 py-4 border-t border-white/5">
                {{ $trades->links() }}
            </div>
            @endif
        </div>
    @endif

@endif

@endsection
