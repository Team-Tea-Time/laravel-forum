@php
$colorClasses = match ($intent) {
    'primary', '', null => 'text-white bg-blue-600 hover:text-white hover:bg-blue-500',
    'secondary' => 'text-zinc-800 bg-zinc-400/50 hover:bg-zinc-400/35 dark:text-slate-800 dark:bg-slate-400/50 dark:hover:bg-slate-400/65',
    'danger' => 'text-white bg-red-500 hover:bg-red-400'
};
@endphp

<a href="{{ $href }}" class="group-button py-2 px-5 text-base font-medium inline-flex items-center gap-x-2 -ms-px first:rounded-s-xl first:ms-0 last:rounded-e-xl focus:z-10 disabled:opacity-50 disabled:pointer-events-none {{ $colorClasses }}" {{ $attributes }}>
    @if (isset($icon) && !empty($icon))
        @include ("forum::components.icons.{$icon}", ['size' => '5'])
    @endif
    {{ $label }}
</a>
