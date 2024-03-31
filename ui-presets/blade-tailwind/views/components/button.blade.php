<button {{ $attributes->merge(['class' => 'bg-blue-500 hover:bg-blue-400 text-white font-semibold px-3 py-2 rounded-md inline-block']) }}>
    {{ $slot }}
</button>
