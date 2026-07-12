@extends('layouts.app')

@section('title', 'Edit Trade')
@section('header', 'Edit Trade')
@section('subheader', $trade->pair . ' · ' . ucfirst($trade->direction) . ' · ' . $trade->entry_time->format('d M Y'))

@section('content')
<form method="POST" action="{{ route('trades.update', $trade) }}" enctype="multipart/form-data">
    @csrf @method('PATCH')
    @include('trades.partials._form', [
        'trade' => $trade,
        'selectedTags' => $selectedTags,
    ])
    <div class="flex gap-3 mt-6">
        <button type="submit" class="btn-primary px-8 py-2.5 rounded-lg text-white font-medium">Simpan Perubahan</button>
        <a href="{{ route('trades.index', ['account_id' => $trade->trading_account_id]) }}" class="px-6 py-2.5 rounded-lg glass-light text-gray-300 font-medium hover:text-white transition-colors">Batal</a>
    </div>
</form>
@endsection
