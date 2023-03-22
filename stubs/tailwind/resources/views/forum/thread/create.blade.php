@extends ('forum.master', ['breadcrumbs_append' => [trans('forum::threads.new_thread')]])

@section ('content')
    <div id="create-thread">
        <h2 class="text-3xl">{{ trans('forum::threads.new_thread') }} ({{ $category->title }})</h2>

        <form method="POST" action="{{ Forum::route('thread.store', $category) }}">
            @csrf

            <div class="mb-3">
                <x-forum.label for="title">{{ trans('forum::general.title') }}</x-forum.label>
                <x-forum.input type="text" name="title" value="{{ old('title') }}" class="w-full" />
            </div>

            <div class="mb-3">
                <x-forum.textarea name="content" class="w-full">{{ old('content') }}</x-forum.textarea>
            </div>

            <div class="text-end">
                <x-forum.button-link href="{{ URL::previous() }}" class="bg-gray-500">{{ trans('forum::general.cancel') }}</x-forum.button-link>
                <x-forum.button type="submit">{{ trans('forum::general.create') }}</x-forum.button>
            </div>
        </form>
    </div>
@stop
