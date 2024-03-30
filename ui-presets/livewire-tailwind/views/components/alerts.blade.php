<div x-data="alerts" @alert.window="onReceiveAlert" class="grid place-content-center fixed bottom-4 left-0 right-0 z-50">
    <template x-for="alert in list" :key="alert.id">
        <div
            x-show="alert.show"
            x-transition:enter="transition-all ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition-all ease-in duration-300"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2"
            class="min-w-80 max-w-96 px-6 py-4 mb-4 text-lg text-center rounded-md"
            :class="alert.classes">
            <span x-text="alert.message"></span>
        </div>
    </template>
</div>

@script
<script>
const SHOW_DURATION = 4000;
const FADE_DURATION = 300;
const TICK_INTERVAL = 250;

Alpine.data('alerts', () => {
    return {
        list: [],
        baseClasses: '',
        styleMap: {
            'success': 'bg-green-400 text-green-900',
            'warning': 'bg-orange-400 text-orange-900',
        },
        init() {
            setInterval(() => {
                for (let i = this.list.length - 1; i >= 0; --i) {
                    if (this.list[i].showFor === SHOW_DURATION) {
                        this.list[i].show = true;
                    }

                    this.list[i].showFor -= TICK_INTERVAL;

                    if (this.list[i].showFor <= FADE_DURATION) {
                        this.list[i].show = false;
                    }

                    if (this.list[i].showFor <= 0) {
                        this.list.splice(i, 1);
                    }
                }
            }, TICK_INTERVAL);
        },
        onReceiveAlert(event) {
            this.list.unshift({
                id: Math.random().toString(36).slice(2, 7),
                show: false,
                type: event.detail.type,
                message: event.detail.message,
                showFor: SHOW_DURATION,
                classes: this.styleMap[event.detail.type]
            });
        }
    }
});
</script>
@endscript
