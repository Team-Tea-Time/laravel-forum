<div class="relative w-full sm:w-72">
    <select {{ $attributes->merge(['class' => 'forum-select appearance-none pr-10']) }}>
        {{ $slot }}
    </select>
    <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">v</span>
</div>
