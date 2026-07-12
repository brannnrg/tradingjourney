<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-primary inline-flex items-center justify-center px-6 py-2.5 rounded-lg text-white text-sm font-semibold tracking-wide focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-gray-900']) }}>
    {{ $slot }}
</button>
