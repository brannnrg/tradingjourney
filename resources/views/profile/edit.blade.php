@extends('layouts.app')

@section('title', 'Profil Pengguna')
@section('header', 'Pengaturan Profil')
@section('subheader', 'Perbarui informasi akun dan keamanan Anda')

@section('content')
<div class="max-w-4xl space-y-6">
    {{-- Update Profile Info --}}
    <div class="card p-6 sm:p-8">
        <div class="max-w-xl">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    {{-- Update Password --}}
    <div class="card p-6 sm:p-8">
        <div class="max-w-xl">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    {{-- Delete Account --}}
    <div class="card p-6 sm:p-8 border-red-500/20">
        <div class="max-w-xl">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection
