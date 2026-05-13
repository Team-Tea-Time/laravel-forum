@php
$colorClasses = match ($type) {
    'primary', '', null => 'border-cyan-200 bg-cyan-50 text-cyan-800 dark:border-cyan-400/20 dark:bg-cyan-400/10 dark:text-cyan-100',
    'success', => 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-100',
    'warning', => 'border-amber-200 bg-amber-50 text-amber-900 dark:border-amber-400/20 dark:bg-amber-400/10 dark:text-amber-100',
    'danger' => 'border-rose-200 bg-rose-50 text-rose-800 dark:border-rose-400/20 dark:bg-rose-400/10 dark:text-rose-100'
};
@endphp

<div class="alert alert-{{ $type }} alert-dismissable my-4 flex justify-between gap-4 rounded-lg border p-4 text-sm font-medium shadow-sm {{ $colorClasses }}">
    <div class="message leading-6">
        {!! $message !!}
    </div>
    <button type="button" data-dismiss="alert" aria-hidden="true" class="shrink-0 rounded-full p-1 hover:bg-black/5 dark:hover:bg-white/10">
        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
        </svg>
    </button>
</div>
