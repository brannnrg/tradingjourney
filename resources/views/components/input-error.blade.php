@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'mt-1 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li class="text-red-400 text-xs">{{ $message }}</li>
        @endforeach
    </ul>
@endif
