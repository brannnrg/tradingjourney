@extends('layouts.app')

@section('title', 'Edit Trade — ' . $trade->pair)
@section('header', 'Edit Trade')
@section('subheader', $trade->pair . ' · ' . ucfirst($trade->direction) . ' · ' . $trade->entry_time->format('d M Y H:i'))

@section('header-actions')
    <a href="{{ route('trades.show', $trade) }}"
       class="px-4 py-2 rounded-lg glass-light text-gray-300 text-sm hover:text-white transition-colors flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali ke Detail
    </a>
@endsection

@section('content')
<form method="POST" action="{{ route('trades.update', $trade) }}" enctype="multipart/form-data">
    @csrf @method('PATCH')
    @include('trades.partials._form', [
        'trade'        => $trade,
        'selectedTags' => $selectedTags,
    ])
    <div class="flex gap-3 mt-6">
        <button type="submit" class="btn-primary px-8 py-2.5 rounded-lg text-white font-medium">
            Simpan Perubahan
        </button>
        <a href="{{ route('trades.show', $trade) }}"
           class="px-6 py-2.5 rounded-lg glass-light text-gray-300 font-medium hover:text-white transition-colors">
            Batal
        </a>
    </div>
</form>
@endsection
