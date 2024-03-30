<div {!! isset($xShow) && !empty($xShow) ? "x-show=\"{$xShow}\"" : "" !!} class="flex {{ isset($reverse) && $reverse ? 'flex-row-reverse' : '' }} items-center mb-2">
    <input
        id="{{ $id }}"
        type="checkbox"
        value="{{ $value }}"
        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-800 dark:border-gray-600"
        {{ $attributes }} />
    @if (isset($label))
        <label for="{{ $id }}" class="ms-2 font-medium text-gray-900 select-none dark:text-slate-400">{{ $label }}</label>
    @endif
</div>
