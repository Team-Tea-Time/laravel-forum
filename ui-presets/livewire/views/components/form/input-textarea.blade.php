<div {!! isset($xShow) && !empty($xShow) ? "x-show=\"{$xShow}\"" : "" !!} class="mb-4">
    @if (isset($label))
        <label for="{{ $id }}" class="block mb-2 font-medium text-gray-900 dark:text-slate-400">{{ $label }}</label>
    @endif
    <textarea
        id="{{ $id }}"
        class="block p-2.5 w-full min-h-36 text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
        {{ $attributes }}></textarea>

    @include ('forum::components.form.error')
</div>
