@extends('layouts.app')

@section('title', 'Tambah Trade')
@section('header', 'Tambah Trade Baru')
@section('subheader', 'Catat transaksi crypto Anda')

@section('content')
<form method="POST" action="{{ route('trades.store') }}" enctype="multipart/form-data">
    @csrf
    @include('trades.partials._form', [
        'trade' => null,
        'selectedTags' => [],
    ])
    <div class="flex gap-3 mt-6">
        <button type="submit" class="btn-primary px-8 py-2.5 rounded-lg text-white font-medium">Simpan Trade</button>
        <a href="{{ route('trades.index') }}" class="px-6 py-2.5 rounded-lg glass-light text-gray-300 font-medium hover:text-white transition-colors">Batal</a>
    </div>
</form>
@endsection
