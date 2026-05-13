@props([
    'type' => 'default'
])

@php
switch($type) {
    case('info'):
        $color = 'border-cyan-200 bg-cyan-50 text-cyan-700 dark:border-cyan-400/20 dark:bg-cyan-400/10 dark:text-cyan-200';
        break;

    case('danger'):
        $color = 'border-rose-200 bg-rose-50 text-rose-700 dark:border-rose-400/20 dark:bg-rose-400/10 dark:text-rose-200';
        break;

    case('warning'):
        $color = 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-400/20 dark:bg-amber-400/10 dark:text-amber-200';
        break;

    case('success'):
        $color = 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-200';
        break;

    default:
        $color = 'border-slate-200 bg-slate-100 text-slate-600 dark:border-white/10 dark:bg-white/10 dark:text-slate-200';
        break;
}
@endphp

<span {{ $attributes->merge(['class' => "$color forum-badge"]) }}>
    {{ $slot }}
</span>
