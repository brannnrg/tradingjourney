@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')
@section('subheader', 'Ringkasan performa journey trading crypto Anda')

@section('header-actions')
    @if($account)
        <a href="{{ route('trading-accounts.report', $account) }}"
           class="px-3.5 py-2 rounded-lg glass-light text-gray-300 text-sm font-medium hover:text-white transition-colors flex items-center gap-1.5"
           title="Lihat & cetak ringkasan laporan">
            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Laporan Journey
        </a>
        <a href="{{ route('trades.create', ['account_id' => $account->id]) }}"
           class="btn-primary px-4 py-2 rounded-lg text-white text-sm font-medium flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Trade
        </a>
    @endif
@endsection

@section('content')

{{-- No accounts state --}}
@if($accounts->isEmpty())
    <div class="flex flex-col items-center justify-center py-28 text-center">
        <div class="w-20 h-20 rounded-2xl btn-primary flex items-center justify-center text-4xl mb-6 shadow-xl shadow-green-500/20">₿</div>
        <h2 class="text-2xl font-bold text-white mb-2">Selamat datang, {{ auth()->user()->name }}!</h2>
        <p class="text-gray-400 mb-8 max-w-md text-sm leading-relaxed">
            Mulai tantangan trading Anda dari modal $100 atau berapapun. Catat setiap transaksi, amati equity curve Anda, dan temukan setup trading terbaik Anda.
        </p>
        <a href="{{ route('trading-accounts.create') }}" class="btn-primary px-6 py-3 rounded-xl text-white font-semibold flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Akun Trading Pertama
        </a>
    </div>
@else

{{-- Account Selector --}}
<div class="mb-6 flex flex-wrap gap-2 items-center">
    @foreach($accounts as $acc)
        <a href="{{ route('dashboard', ['account_id' => $acc->id]) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $acc->id == $selectedAccountId ? 'btn-primary text-white shadow-lg shadow-green-500/20' : 'glass-light text-gray-400 hover:text-white' }}">
            {{ $acc->account_name }}
            <span class="text-xs ml-1 opacity-75">({{ $acc->currency }})</span>
        </a>
    @endforeach
    <a href="{{ route('trading-accounts.create') }}" class="px-3.5 py-2 rounded-lg text-xs text-gray-500 glass-light hover:text-green-400 transition-all flex items-center gap-1">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Akun Baru
    </a>
</div>

@if($account)

{{-- Stat Cards --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5 mb-6">
    {{-- Current Balance --}}
    <div class="card p-4">
        <div class="text-[11px] text-gray-500 uppercase tracking-wider mb-1">Saldo Saat Ini</div>
        <div class="text-xl font-bold font-mono {{ $account->current_balance >= $account->initial_balance ? 'profit' : 'loss' }}">
            {{ number_format($account->current_balance, 2) }}
        </div>
        <div class="text-[11px] text-gray-500 mt-1 truncate">
            Modal: {{ number_format($account->initial_balance, 2) }} {{ $account->currency }}
        </div>
    </div>

    {{-- Net P/L --}}
    <div class="card p-4">
        <div class="text-[11px] text-gray-500 uppercase tracking-wider mb-1">Net Profit/Loss</div>
        <div class="text-xl font-bold font-mono {{ $account->net_profit >= 0 ? 'profit' : 'loss' }}">
            {{ $account->net_profit >= 0 ? '+' : '' }}{{ number_format($account->net_profit, 2) }}
        </div>
        <div class="text-[11px] mt-1 font-mono {{ $account->growth_percentage >= 0 ? 'profit' : 'loss' }}">
            {{ $account->growth_percentage >= 0 ? '+' : '' }}{{ number_format($account->growth_percentage, 2) }}% growth
        </div>
    </div>

    {{-- Win Rate --}}
    <div class="card p-4">
        <div class="text-[11px] text-gray-500 uppercase tracking-wider mb-1">Win Rate</div>
        <div class="text-xl font-bold font-mono text-white">{{ $account->win_rate }}%</div>
        <div class="text-[11px] text-gray-500 mt-1 font-mono">
            {{ $account->winning_trades }}W / {{ $account->losing_trades }}L ({{ $account->total_trades }})
        </div>
    </div>

    {{-- Profit Factor --}}
    <div class="card p-4">
        <div class="text-[11px] text-gray-500 uppercase tracking-wider mb-1">Profit Factor</div>
        <div class="text-xl font-bold font-mono {{ $account->profit_factor >= 1.5 ? 'profit' : ($account->profit_factor >= 1 ? 'text-yellow-400' : 'loss') }}">
            {{ $account->profit_factor == 999 ? '∞' : $account->profit_factor }}
        </div>
        <div class="text-[11px] text-gray-500 mt-1">
            {{ $account->profit_factor >= 1.5 ? 'Sistem sehat' : 'Minimal 1.5' }}
        </div>
    </div>

    {{-- Avg Win / Loss --}}
    <div class="card p-4">
        <div class="text-[11px] text-gray-500 uppercase tracking-wider mb-1">Avg Win / Loss</div>
        <div class="text-xs font-mono font-bold">
            <span class="profit">+{{ number_format($account->average_win, 2) }}</span>
            <span class="text-gray-600 mx-1">/</span>
            <span class="loss">-{{ number_format($account->average_loss, 2) }}</span>
        </div>
        <div class="text-[11px] text-gray-500 mt-1">
            Rasio: {{ $account->average_loss > 0 ? round($account->average_win / $account->average_loss, 2) : '—' }}x
        </div>
    </div>

    {{-- Expectancy --}}
    <div class="card p-4">
        <div class="text-[11px] text-gray-500 uppercase tracking-wider mb-1">Expectancy / Trade</div>
        <div class="text-xl font-bold font-mono {{ $account->expectancy >= 0 ? 'profit' : 'loss' }}">
            {{ $account->expectancy >= 0 ? '+' : '' }}{{ number_format($account->expectancy, 2) }}
        </div>
        <div class="text-[11px] text-gray-500 mt-1 truncate">
            Harapan per trade
        </div>
    </div>
</div>

{{-- Open Positions Alert (if any) --}}
@if($openTrades->isNotEmpty())
    <div class="card p-4 mb-6 border border-yellow-500/30 bg-yellow-500/5">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-yellow-400 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 animate-ping"></span>
                Posisi Sedang Aktif ({{ $openTrades->count() }} Open)
            </h3>
            <span class="text-xs text-gray-400">Klik trade untuk melihat detail atau menutup posisi</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($openTrades as $trade)
                <a href="{{ route('trades.show', $trade) }}"
                   class="glass p-3 rounded-xl border border-yellow-500/20 hover:border-yellow-500/40 transition-all flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-white text-sm">{{ $trade->pair }}</span>
                            <span class="text-[11px] px-1.5 py-0.5 rounded font-bold
                                {{ $trade->direction === 'long' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                {{ strtoupper($trade->direction) }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-400 mt-1 font-mono">
                            Entry: {{ number_format($trade->entry_price, 4) }} · Qty: {{ $trade->quantity }}
                        </div>
                    </div>
                    <span class="btn-primary text-[11px] px-2.5 py-1 rounded-md text-white font-medium">Tutup Trade →</span>
                </a>
            @endforeach
        </div>
    </div>
@endif

{{-- Charts Row --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
    {{-- Equity Curve --}}
    <div class="card p-5 lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-300 flex items-center gap-2">
                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                Equity Curve (Pertumbuhan Saldo)
            </h3>
            <span class="text-xs text-gray-500 font-mono">{{ $account->currency }}</span>
        </div>
        @if(count($equityCurve['data']) > 1)
            <div class="h-64">
                <canvas id="equityChart"></canvas>
            </div>
        @else
            <div class="flex flex-col items-center justify-center h-64 text-gray-600 text-xs">
                <svg class="w-8 h-8 mb-2 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Belum ada transaksi yang closed. Tambahkan transaksi pertama Anda!
            </div>
        @endif
    </div>

    {{-- Win vs Loss Donut --}}
    <div class="card p-5">
        <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
            Rasio Menang / Kalah
        </h3>
        @if($account->total_trades > 0)
            <div class="h-48 flex items-center justify-center">
                <canvas id="winLossChart"></canvas>
            </div>
            <div class="grid grid-cols-2 gap-2 mt-4 text-center">
                <div class="glass-light p-2 rounded-lg">
                    <div class="text-xs text-gray-400">Winning</div>
                    <div class="text-base font-bold profit font-mono">{{ $account->winning_trades }}</div>
                </div>
                <div class="glass-light p-2 rounded-lg">
                    <div class="text-xs text-gray-400">Losing</div>
                    <div class="text-base font-bold loss font-mono">{{ $account->losing_trades }}</div>
                </div>
            </div>
        @else
            <div class="flex items-center justify-center h-48 text-gray-600 text-xs">Belum ada trade closed</div>
        @endif
    </div>
</div>

{{-- Daily P/L Calendar / Heatmap --}}
@if(!empty($dailyPnl))
<div class="card p-5 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-gray-300 flex items-center gap-2">
            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Kalender Profit / Loss Harian
        </h3>
        <span class="text-xs text-gray-500">Aktivitas per hari</span>
    </div>

    <div class="flex flex-wrap gap-2">
        @foreach($dailyPnl as $date => $day)
            <div class="p-2.5 rounded-xl border flex-1 min-w-[120px] max-w-[160px]
                {{ $day['pnl'] > 0 ? 'bg-green-500/10 border-green-500/30' : ($day['pnl'] < 0 ? 'bg-red-500/10 border-red-500/30' : 'bg-gray-800 border-white/5') }}">
                <div class="text-[11px] text-gray-400 font-mono">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</div>
                <div class="text-sm font-bold font-mono mt-0.5 {{ $day['pnl'] >= 0 ? 'profit' : 'loss' }}">
                    {{ $day['pnl'] >= 0 ? '+' : '' }}{{ number_format($day['pnl'], 2) }}
                </div>
                <div class="text-[10px] text-gray-400 mt-1">
                    {{ $day['trades'] }} trade ({{ $day['wins'] }}W/{{ $day['losses'] }}L)
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- Strategy & Pair Performance --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
    {{-- Strategy Performance --}}
    <div class="card p-5">
        <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            Performa per Strategi Setup
        </h3>
        @if(count($strategyPerformance) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-gray-500 border-b border-white/5 pb-2">
                            <th class="text-left pb-2">Strategi</th>
                            <th class="text-center pb-2">Win Rate</th>
                            <th class="text-center pb-2">Trades</th>
                            <th class="text-right pb-2">Net P/L</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($strategyPerformance as $strat)
                        <tr class="hover:bg-white/2 transition-colors">
                            <td class="py-2.5 font-medium text-white">
                                <span class="badge-strategy px-2 py-0.5 rounded text-[11px]">{{ $strat['name'] }}</span>
                            </td>
                            <td class="py-2.5 text-center font-mono text-gray-300">{{ $strat['win_rate'] }}%</td>
                            <td class="py-2.5 text-center text-gray-400">{{ $strat['trade_count'] }}</td>
                            <td class="py-2.5 text-right font-mono font-bold {{ $strat['total_pnl'] >= 0 ? 'profit' : 'loss' }}">
                                {{ $strat['total_pnl'] >= 0 ? '+' : '' }}{{ number_format($strat['total_pnl'], 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-gray-600 text-xs">Belum ada data strategi</div>
        @endif
    </div>

    {{-- Pair Performance --}}
    <div class="card p-5">
        <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Performa per Pair
        </h3>
        @if(count($pairPerformance) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-gray-500 border-b border-white/5 pb-2">
                            <th class="text-left pb-2">Pair</th>
                            <th class="text-center pb-2">Win Rate</th>
                            <th class="text-center pb-2">Trades</th>
                            <th class="text-right pb-2">Net P/L</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($pairPerformance as $pair)
                        <tr class="hover:bg-white/2 transition-colors">
                            <td class="py-2.5 font-mono font-semibold text-white">{{ $pair['pair'] }}</td>
                            <td class="py-2.5 text-center font-mono text-gray-300">{{ $pair['win_rate'] }}%</td>
                            <td class="py-2.5 text-center text-gray-400">{{ $pair['trade_count'] }}</td>
                            <td class="py-2.5 text-right font-mono font-bold {{ $pair['total_pnl'] >= 0 ? 'profit' : 'loss' }}">
                                {{ $pair['total_pnl'] >= 0 ? '+' : '' }}{{ number_format($pair['total_pnl'], 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-gray-600 text-xs">Belum ada data pair</div>
        @endif
    </div>
</div>

{{-- Recent Trades --}}
<div class="card p-5">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-gray-300">Transaksi Terbaru</h3>
        <a href="{{ route('trades.index', ['account_id' => $account->id]) }}" class="text-xs text-green-400 hover:text-green-300">Lihat semua transaksi →</a>
    </div>
    @if($recentTrades->isEmpty())
        <div class="text-center py-8 text-gray-600 text-xs">
            Belum ada trade.
            <a href="{{ route('trades.create', ['account_id' => $account->id]) }}" class="text-green-400 hover:underline">Tambah sekarang</a>
        </div>
    @else
        <div class="space-y-2">
            @foreach($recentTrades as $trade)
            <a href="{{ route('trades.show', $trade) }}"
               class="flex items-center gap-4 p-3 rounded-xl glass-light hover:bg-white/5 transition-all group">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold font-mono
                    {{ $trade->direction === 'long' ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30' }}">
                    {{ $trade->direction === 'long' ? 'L' : 'S' }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-bold text-white group-hover:text-green-400 transition-colors font-mono">{{ $trade->pair }}</div>
                    <div class="text-xs text-gray-500">
                        {{ $trade->entry_time->format('d M Y H:i') }}
                        @if($trade->tags->first())
                            · <span class="text-gray-400">{{ $trade->tags->first()->name }}</span>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    @if($trade->status === 'closed')
                        <div class="text-sm font-bold font-mono {{ $trade->profit_loss >= 0 ? 'profit' : 'loss' }}">
                            {{ $trade->profit_loss >= 0 ? '+' : '' }}{{ number_format($trade->profit_loss, 4) }}
                        </div>
                        <div class="text-xs font-mono {{ $trade->profit_loss_percent >= 0 ? 'text-green-500' : 'text-red-500' }}">
                            {{ $trade->profit_loss_percent >= 0 ? '+' : '' }}{{ number_format($trade->profit_loss_percent, 2) }}%
                        </div>
                    @else
                        <span class="text-[11px] px-2 py-0.5 rounded-full bg-yellow-500/20 text-yellow-400 font-mono animate-pulse">OPEN</span>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    @endif
</div>

@endif {{-- end if $account --}}
@endif {{-- end if $accounts not empty --}}

@endsection

@push('scripts')
@if(isset($account) && $account && count($equityCurve['data']) > 1)
<script>
// Equity Curve Chart
const equityCtx = document.getElementById('equityChart').getContext('2d');
const equityData = @json($equityCurve['data']);
const equityLabels = @json($equityCurve['labels']);
const initialBalance = {{ $account->initial_balance }};
const isNetProfit = equityData[equityData.length-1] >= initialBalance;

new Chart(equityCtx, {
    type: 'line',
    data: {
        labels: equityLabels,
        datasets: [{
            label: 'Balance ({{ $account->currency }})',
            data: equityData,
            borderColor: isNetProfit ? '#22c55e' : '#f87171',
            backgroundColor: isNetProfit ? 'rgba(34,197,94,0.08)' : 'rgba(248,113,113,0.08)',
            borderWidth: 2.5,
            fill: true,
            tension: 0.35,
            pointBackgroundColor: isNetProfit ? '#22c55e' : '#f87171',
            pointRadius: equityData.length <= 25 ? 4 : 2,
            pointHoverRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#111827',
                titleColor: '#9ca3af',
                bodyColor: '#ffffff',
                borderColor: 'rgba(255,255,255,0.1)',
                borderWidth: 1,
                callbacks: {
                    label: ctx => ' Saldo: ' + ctx.parsed.y.toFixed(2) + ' {{ $account->currency }}'
                }
            }
        },
        scales: {
            x: {
                grid: { color: 'rgba(255,255,255,0.03)' },
                ticks: { color: '#6b7280', font: { size: 10 }, maxTicksLimit: 8 }
            },
            y: {
                grid: { color: 'rgba(255,255,255,0.05)' },
                ticks: { color: '#6b7280', font: { size: 10 } }
            }
        }
    }
});
</script>
@endif

@if(isset($account) && $account && $account->total_trades > 0)
<script>
// Win/Loss Donut Chart
const winLossCtx = document.getElementById('winLossChart').getContext('2d');
new Chart(winLossCtx, {
    type: 'doughnut',
    data: {
        labels: ['Menang (Win)', 'Kalah (Loss)'],
        datasets: [{
            data: [{{ $account->winning_trades }}, {{ $account->losing_trades }}],
            backgroundColor: ['rgba(34,197,94,0.85)', 'rgba(248,113,113,0.85)'],
            borderColor: ['#22c55e', '#f87171'],
            borderWidth: 1.5,
            hoverOffset: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#111827',
                bodyColor: '#ffffff',
                borderColor: 'rgba(255,255,255,0.1)',
                borderWidth: 1,
            }
        }
    }
});
</script>
@endif
@endpush
