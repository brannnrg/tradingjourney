@extends('layouts.app')

@section('title', 'Laporan Journey — ' . $tradingAccount->account_name)
@section('header', 'Laporan Journey')
@section('subheader', $tradingAccount->account_name . ' · ' . $tradingAccount->currency . ' · ' . $tradingAccount->start_date->format('d M Y') . ' s/d sekarang')

@section('header-actions')
    <a href="{{ route('trading-accounts.show', $tradingAccount) }}"
       class="px-4 py-2 rounded-lg glass-light text-gray-300 text-sm hover:text-white transition-colors flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>
    <button onclick="window.print()"
            class="px-4 py-2 rounded-lg glass-light text-gray-300 text-sm hover:text-white transition-colors flex items-center gap-1.5">
        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Cetak / PDF
    </button>
    <a href="{{ route('trades.export', ['account_id' => $tradingAccount->id]) }}"
       class="px-4 py-2 rounded-lg glass-light text-gray-300 text-sm hover:text-white transition-colors flex items-center gap-1.5">
        <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export CSV
    </a>
@endsection

@section('content')

{{-- Summary Stats --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div class="card p-4 text-center">
        <div class="text-xs text-gray-500 mb-1">Total Trades</div>
        <div class="text-2xl font-bold text-white font-mono">{{ $tradingAccount->total_trades }}</div>
    </div>
    <div class="card p-4 text-center">
        <div class="text-xs text-gray-500 mb-1">Win Rate</div>
        <div class="text-2xl font-bold font-mono {{ $tradingAccount->win_rate >= 50 ? 'profit' : 'loss' }}">{{ $tradingAccount->win_rate }}%</div>
    </div>
    <div class="card p-4 text-center">
        <div class="text-xs text-gray-500 mb-1">Net P/L</div>
        <div class="text-xl font-bold font-mono {{ $tradingAccount->net_profit >= 0 ? 'profit' : 'loss' }}">
            {{ $tradingAccount->net_profit >= 0 ? '+' : '' }}{{ number_format($tradingAccount->net_profit, 2) }}
        </div>
    </div>
    <div class="card p-4 text-center">
        <div class="text-xs text-gray-500 mb-1">Growth</div>
        <div class="text-xl font-bold font-mono {{ $tradingAccount->growth_percentage >= 0 ? 'profit' : 'loss' }}">
            {{ $tradingAccount->growth_percentage >= 0 ? '+' : '' }}{{ number_format($tradingAccount->growth_percentage, 2) }}%
        </div>
    </div>
    <div class="card p-4 text-center">
        <div class="text-xs text-gray-500 mb-1">Profit Factor</div>
        <div class="text-xl font-bold font-mono {{ $tradingAccount->profit_factor >= 1.5 ? 'profit' : ($tradingAccount->profit_factor >= 1 ? 'text-yellow-400' : 'loss') }}">
            {{ $tradingAccount->profit_factor == 999 ? '∞' : $tradingAccount->profit_factor }}
        </div>
    </div>
    <div class="card p-4 text-center">
        <div class="text-xs text-gray-500 mb-1">Expectancy</div>
        <div class="text-xl font-bold font-mono {{ $tradingAccount->expectancy >= 0 ? 'profit' : 'loss' }}">
            {{ $tradingAccount->expectancy >= 0 ? '+' : '' }}{{ number_format($tradingAccount->expectancy, 2) }}
        </div>
    </div>
</div>

{{-- Monthly Breakdown --}}
@if(!empty($monthly))
<div class="card p-6 mb-6">
    <h3 class="text-sm font-semibold text-gray-300 mb-5 flex items-center gap-2">
        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Ringkasan per Bulan
    </h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs text-gray-500 border-b border-white/5">
                    <th class="text-left pb-3">Bulan</th>
                    <th class="text-center pb-3">Total Trades</th>
                    <th class="text-center pb-3">Win</th>
                    <th class="text-center pb-3">Loss</th>
                    <th class="text-center pb-3">Win Rate</th>
                    <th class="text-right pb-3">Net P/L</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($monthly as $row)
                <tr class="hover:bg-white/2 transition-colors">
                    <td class="py-3 font-medium text-white">{{ $row['label'] }}</td>
                    <td class="py-3 text-center text-gray-300 font-mono">{{ $row['trades'] }}</td>
                    <td class="py-3 text-center profit font-mono font-semibold">{{ $row['wins'] }}</td>
                    <td class="py-3 text-center loss font-mono font-semibold">{{ $row['losses'] }}</td>
                    <td class="py-3 text-center font-mono">
                        <span class="{{ $row['win_rate'] >= 50 ? 'profit' : 'loss' }}">{{ $row['win_rate'] }}%</span>
                    </td>
                    <td class="py-3 text-right font-mono font-bold {{ $row['pnl'] >= 0 ? 'profit' : 'loss' }}">
                        {{ $row['pnl'] >= 0 ? '+' : '' }}{{ number_format($row['pnl'], 2) }} {{ $tradingAccount->currency }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="border-t-2 border-white/10">
                <tr class="font-bold text-sm">
                    <td class="pt-3 text-white">TOTAL</td>
                    <td class="pt-3 text-center text-white font-mono">{{ $tradingAccount->total_trades }}</td>
                    <td class="pt-3 text-center profit font-mono">{{ $tradingAccount->winning_trades }}</td>
                    <td class="pt-3 text-center loss font-mono">{{ $tradingAccount->losing_trades }}</td>
                    <td class="pt-3 text-center font-mono {{ $tradingAccount->win_rate >= 50 ? 'profit' : 'loss' }}">{{ $tradingAccount->win_rate }}%</td>
                    <td class="pt-3 text-right font-mono {{ $tradingAccount->net_profit >= 0 ? 'profit' : 'loss' }}">
                        {{ $tradingAccount->net_profit >= 0 ? '+' : '' }}{{ number_format($tradingAccount->net_profit, 2) }} {{ $tradingAccount->currency }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endif

{{-- Full Trade Log --}}
<div class="card p-6">
    <div class="flex items-center justify-between mb-5">
        <h3 class="text-sm font-semibold text-gray-300 flex items-center gap-2">
            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Log Lengkap Semua Trade ({{ $trades->count() }})
        </h3>
    </div>

    @if($trades->isEmpty())
        <div class="text-center py-8 text-gray-600 text-sm">Belum ada transaksi.</div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-gray-500 border-b border-white/5">
                        <th class="text-left pb-3">#</th>
                        <th class="text-left pb-3">Pair</th>
                        <th class="text-center pb-3">Arah</th>
                        <th class="text-center pb-3">Status</th>
                        <th class="text-right pb-3">Qty</th>
                        <th class="text-right pb-3">Entry</th>
                        <th class="text-right pb-3">Exit</th>
                        <th class="text-right pb-3">Net P/L</th>
                        <th class="text-right pb-3">P/L %</th>
                        <th class="text-left pb-3">Entry Time</th>
                        <th class="text-left pb-3">Tags</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($trades as $i => $trade)
                    <tr class="hover:bg-white/2 transition-colors">
                        <td class="py-2.5 text-gray-600 font-mono">{{ $i + 1 }}</td>
                        <td class="py-2.5 font-mono font-bold text-white">{{ $trade->pair }}</td>
                        <td class="py-2.5 text-center">
                            <span class="px-1.5 py-0.5 rounded font-bold
                                {{ $trade->direction === 'long' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                {{ strtoupper($trade->direction) }}
                            </span>
                        </td>
                        <td class="py-2.5 text-center">
                            <span class="{{ $trade->status === 'closed' ? 'text-gray-400' : 'text-yellow-400' }}">
                                {{ strtoupper($trade->status) }}
                            </span>
                        </td>
                        <td class="py-2.5 text-right font-mono text-gray-300">{{ $trade->quantity }}</td>
                        <td class="py-2.5 text-right font-mono text-gray-300">{{ number_format($trade->entry_price, 4) }}</td>
                        <td class="py-2.5 text-right font-mono text-gray-400">{{ $trade->exit_price ? number_format($trade->exit_price, 4) : '—' }}</td>
                        <td class="py-2.5 text-right font-mono font-bold {{ $trade->profit_loss > 0 ? 'profit' : ($trade->profit_loss < 0 ? 'loss' : 'text-gray-400') }}">
                            @if($trade->profit_loss !== null)
                                {{ $trade->profit_loss >= 0 ? '+' : '' }}{{ number_format($trade->profit_loss, 4) }}
                            @else —
                            @endif
                        </td>
                        <td class="py-2.5 text-right font-mono {{ $trade->profit_loss_percent > 0 ? 'text-green-400' : ($trade->profit_loss_percent < 0 ? 'text-red-400' : 'text-gray-600') }}">
                            @if($trade->profit_loss_percent !== null)
                                {{ $trade->profit_loss_percent >= 0 ? '+' : '' }}{{ number_format($trade->profit_loss_percent, 2) }}%
                            @else —
                            @endif
                        </td>
                        <td class="py-2.5 text-gray-400 font-mono">{{ $trade->entry_time->format('d M Y H:i') }}</td>
                        <td class="py-2.5">
                            <div class="flex flex-wrap gap-1">
                                @foreach($trade->tags->take(2) as $tag)
                                    <span class="px-1.5 py-0.5 rounded text-[10px] badge-{{ $tag->type }}">{{ $tag->name }}</span>
                                @endforeach
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
