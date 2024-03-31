<button {{ $attributes->merge(['class' => 'bg-gray-300 hover:bg-gray-200 text-gray-600 font-semibold px-3 py-2 rounded-md inline-block']) }}>
    {{ $slot }}
</button>
