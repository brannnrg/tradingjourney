@extends('layouts.app')

@section('title', 'Akun Trading')
@section('header', 'Akun Trading')
@section('subheader', 'Kelola semua akun exchange Anda')

@section('header-actions')
    <a href="{{ route('trading-accounts.create') }}" class="btn-primary px-4 py-2 rounded-lg text-white text-sm font-medium flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Akun Baru
    </a>
@endsection

@section('content')

@if($accounts->isEmpty())
    <div class="flex flex-col items-center justify-center py-32 text-center">
        <div class="w-16 h-16 rounded-2xl glass flex items-center justify-center text-3xl mb-4">📊</div>
        <h2 class="text-xl font-bold text-white mb-2">Belum ada akun trading</h2>
        <p class="text-gray-400 mb-6 max-w-sm">Tambahkan akun trading untuk mulai melacak perjalanan crypto Anda.</p>
        <a href="{{ route('trading-accounts.create') }}" class="btn-primary px-6 py-2.5 rounded-xl text-white font-medium">Buat Akun Trading</a>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($accounts as $account)
        <div class="card p-5 hover:border-green-500/20 transition-all duration-300 group">
            {{-- Header --}}
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="font-semibold text-white group-hover:text-green-400 transition-colors">{{ $account->account_name }}</h3>
                    <div class="text-xs text-gray-500 mt-0.5">{{ $account->exchange ?? 'Exchange' }} · {{ $account->currency }}</div>
                </div>
                <div class="flex gap-1">
                    <a href="{{ route('trading-accounts.edit', $account) }}" class="p-1.5 rounded-lg text-gray-500 hover:text-blue-400 hover:bg-blue-400/10 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                    <form method="POST" action="{{ route('trading-accounts.destroy', $account) }}" onsubmit="return confirm('Hapus akun dan semua trade di dalamnya?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 rounded-lg text-gray-500 hover:text-red-400 hover:bg-red-400/10 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Balance --}}
            <div class="mb-4">
                <div class="text-2xl font-bold {{ $account->current_balance >= $account->initial_balance ? 'profit' : 'loss' }}">
                    {{ number_format($account->current_balance, 2) }} <span class="text-sm font-normal text-gray-500">{{ $account->currency }}</span>
                </div>
                <div class="text-xs text-gray-500 mt-0.5">Modal awal: {{ number_format($account->initial_balance, 2) }} {{ $account->currency }}</div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-3 gap-2 mb-4">
                <div class="glass-light rounded-lg p-2 text-center">
                    <div class="text-sm font-semibold text-white">{{ $account->win_rate }}%</div>
                    <div class="text-xs text-gray-500">Win Rate</div>
                </div>
                <div class="glass-light rounded-lg p-2 text-center">
                    <div class="text-sm font-semibold {{ $account->net_profit >= 0 ? 'profit' : 'loss' }}">
                        {{ $account->net_profit >= 0 ? '+' : '' }}{{ number_format($account->net_profit, 2) }}
                    </div>
                    <div class="text-xs text-gray-500">Net P/L</div>
                </div>
                <div class="glass-light rounded-lg p-2 text-center">
                    <div class="text-sm font-semibold text-white">{{ $account->total_trades }}</div>
                    <div class="text-xs text-gray-500">Trades</div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between pt-3 border-t border-white/5">
                <span class="text-xs text-gray-500">Mulai: {{ $account->start_date->format('d M Y') }}</span>
                <a href="{{ route('trading-accounts.show', $account) }}" class="text-xs text-green-400 hover:text-green-300 font-medium">Detail →</a>
            </div>
        </div>
        @endforeach
    </div>
@endif

@endsection
