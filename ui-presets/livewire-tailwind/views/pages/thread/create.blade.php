<div>
    @include ('forum::components.loading-overlay')
    @include ('forum::components.breadcrumbs')

    <div class="flex justify-center items-center">
        <div class="grow max-w-screen-lg">
            <h1>{{ trans('forum::threads.new_thread') }} ({{ $category->title }})</h1>

            <div class="bg-white rounded-md shadow-md my-2 p-6">
                <form wire:submit="create">
                    <x-forum::form.input-text
                        id="title"
                        value=""
                        :label="trans('forum::general.title')"
                        wire:model="title" />

                    <x-forum::form.input-textarea
                        id="content"
                        wire:model="content" />

                    <div class="flex mt-6">
                        <div class="grow">
                            <x-forum::button
                                href="{{ URL::previous() }}"
                                intent="secondary"
                                label="{{ trans('forum::general.cancel') }}" />
                        </div>
                        <div class="grow text-right">
                            <x-forum::button :label="trans('forum::general.create')" type="submit" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
