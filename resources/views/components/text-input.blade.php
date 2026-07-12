@props(['disabled' => false])

<input
    {{ $disabled ? 'disabled' : '' }}
    {!! $attributes->merge(['class' => 'input-field w-full px-4 py-2.5 text-sm']) !!}
>
