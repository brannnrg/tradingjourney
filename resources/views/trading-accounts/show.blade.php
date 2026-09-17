@extends('layouts.app')

@section('title', $tradingAccount->account_name)
@section('header', $tradingAccount->account_name)
@section('subheader', ($tradingAccount->exchange ?? 'Exchange') . ' · ' . $tradingAccount->currency . ' · Mulai ' . $tradingAccount->start_date->format('d M Y'))

@section('header-actions')
    <a href="{{ route('trading-accounts.report', $tradingAccount) }}"
       class="px-4 py-2 rounded-lg glass-light text-gray-300 text-sm font-medium hover:text-white transition-colors flex items-center gap-1.5">
        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Laporan
    </a>
    <a href="{{ route('trades.create', ['account_id' => $tradingAccount->id]) }}"
       class="btn-primary px-4 py-2 rounded-lg text-white text-sm font-medium flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Trade
    </a>
    <a href="{{ route('trading-accounts.edit', $tradingAccount) }}"
       class="px-4 py-2 rounded-lg glass-light text-gray-300 text-sm font-medium hover:text-white transition-colors">
        Edit
    </a>
@endsection

@section('content')

{{-- Stats Row --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div class="card p-4">
        <div class="text-xs text-gray-500 mb-1">Saldo Saat Ini</div>
        <div class="text-xl font-bold font-mono {{ $tradingAccount->current_balance >= $tradingAccount->initial_balance ? 'profit' : 'loss' }}">
            {{ number_format($tradingAccount->current_balance, 2) }}
        </div>
        <div class="text-xs text-gray-600 mt-0.5 font-mono">{{ $tradingAccount->currency }}</div>
    </div>

    <div class="card p-4">
        <div class="text-xs text-gray-500 mb-1">Net P/L</div>
        <div class="text-xl font-bold font-mono {{ $tradingAccount->net_profit >= 0 ? 'profit' : 'loss' }}">
            {{ $tradingAccount->net_profit >= 0 ? '+' : '' }}{{ number_format($tradingAccount->net_profit, 2) }}
        </div>
        <div class="text-xs mt-0.5 font-mono {{ $tradingAccount->growth_percentage >= 0 ? 'profit' : 'loss' }}">
            {{ $tradingAccount->growth_percentage >= 0 ? '+' : '' }}{{ number_format($tradingAccount->growth_percentage, 2) }}%
        </div>
    </div>

    <div class="card p-4">
        <div class="text-xs text-gray-500 mb-1">Win Rate</div>
        <div class="text-xl font-bold font-mono text-white">{{ $tradingAccount->win_rate }}%</div>
        <div class="text-xs text-gray-600 mt-0.5 font-mono">{{ $tradingAccount->winning_trades }}W / {{ $tradingAccount->losing_trades }}L</div>
    </div>

    <div class="card p-4">
        <div class="text-xs text-gray-500 mb-1">Profit Factor</div>
        <div class="text-xl font-bold font-mono {{ $tradingAccount->profit_factor >= 1.5 ? 'profit' : ($tradingAccount->profit_factor >= 1 ? 'text-yellow-400' : 'loss') }}">
            {{ $tradingAccount->profit_factor == 999 ? '∞' : $tradingAccount->profit_factor }}
        </div>
        <div class="text-xs text-gray-600 mt-0.5">Total {{ $tradingAccount->total_trades }} trades</div>
    </div>

    <div class="card p-4">
        <div class="text-xs text-gray-500 mb-1">Avg Win / Loss</div>
        <div class="text-xs font-bold font-mono mt-1">
            <span class="profit">+{{ number_format($tradingAccount->average_win, 2) }}</span> /
            <span class="loss">-{{ number_format($tradingAccount->average_loss, 2) }}</span>
        </div>
        <div class="text-xs text-gray-600 mt-0.5">
            Rasio: {{ $tradingAccount->average_loss > 0 ? round($tradingAccount->average_win / $tradingAccount->average_loss, 2) : '—' }}x
        </div>
    </div>

    <div class="card p-4">
        <div class="text-xs text-gray-500 mb-1">Expectancy</div>
        <div class="text-xl font-bold font-mono {{ $tradingAccount->expectancy >= 0 ? 'profit' : 'loss' }}">
            {{ $tradingAccount->expectancy >= 0 ? '+' : '' }}{{ number_format($tradingAccount->expectancy, 2) }}
        </div>
        <div class="text-xs text-gray-600 mt-0.5">per trade</div>
    </div>
</div>

{{-- Description --}}
@if($tradingAccount->description)
<div class="card px-5 py-4 mb-6">
    <p class="text-sm text-gray-400 leading-relaxed">{{ $tradingAccount->description }}</p>
</div>
@endif

{{-- Trade List --}}
<div class="card p-5">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-gray-300 flex items-center gap-2">
            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Semua Trade ({{ $tradingAccount->trades->count() }})
        </h3>
        <a href="{{ route('trades.index', ['account_id' => $tradingAccount->id]) }}"
           class="text-xs text-green-400 hover:text-green-300 flex items-center gap-1">
            Filter & Lihat Semua →
        </a>
    </div>

    @if($tradingAccount->trades->isEmpty())
        <div class="text-center py-16 text-gray-600">
            <div class="text-4xl mb-3">📈</div>
            <p class="text-sm mb-4 text-gray-400">Belum ada trade di akun ini.</p>
            <a href="{{ route('trades.create', ['account_id' => $tradingAccount->id]) }}"
               class="btn-primary px-5 py-2.5 rounded-lg text-white text-sm font-semibold inline-block">
                Tambah Trade Pertama
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-gray-500 border-b border-white/5">
                        <th class="text-left pb-3">Pair & Waktu</th>
                        <th class="text-center pb-3">Arah</th>
                        <th class="text-center pb-3">Status</th>
                        <th class="text-right pb-3">Qty</th>
                        <th class="text-right pb-3">Entry</th>
                        <th class="text-right pb-3">Exit</th>
                        <th class="text-right pb-3">Net P/L</th>
                        <th class="text-right pb-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($tradingAccount->trades as $trade)
                    <tr class="hover:bg-white/2 transition-colors group">
                        <td class="py-3">
                            <a href="{{ route('trades.show', $trade) }}" class="font-mono font-bold text-white hover:text-green-400 transition-colors">
                                {{ $trade->pair }}
                            </a>
                            <div class="text-xs text-gray-500 font-mono">{{ $trade->entry_time->format('d M Y H:i') }}</div>
                        </td>
                        <td class="py-3 text-center">
                            <span class="px-2 py-0.5 rounded text-xs font-bold font-mono
                                {{ $trade->direction === 'long' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                {{ strtoupper($trade->direction) }}
                            </span>
                        </td>
                        <td class="py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-mono
                                {{ $trade->status === 'closed' ? 'bg-gray-500/20 text-gray-400' : 'bg-yellow-500/20 text-yellow-400' }}">
                                {{ strtoupper($trade->status) }}
                            </span>
                        </td>
                        <td class="py-3 text-right font-mono text-gray-300 text-xs">{{ $trade->quantity }}</td>
                        <td class="py-3 text-right font-mono text-gray-300 text-xs">{{ number_format($trade->entry_price, 4) }}</td>
                        <td class="py-3 text-right font-mono text-gray-400 text-xs">
                            {{ $trade->exit_price ? number_format($trade->exit_price, 4) : '—' }}
                        </td>
                        <td class="py-3 text-right font-mono font-semibold text-xs
                            {{ $trade->profit_loss > 0 ? 'profit' : ($trade->profit_loss < 0 ? 'loss' : 'text-gray-400') }}">
                            @if($trade->profit_loss !== null)
                                {{ $trade->profit_loss >= 0 ? '+' : '' }}{{ number_format($trade->profit_loss, 4) }}
                                <div class="text-[11px] font-normal text-gray-500">
                                    {{ $trade->profit_loss_percent >= 0 ? '+' : '' }}{{ number_format($trade->profit_loss_percent, 2) }}%
                                </div>
                            @else
                                —
                            @endif
                        </td>
                        <td class="py-3 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('trades.show', $trade) }}"
                                   class="p-1.5 rounded text-gray-500 hover:text-green-400 hover:bg-green-500/10 transition-colors"
                                   title="Lihat Detail">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('trades.edit', $trade) }}"
                                   class="p-1.5 rounded text-gray-500 hover:text-blue-400 hover:bg-blue-400/10 transition-colors"
                                   title="Edit">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('trades.destroy', $trade) }}"
                                      onsubmit="return confirm('Hapus trade {{ $trade->pair }} ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="p-1.5 rounded text-gray-500 hover:text-red-400 hover:bg-red-400/10 transition-colors"
                                            title="Hapus">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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
