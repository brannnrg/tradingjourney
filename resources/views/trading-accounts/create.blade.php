@extends('layouts.app')

@section('title', 'Buat Akun Trading')
@section('header', 'Akun Trading Baru')
@section('subheader', 'Tambahkan akun exchange untuk mulai tracking')

@section('content')
<div class="max-w-xl">
    <div class="card p-6">
        <form method="POST" action="{{ route('trading-accounts.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Nama Akun <span class="text-red-400">*</span></label>
                <input type="text" name="account_name" value="{{ old('account_name') }}"
                    class="input-field w-full px-4 py-2.5 text-sm" placeholder="cth: Binance Spot, Bybit Futures...">
                @error('account_name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Exchange</label>
                <select name="exchange" class="input-field w-full px-4 py-2.5 text-sm">
                    <option value="">-- Pilih Exchange --</option>
                    @foreach(['Binance', 'Bybit', 'OKX', 'KuCoin', 'Bitget', 'MEXC', 'Gate.io', 'Lainnya'] as $ex)
                        <option value="{{ $ex }}" {{ old('exchange') == $ex ? 'selected' : '' }}>{{ $ex }}</option>
                    @endforeach
                </select>
                @error('exchange') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Currency <span class="text-red-400">*</span></label>
                    <select name="currency" class="input-field w-full px-4 py-2.5 text-sm">
                        @foreach(['USDT', 'USDC', 'BTC', 'ETH', 'BNB', 'USD'] as $cur)
                            <option value="{{ $cur }}" {{ old('currency', 'USDT') == $cur ? 'selected' : '' }}>{{ $cur }}</option>
                        @endforeach
                    </select>
                    @error('currency') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Modal Awal <span class="text-red-400">*</span></label>
                    <input type="number" name="initial_balance" value="{{ old('initial_balance', 100) }}"
                        step="any" min="0" class="input-field w-full px-4 py-2.5 text-sm" placeholder="100">
                    @error('initial_balance') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Tanggal Mulai <span class="text-red-400">*</span></label>
                <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}"
                    class="input-field w-full px-4 py-2.5 text-sm">
                @error('start_date') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Deskripsi</label>
                <textarea name="description" rows="3" class="input-field w-full px-4 py-2.5 text-sm resize-none"
                    placeholder="Tujuan, strategi utama, atau catatan untuk akun ini...">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary px-6 py-2.5 rounded-lg text-white text-sm font-medium">
                    Buat Akun
                </button>
                <a href="{{ route('trading-accounts.index') }}" class="px-6 py-2.5 rounded-lg glass-light text-gray-300 text-sm font-medium hover:text-white transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
