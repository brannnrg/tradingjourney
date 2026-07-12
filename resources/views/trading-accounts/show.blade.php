@extends('layouts.app')

@section('title', $tradingAccount->account_name)
@section('header', $tradingAccount->account_name)
@section('subheader', ($tradingAccount->exchange ?? 'Exchange') . ' · ' . $tradingAccount->currency . ' · Mulai ' . $tradingAccount->start_date->format('d M Y'))

@section('header-actions')
    <a href="{{ route('trades.create', ['account_id' => $tradingAccount->id]) }}" class="btn-primary px-4 py-2 rounded-lg text-white text-sm font-medium flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Trade
    </a>
    <a href="{{ route('trading-accounts.edit', $tradingAccount) }}" class="px-4 py-2 rounded-lg glass-light text-gray-300 text-sm font-medium hover:text-white transition-colors">Edit</a>
@endsection

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card p-4">
        <div class="text-xs text-gray-500 mb-1">Saldo Saat Ini</div>
        <div class="text-xl font-bold {{ $tradingAccount->current_balance >= $tradingAccount->initial_balance ? 'profit' : 'loss' }}">
            {{ number_format($tradingAccount->current_balance, 4) }}
        </div>
        <div class="text-xs text-gray-600">{{ $tradingAccount->currency }}</div>
    </div>
    <div class="card p-4">
        <div class="text-xs text-gray-500 mb-1">Net P/L</div>
        <div class="text-xl font-bold {{ $tradingAccount->net_profit >= 0 ? 'profit' : 'loss' }}">
            {{ $tradingAccount->net_profit >= 0 ? '+' : '' }}{{ number_format($tradingAccount->net_profit, 4) }}
        </div>
        <div class="text-xs text-gray-600">{{ $tradingAccount->currency }}</div>
    </div>
    <div class="card p-4">
        <div class="text-xs text-gray-500 mb-1">Win Rate</div>
        <div class="text-xl font-bold text-white">{{ $tradingAccount->win_rate }}%</div>
        <div class="text-xs text-gray-600">{{ $tradingAccount->winning_trades }}W / {{ $tradingAccount->losing_trades }}L</div>
    </div>
    <div class="card p-4">
        <div class="text-xs text-gray-500 mb-1">Profit Factor</div>
        <div class="text-xl font-bold {{ $tradingAccount->profit_factor >= 1 ? 'profit' : 'loss' }}">
            {{ $tradingAccount->profit_factor == 999 ? '∞' : $tradingAccount->profit_factor }}
        </div>
        <div class="text-xs text-gray-600">Total {{ $tradingAccount->total_trades }} trades</div>
    </div>
</div>

{{-- Trade List --}}
<div class="card p-5">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-gray-300">Semua Trade</h3>
        <a href="{{ route('trades.index', ['account_id' => $tradingAccount->id]) }}" class="text-xs text-green-400">Lihat dengan filter →</a>
    </div>

    @if($tradingAccount->trades->isEmpty())
        <div class="text-center py-12 text-gray-500">
            <div class="text-4xl mb-3">📈</div>
            <p class="mb-4">Belum ada trade di akun ini.</p>
            <a href="{{ route('trades.create', ['account_id' => $tradingAccount->id]) }}" class="btn-primary px-5 py-2 rounded-lg text-white text-sm">Tambah Trade Pertama</a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-gray-500 border-b border-white/5">
                        <th class="text-left pb-3">Pair</th>
                        <th class="text-center pb-3">Arah</th>
                        <th class="text-right pb-3">Quantity</th>
                        <th class="text-right pb-3">Entry</th>
                        <th class="text-right pb-3">Exit</th>
                        <th class="text-right pb-3">P/L</th>
                        <th class="text-center pb-3">Status</th>
                        <th class="text-right pb-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($tradingAccount->trades as $trade)
                    <tr class="hover:bg-white/2 transition-colors">
                        <td class="py-3">
                            <div class="font-mono font-medium text-white">{{ $trade->pair }}</div>
                            <div class="text-xs text-gray-500">{{ $trade->entry_time->format('d M H:i') }}</div>
                        </td>
                        <td class="py-3 text-center">
                            <span class="px-2 py-0.5 rounded text-xs font-bold {{ $trade->direction === 'long' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                {{ strtoupper($trade->direction) }}
                            </span>
                        </td>
                        <td class="py-3 text-right font-mono text-gray-300">{{ $trade->quantity }}</td>
                        <td class="py-3 text-right font-mono text-gray-300">{{ number_format($trade->entry_price, 4) }}</td>
                        <td class="py-3 text-right font-mono text-gray-400">{{ $trade->exit_price ? number_format($trade->exit_price, 4) : '—' }}</td>
                        <td class="py-3 text-right font-semibold {{ $trade->profit_loss > 0 ? 'profit' : ($trade->profit_loss < 0 ? 'loss' : 'text-gray-400') }}">
                            @if($trade->profit_loss !== null)
                                {{ $trade->profit_loss >= 0 ? '+' : '' }}{{ number_format($trade->profit_loss, 4) }}
                                <div class="text-xs font-normal">{{ $trade->profit_loss_percent >= 0 ? '+' : '' }}{{ number_format($trade->profit_loss_percent, 2) }}%</div>
                            @else —
                            @endif
                        </td>
                        <td class="py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs {{ $trade->status === 'closed' ? 'bg-gray-500/20 text-gray-400' : 'bg-yellow-500/20 text-yellow-400' }}">
                                {{ strtoupper($trade->status) }}
                            </span>
                        </td>
                        <td class="py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('trades.edit', $trade) }}" class="text-gray-500 hover:text-blue-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('trades.destroy', $trade) }}" onsubmit="return confirm('Hapus trade ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-500 hover:text-red-400 transition-colors">
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
    @endif
</div>

@endsection
