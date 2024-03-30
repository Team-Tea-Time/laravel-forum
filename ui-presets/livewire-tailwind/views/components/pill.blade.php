<span class="inline-block
    rounded-full
    text-base
    text-nowrap
    align-middle
    {{ $bgColor ?? 'bg-zinc-300 dark:bg-slate-500' }}
    {{ $textColor ?? 'text-zinc-600 dark:text-slate-200' }}
    {{ $padding ?? 'px-2' }}
    {{ $margin ?? 'mx-2' }}">
    @if (isset($icon))
        @include ("forum::components.icons.{$icon}")
    @endif
    {{ $text }}
</span>
