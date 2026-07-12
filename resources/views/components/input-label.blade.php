@props(['for' => '', 'value' => null])

<label for="{{ $for }}" {{ $attributes->merge(['class' => 'block text-sm font-medium text-gray-300 mb-1.5']) }}>
    {{ $value ?? $slot }}
</label>
