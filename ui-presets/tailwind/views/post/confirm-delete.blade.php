@extends ('forum.master', ['breadcrumbs_append' => [trans_choice('forum::posts.delete', 1)]])

@section ('content')
    <div id="delete-post">
        <h2 class="text-3xl">{{ trans_choice('forum::posts.delete', 1) }}</h2>

        <hr>

        @include ('forum.post.partials.list', ['post' => $post, 'single' => true])

        <form method="POST" action="{{ Forum::route('post.delete', $post) }}">
            @csrf
            @method('DELETE')

            <div class="bg-white border rounded-md mb-3">
                <div class="p-4">

                    @if (config('forum.general.soft_deletes'))
                        <div class="form-check" v-if="selectedPostAction == 'delete'">
                            <input class="form-check-input" type="checkbox" name="permadelete" value="1" id="permadelete">
                            <label class="form-check-label" for="permadelete">
                                {{ trans('forum::general.perma_delete') }}
                            </label>
                        </div>
                    @else
                        {{ trans('forum::general.generic_confirm') }}
                    @endif
                </div>
            </div>

            <div class="flex justify-end items-center gap-2">
                <x-forum.button-link href="{{ URL::previous() }}">{{ trans('forum::general.cancel') }}</x-forum.button-link>
                <x-forum.button type="submit" class="bg-red-500 px-5">{{ trans('forum::general.delete') }}</x-forum.button>
            </div>
        </form>
    </div>
@stop
