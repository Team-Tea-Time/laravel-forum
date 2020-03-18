@extends ('forum::master')

@section ('content')
    <div id="thread">
        <h2>
            @if ($thread->trashed())
                <span class="badge badge-danger">{{ trans('forum::general.deleted') }}</span>
            @endif
            @if ($thread->locked)
                <span class="badge badge-warning">{{ trans('forum::threads.locked') }}</span>
            @endif
            @if ($thread->pinned)
                <span class="badge badge-info">{{ trans('forum::threads.pinned') }}</span>
            @endif
            {{ $thread->title }}
        </h2>

        <hr>

        @can ('manageThreads', $category)
            <form action="{{ Forum::route('thread.update', $thread) }}" method="POST" data-actions-form>
                @csrf
                @method('patch')

                @include ('forum::thread.partials.actions')
            </form>
        @endcan

        @can ('deletePosts', $thread)
            <form action="{{ Forum::route('bulk.post.update') }}" method="POST" class="v-bulk-post-update-form">
                @csrf
                <input type="hidden" name="_method" :value="actionMethods[selectedAction]" />
        @endcan

        <div class="row">
            <div class="col col-xs-4">
                @can ('reply', $thread)
                    <div class="btn-group" role="group">
                        <a href="{{ Forum::route('post.create', $thread) }}" class="btn btn-primary">{{ trans('forum::general.new_reply') }}</a>
                        <a href="#quick-reply" class="btn btn-primary">{{ trans('forum::general.quick_reply') }}</a>
                    </div>
                @endcan
            </div>
            <div class="col col-xs-8 text-right">
                {!! $posts->links() !!}
            </div>
        </div>

        @can ('deletePosts', $thread)
            <div class="text-right p-1">
                <div class="form-check">
                    <label for="selectAllPosts">
                        {{ trans('forum::posts.select_all') }}
                    </label>
                    <input type="checkbox" value="" id="selectAllPosts" @click="toggleAll" :checked="selectedPosts.length == posts.data.length">
                </div>
            </div>
        @endcan
        
        @foreach ($posts as $post)
            @include ('forum::post.partials.list', compact('post'))
        @endforeach

        @can ('deletePosts', $thread)
                <transition name="fade">
                    <div class="card text-white bg-secondary fixed-bottom rounded-0" v-if="selectedPosts.length">
                        <div class="card-header text-center">
                            {{ trans('forum::general.with_selection') }}
                        </div>
                        <div class="card-body">
                            <div class="container">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="bulk-actions">{{ trans_choice('forum::general.actions', 1) }}</label>
                                    </div>
                                    <select class="custom-select" id="bulk-actions" v-model="selectedAction">
                                        <option value="delete">{{ trans('forum::general.delete') }}</option>
                                        <option value="restore">{{ trans('forum::general.restore') }}</option>
                                        <option value="permadelete">{{ trans('forum::general.perma_delete') }}</option>
                                    </select>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary" @click="submit">{{ trans('forum::general.proceed') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>
            </form>
        @endcan

        {!! $posts->links() !!}

        @can ('reply', $thread)
            <h3>{{ trans('forum::general.quick_reply') }}</h3>
            <div id="quick-reply">
                <form method="POST" action="{{ Forum::route('post.store', $thread) }}">
                    {!! csrf_field() !!}

                    <div class="form-group">
                        <textarea name="content" class="form-control">{{ old('content') }}</textarea>
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary px-5">{{ trans('forum::general.reply') }}</button>
                    </div>
                </form>
            </div>
        @endcan
    </div>

    <script>
    new Vue({
        el: '.v-bulk-post-update-form',
        name: 'BulkPostUpdateForm',
        data: {
            posts: @json($posts),
            actionMethods: {
                'delete': 'DELETE',
                'permadelete': 'DELETE',
                'restore': 'PATCH'
            },
            actionsRequiringConfirmation: ['delete', 'permadelete'],
            selectedAction: 'delete',
            selectedPosts: []
        },
        computed: {
            postIds ()
            {
                return this.posts.data.map(post => post.id);
            }
        },
        methods: {
            toggleAll ()
            {
                this.selectedPosts = (this.selectedPosts.length < this.posts.data.length) ? this.postIds : [];
            },
            submit (event)
            {
                if (this.actionsRequiringConfirmation.includes(this.selectedAction) && ! confirm("{{ trans('forum::general.generic_confirm') }}"))
                {
                    event.preventDefault();
                }
            }
        }
    });
    </script>
@stop
