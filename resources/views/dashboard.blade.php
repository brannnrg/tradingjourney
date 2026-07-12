@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')
@section('subheader', 'Ringkasan performa trading crypto Anda')

@section('header-actions')
    @if($account)
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
    <div class="flex flex-col items-center justify-center py-32 text-center">
        <div class="w-20 h-20 rounded-2xl btn-primary flex items-center justify-center text-4xl mb-6">₿</div>
        <h2 class="text-2xl font-bold text-white mb-2">Selamat datang, {{ auth()->user()->name }}!</h2>
        <p class="text-gray-400 mb-8 max-w-md">Mulai perjalanan trading crypto Anda dengan membuat akun trading pertama. Catat setiap trade dan lihat perkembangan modal Anda.</p>
        <a href="{{ route('trading-accounts.create') }}" class="btn-primary px-6 py-3 rounded-xl text-white font-semibold flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Akun Trading Pertama
        </a>
    </div>
@else

{{-- Account Selector --}}
<div class="mb-6 flex flex-wrap gap-2">
    @foreach($accounts as $acc)
        <a href="{{ route('dashboard', ['account_id' => $acc->id]) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $acc->id == $selectedAccountId ? 'btn-primary text-white' : 'glass-light text-gray-400 hover:text-white' }}">
            {{ $acc->account_name }}
            <span class="text-xs ml-1 opacity-70">{{ $acc->exchange ?? $acc->currency }}</span>
        </a>
    @endforeach
    <a href="{{ route('trading-accounts.create') }}" class="px-4 py-2 rounded-lg text-sm text-gray-500 glass-light hover:text-green-400 transition-all flex items-center gap-1">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Akun Baru
    </a>
</div>

@if($account)
{{-- Stat Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- Current Balance --}}
    <div class="card p-5">
        <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Saldo Saat Ini</div>
        <div class="text-2xl font-bold {{ $account->current_balance >= $account->initial_balance ? 'profit' : 'loss' }}">
            {{ number_format($account->current_balance, 2) }}
        </div>
        <div class="text-xs text-gray-500 mt-1">{{ $account->currency }} · Modal awal: {{ number_format($account->initial_balance, 2) }}</div>
    </div>

    {{-- Net P/L --}}
    <div class="card p-5">
        <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Net Profit/Loss</div>
        <div class="text-2xl font-bold {{ $account->net_profit >= 0 ? 'profit' : 'loss' }}">
            {{ $account->net_profit >= 0 ? '+' : '' }}{{ number_format($account->net_profit, 2) }}
        </div>
        @if($account->initial_balance > 0)
        <div class="text-xs mt-1 {{ $account->net_profit >= 0 ? 'text-green-500' : 'text-red-500' }}">
            {{ $account->net_profit >= 0 ? '+' : '' }}{{ number_format(($account->net_profit / $account->initial_balance) * 100, 2) }}% dari modal
        </div>
        @endif
    </div>

    {{-- Win Rate --}}
    <div class="card p-5">
        <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Win Rate</div>
        <div class="text-2xl font-bold text-white">{{ $account->win_rate }}%</div>
        <div class="text-xs text-gray-500 mt-1">{{ $account->winning_trades }}W / {{ $account->losing_trades }}L · {{ $account->total_trades }} total</div>
    </div>

    {{-- Profit Factor --}}
    <div class="card p-5">
        <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Profit Factor</div>
        <div class="text-2xl font-bold {{ $account->profit_factor >= 1 ? 'profit' : 'loss' }}">
            {{ $account->profit_factor == 999 ? '∞' : $account->profit_factor }}
        </div>
        <div class="text-xs text-gray-500 mt-1">> 1.5 = sistem bagus</div>
    </div>
</div>

{{-- Charts --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    {{-- Equity Curve --}}
    <div class="card p-5 lg:col-span-2">
        <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            Equity Curve
        </h3>
        @if(count($equityCurve['data']) > 1)
            <canvas id="equityChart" height="200"></canvas>
        @else
            <div class="flex items-center justify-center h-48 text-gray-600 text-sm">
                Belum ada trade yang closed. Tambahkan trade pertama Anda!
            </div>
        @endif
    </div>

    {{-- Win/Loss Donut --}}
    <div class="card p-5">
        <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
            Win vs Loss
        </h3>
        @if($account->total_trades > 0)
            <canvas id="winLossChart" class="max-h-48"></canvas>
            <div class="flex justify-center gap-6 mt-3 text-xs text-gray-400">
                <span><span class="inline-block w-2.5 h-2.5 rounded-full bg-green-500 mr-1"></span>Win {{ $account->winning_trades }}</span>
                <span><span class="inline-block w-2.5 h-2.5 rounded-full bg-red-500 mr-1"></span>Loss {{ $account->losing_trades }}</span>
            </div>
        @else
            <div class="flex items-center justify-center h-48 text-gray-600 text-sm">Belum ada data</div>
        @endif
    </div>
</div>

{{-- Pair Performance --}}
@if(count($pairPerformance) > 0)
<div class="card p-5 mb-6">
    <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
        <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        Performa per Pair
    </h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs text-gray-500 border-b border-white/5">
                    <th class="text-left pb-2">Pair</th>
                    <th class="text-right pb-2">Trades</th>
                    <th class="text-right pb-2">Total P/L</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pairPerformance as $pair)
                <tr class="border-b border-white/5 hover:bg-white/2 transition-colors">
                    <td class="py-2.5 font-mono font-medium text-white">{{ $pair['pair'] }}</td>
                    <td class="py-2.5 text-right text-gray-400">{{ $pair['trade_count'] }}</td>
                    <td class="py-2.5 text-right font-semibold {{ $pair['total_pnl'] >= 0 ? 'profit' : 'loss' }}">
                        {{ $pair['total_pnl'] >= 0 ? '+' : '' }}{{ number_format($pair['total_pnl'], 4) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Recent Trades --}}
<div class="card p-5">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-gray-300">Trade Terbaru</h3>
        <a href="{{ route('trades.index', ['account_id' => $account->id]) }}" class="text-xs text-green-400 hover:text-green-300">Lihat semua →</a>
    </div>
    @if($recentTrades->isEmpty())
        <div class="text-center py-8 text-gray-600 text-sm">Belum ada trade. <a href="{{ route('trades.create', ['account_id' => $account->id]) }}" class="text-green-400 hover:underline">Tambah sekarang</a></div>
    @else
        <div class="space-y-2">
            @foreach($recentTrades as $trade)
            <div class="flex items-center gap-4 p-3 rounded-lg glass-light hover:bg-white/5 transition-colors">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold
                    {{ $trade->direction === 'long' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                    {{ $trade->direction === 'long' ? 'L' : 'S' }}
                </div>
                <div class="flex-1">
                    <div class="text-sm font-medium text-white">{{ $trade->pair }}</div>
                    <div class="text-xs text-gray-500">{{ $trade->entry_time->format('d M Y H:i') }}</div>
                </div>
                <div class="text-right">
                    @if($trade->profit_loss !== null)
                        <div class="text-sm font-semibold {{ $trade->profit_loss >= 0 ? 'profit' : 'loss' }}">
                            {{ $trade->profit_loss >= 0 ? '+' : '' }}{{ number_format($trade->profit_loss, 4) }}
                        </div>
                        <div class="text-xs {{ $trade->profit_loss_percent >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $trade->profit_loss_percent >= 0 ? '+' : '' }}{{ number_format($trade->profit_loss_percent, 2) }}%
                        </div>
                    @else
                        <span class="text-xs px-2 py-0.5 rounded-full bg-yellow-500/10 text-yellow-400">OPEN</span>
                    @endif
                </div>
            </div>
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

new Chart(equityCtx, {
    type: 'line',
    data: {
        labels: equityLabels,
        datasets: [{
            label: 'Balance ({{ $account->currency }})',
            data: equityData,
            borderColor: equityData[equityData.length-1] >= initialBalance ? '#22c55e' : '#f87171',
            backgroundColor: equityData[equityData.length-1] >= initialBalance
                ? 'rgba(34,197,94,0.05)' : 'rgba(248,113,113,0.05)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: equityData[equityData.length-1] >= initialBalance ? '#22c55e' : '#f87171',
            pointRadius: equityData.length <= 20 ? 4 : 2,
            pointHoverRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1f2937',
                titleColor: '#9ca3af',
                bodyColor: '#f1f5f9',
                borderColor: 'rgba(255,255,255,0.1)',
                borderWidth: 1,
                callbacks: {
                    label: ctx => ' ' + ctx.parsed.y.toFixed(4) + ' {{ $account->currency }}'
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
        labels: ['Win', 'Loss'],
        datasets: [{
            data: [{{ $account->winning_trades }}, {{ $account->losing_trades }}],
            backgroundColor: ['rgba(34,197,94,0.8)', 'rgba(248,113,113,0.8)'],
            borderColor: ['#22c55e', '#f87171'],
            borderWidth: 1,
            hoverOffset: 6
        }]
    },
    options: {
        responsive: true,
        cutout: '72%',
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1f2937',
                bodyColor: '#f1f5f9',
                borderColor: 'rgba(255,255,255,0.1)',
                borderWidth: 1,
            }
        }
    }
});
</script>
@endif
@endpush
