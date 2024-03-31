<time x-data="timestamp"
    x-effect="setNaturalDiff(new Date('{{ $carbon }}'), $store.time.now)"
    class="timestamp"
    datetime="{{ $carbon }}"
    title="{{ $carbon->toDayDateTimeString() }}">
    <span x-text="naturalDiff"></span>
</time>

@script
<script type="module">
Alpine.data('timestamp', () => {
    return {
        naturalDiff: '',
        setNaturalDiff(then, now) {
            this.naturalDiff = dateFormatDistance(then, $store.time.now, { addSuffix: true });
        }
    }
});
</script>
@endscript
