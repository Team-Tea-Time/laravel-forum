<div class="inline-block relative w-64">
    <select {{ $attributes->merge(['class' => 'block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline']) }}>
        {{ $slot }}
    </select>
</div>
