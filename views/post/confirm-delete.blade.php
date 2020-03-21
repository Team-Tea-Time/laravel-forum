@extends ('forum::master', ['breadcrumbs_append' => [trans_choice('forum::posts.delete', 1)]])

@section ('content')
    <div id="delete-post">
        <h2 class="flex-grow-1">{{ trans_choice('forum::posts.delete', 1) }}</h2>

        <hr>

        @include ('forum::post.partials.list', ['post' => $post, 'single' => true])

        <form method="POST" action="{{ Forum::route('post.destroy', $post) }}">
            @csrf
            @method('DELETE')
            
            <div class="card mb-3">
                <div class="card-body">
                    {{ trans('forum::general.generic_confirm') }}
                </div>
            </div>

            <div class="text-right">
                <a href="{{ URL::previous() }}" class="btn btn-link">{{ trans('forum::general.cancel') }}</a>
                <button type="submit" class="btn btn-danger px-5">{{ trans('forum::general.delete') }}</button>
            </div>
        </form>
    </div>
@stop
