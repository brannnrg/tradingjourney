@extends('layouts.app')

@section('title', 'Trade Log')
@section('header', 'Trade Log')
@section('subheader', 'Semua riwayat transaksi crypto Anda')

@section('header-actions')
    <a href="{{ route('trades.create', ['account_id' => $selectedAccountId]) }}"
       class="btn-primary px-4 py-2 rounded-lg text-white text-sm font-medium flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Trade
    </a>
@endsection

@section('content')

{{-- Account Filter --}}
<div class="mb-6 flex flex-wrap gap-2 items-center">
    @foreach($accounts as $acc)
        <a href="{{ route('trades.index', ['account_id' => $acc->id]) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $acc->id == $selectedAccountId ? 'btn-primary text-white' : 'glass-light text-gray-400 hover:text-white' }}">
            {{ $acc->account_name }}
        </a>
    @endforeach
</div>

@if($accounts->isEmpty())
    <div class="text-center py-24 text-gray-500">
        <div class="text-4xl mb-3">📊</div>
        <p class="mb-4">Belum ada akun trading.</p>
        <a href="{{ route('trading-accounts.create') }}" class="btn-primary px-5 py-2 rounded-lg text-white text-sm">Buat Akun Trading</a>
    </div>
@elseif($trades->isEmpty())
    <div class="card p-12 text-center">
        <div class="text-5xl mb-4">📈</div>
        <h3 class="text-lg font-semibold text-white mb-2">Belum ada trade</h3>
        <p class="text-gray-500 mb-6">Mulai catat transaksi pertama Anda!</p>
        <a href="{{ route('trades.create', ['account_id' => $selectedAccountId]) }}" class="btn-primary px-6 py-2.5 rounded-lg text-white font-medium">Tambah Trade</a>
    </div>
@else
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-gray-500 border-b border-white/5 bg-white/2">
                        <th class="text-left px-5 py-3">Pair</th>
                        <th class="text-center px-4 py-3">Arah</th>
                        <th class="text-right px-4 py-3">Quantity</th>
                        <th class="text-right px-4 py-3">Entry</th>
                        <th class="text-right px-4 py-3">Exit</th>
                        <th class="text-right px-4 py-3">P/L</th>
                        <th class="text-right px-4 py-3">P/L %</th>
                        <th class="text-center px-4 py-3">Status</th>
                        <th class="text-left px-4 py-3">Tags</th>
                        <th class="text-right px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($trades as $trade)
                    <tr class="hover:bg-white/2 transition-colors group">
                        <td class="px-5 py-3.5">
                            <div class="font-mono font-semibold text-white">{{ $trade->pair }}</div>
                            <div class="text-xs text-gray-500">{{ $trade->entry_time->format('d M Y H:i') }}</div>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="px-2.5 py-0.5 rounded text-xs font-bold
                                {{ $trade->direction === 'long' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                {{ $trade->direction === 'long' ? '↑ LONG' : '↓ SHORT' }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-right font-mono text-gray-300 text-xs">{{ $trade->quantity }}</td>
                        <td class="px-4 py-3.5 text-right font-mono text-gray-300 text-xs">{{ number_format($trade->entry_price, 4) }}</td>
                        <td class="px-4 py-3.5 text-right font-mono text-gray-400 text-xs">{{ $trade->exit_price ? number_format($trade->exit_price, 4) : '—' }}</td>
                        <td class="px-4 py-3.5 text-right font-semibold
                            {{ $trade->profit_loss > 0 ? 'profit' : ($trade->profit_loss < 0 ? 'loss' : 'text-gray-400') }}">
                            @if($trade->profit_loss !== null)
                                {{ $trade->profit_loss >= 0 ? '+' : '' }}{{ number_format($trade->profit_loss, 4) }}
                            @else <span class="text-gray-600">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-right text-xs
                            {{ $trade->profit_loss_percent > 0 ? 'text-green-500' : ($trade->profit_loss_percent < 0 ? 'text-red-500' : 'text-gray-500') }}">
                            @if($trade->profit_loss_percent !== null)
                                {{ $trade->profit_loss_percent >= 0 ? '+' : '' }}{{ number_format($trade->profit_loss_percent, 2) }}%
                            @else <span class="text-gray-600">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs
                                {{ $trade->status === 'closed' ? 'bg-gray-500/20 text-gray-400' : 'bg-yellow-500/20 text-yellow-400' }}">
                                {{ $trade->status === 'closed' ? '✓ CLOSED' : '● OPEN' }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex flex-wrap gap-1">
                                @foreach($trade->tags->take(3) as $tag)
                                    <span class="px-1.5 py-0.5 rounded text-xs badge-{{ $tag->type }}">{{ $tag->name }}</span>
                                @endforeach
                                @if($trade->tags->count() > 3)
                                    <span class="px-1.5 py-0.5 rounded text-xs text-gray-500">+{{ $trade->tags->count() - 3 }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                @if($trade->screenshot_path)
                                    <a href="{{ Storage::url($trade->screenshot_path) }}" target="_blank"
                                       class="p-1.5 rounded text-gray-500 hover:text-purple-400 hover:bg-purple-400/10 transition-all" title="Lihat Screenshot">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </a>
                                @endif
                                <a href="{{ route('trades.edit', $trade) }}"
                                   class="p-1.5 rounded text-gray-500 hover:text-blue-400 hover:bg-blue-400/10 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('trades.destroy', $trade) }}" onsubmit="return confirm('Hapus trade ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded text-gray-500 hover:text-red-400 hover:bg-red-400/10 transition-all">
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
            {{ $trades->appends(['account_id' => $selectedAccountId])->links() }}
        </div>
        @endif
    </div>
@endif

@endsection
