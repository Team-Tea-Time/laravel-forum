@php
$colorClasses = match ($intent) {
    'primary', '', null => 'text-white bg-blue-600 hover:text-white hover:bg-blue-500',
    'secondary' => 'text-zinc-800 bg-zinc-400/50 hover:bg-zinc-400/35 dark:text-slate-800 dark:bg-slate-400/50 dark:hover:bg-slate-400/65',
    'danger' => 'text-white bg-red-500 hover:bg-red-400'
};

$sizeClasses = match ($size) {
    'regular', '', null => 'min-w-36 px-4 py-2',
    'small' => 'px-4 py-1',
};
@endphp

<a href="{{ $href }}" class="link-button inline-block rounded-full font-medium text-lg text-center {{ $colorClasses }} {{ $sizeClasses }}" {{ $attributes }}>
    @if (isset($icon) && !empty($icon))
        @include ("forum::components.icons.{$icon}", ['size' => '5'])
    @endif
    {{ $label }}
</a>
