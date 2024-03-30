<div {!! isset($xShow) && !empty($xShow) ? "x-show=\"{$xShow}\"" : "" !!} class="mb-4">
    <label for="{{ $id }}" class="block mb-2 font-medium text-gray-900 dark:text-slate-400">{{ $label }}</label>
    <input
        type="{{ $type ?? 'text' }}"
        id="{{ $id }}"
        value="{{ $value }}"
        class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
        {{ $attributes }} />

    @include ('forum::components.form.error')
</div>
