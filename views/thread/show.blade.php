@extends ('forum::master', ['thread' => null, 'breadcrumbs_append' => [$thread->title]])

@section ('content')
    <div id="thread" class="v-thread">
        <div class="d-flex flex-column flex-md-row justify-content-between">
            <h2 class="flex-grow-1">{{ $thread->title }}</h2>
            
            @can ('manageThreads', $category)
                <div>
                    @can ('deleteThreads', $category)
                        <div class="btn-group mr-3" role="group">
                            @if ($thread->trashed())
                                <a href="#" class="btn btn-secondary" data-open-modal="restore-thread">
                                    <i data-feather="refresh-cw"></i> {{ trans('forum::general.restore') }}
                                </a>
                            @else
                                <a href="#" class="btn btn-danger" data-open-modal="delete-thread">
                                    <i data-feather="trash"></i> {{ trans('forum::general.delete') }}
                                </a>
                            @endif
                        </div>
                    @endcan

                    <div class="btn-group" role="group">
                        @if (! $thread->trashed())
                            @can ('lockThreads', $category)
                                @if ($thread->locked)
                                    <a href="#" class="btn btn-secondary" data-open-modal="unlock-thread">
                                        <i data-feather="unlock"></i> {{ trans('forum::threads.unlock') }}
                                    </a>
                                @else
                                    <a href="#" class="btn btn-secondary" data-open-modal="lock-thread">
                                        <i data-feather="lock"></i> {{ trans('forum::threads.lock') }}
                                    </a>
                                @endif
                            @endcan
                            @can ('pinThreads', $category)
                                @if ($thread->pinned)
                                    <a class="btn btn-secondary" data-open-modal="unpin-thread">
                                        <i data-feather="arrow-down"></i> {{ trans('forum::threads.unpin') }}
                                    </a>
                                @else
                                    <a href="#" class="btn btn-secondary" data-open-modal="pin-thread">
                                        <i data-feather="arrow-up"></i> {{ trans('forum::threads.pin') }}
                                    </a>
                                @endif
                            @endcan
                            @can ('rename', $thread)
                                <a href="#" class="btn btn-secondary" data-open-modal="rename-thread">
                                    <i data-feather="edit-2"></i> {{ trans('forum::general.rename') }}</option>
                            @endcan
                            @can ('moveThreadsFrom', $category)
                                <a href="#" class="btn btn-secondary" data-open-modal="move-thread">
                                    <i data-feather="corner-up-right"></i> {{ trans('forum::general.move') }}
                                </a>
                            @endcan
                        @endif
                    </div>
                </div>
            @endcan
        </div>


        <div class="thread-badges">
            @if ($thread->trashed())
                <span class="badge badge-pill badge-danger">{{ trans('forum::general.deleted') }}</span>
            @endif
            @if ($thread->pinned)
                <span class="badge badge-pill badge-info">{{ trans('forum::threads.pinned') }}</span>
            @endif
            @if ($thread->locked)
                <span class="badge badge-pill badge-warning">{{ trans('forum::threads.locked') }}</span>
            @endif
        </div>

        <hr>

        @can ('deletePosts', $thread)
            <form action="{{ Forum::route('bulk.post.update') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" :value="postActionMethods[selectedPostAction]" />
        @endcan

        <div class="row">
            <div class="col col-xs-8">
                {{ $posts->links() }}
            </div>
            <div class="col col-xs-4 text-right">
                @can ('reply', $thread)
                <div class="btn-group" role="group">
                    <a href="{{ Forum::route('post.create', $thread) }}" class="btn btn-primary">
                        <i data-feather="message-square"></i> {{ trans('forum::general.new_reply') }}
                    </a>
                    <a href="#quick-reply" class="btn btn-primary">
                        {{ trans('forum::general.quick_reply') }}
                    </a>
                </div>
                @endcan
            </div>
        </div>

        @can ('deletePosts', $thread)
            <div class="text-right p-1">
                <div class="form-check">
                    <label for="selectAllPosts">
                        {{ trans('forum::posts.select_all') }}
                    </label>
                    <input type="checkbox" value="" id="selectAllPosts" class="align-middle" @click="toggleAll" :checked="selectedPosts.length == posts.data.length">
                </div>
            </div>
        @endcan
        
        @foreach ($posts as $post)
            @include ('forum::post.partials.list', compact('post'))
        @endforeach

        @can ('deletePosts', $thread)
                <div class="fixed-bottom-right pb-xs-0 pr-xs-0 pb-sm-3 pr-sm-3">
                    <transition name="fade">
                        <div class="card text-white bg-secondary shadow-sm" v-if="selectedPosts.length">
                            <div class="card-header text-center">
                                {{ trans('forum::general.with_selection') }}
                            </div>
                            <div class="card-body">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="bulk-actions">{{ trans_choice('forum::general.actions', 1) }}</label>
                                    </div>
                                    <select class="custom-select" id="bulk-actions" v-model="selectedPostAction">
                                        <option value="delete">{{ trans('forum::general.delete') }}</option>
                                        <option value="restore">{{ trans('forum::general.restore') }}</option>
                                        <option value="permadelete">{{ trans('forum::general.perma_delete') }}</option>
                                    </select>
                                    <div class="input-group-breadcrumbs_append">
                                        <button type="submit" class="btn btn-primary" @click="submitPosts">{{ trans('forum::general.proceed') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
            </form>
        @endcan

        {{ $posts->links() }}

        @can ('reply', $thread)
            <h3>{{ trans('forum::general.quick_reply') }}</h3>
            <div id="quick-reply">
                <form method="POST" action="{{ Forum::route('post.store', $thread) }}">
                    @csrf

                    <div class="form-group">
                        <textarea name="content" class="form-control">{{ old('content') }}</textarea>
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary px-5">{{ trans('forum::general.reply') }}</button>
                    </div>
                </form>
            </div>
        @endcan

        @can ('manageThreads', $category)
            @component('forum::modal-form')
                @slot('key', 'thread-actions')
                @slot('title', trans('forum::threads.actions'))
                @slot('route', Forum::route('thread.update', $thread))
                @slot('method', 'PATCH')

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="action">{{ trans_choice('forum::general.actions', 1) }}</label>
                    </div>
                    <select class="custom-select" name="action" id="action" v-model="selectedThreadAction">
                        @if (! $thread->trashed())
                            @can ('lockThreads', $category)
                                @if ($thread->locked)
                                    <option value="unlock">{{ trans('forum::threads.unlock') }}</option>
                                @else
                                    <option value="lock">{{ trans('forum::threads.lock') }}</option>
                                @endif
                            @endcan
                            @can ('pinThreads', $category)
                                @if ($thread->pinned)
                                    <option value="unpin">{{ trans('forum::threads.unpin') }}</option>
                                @else
                                    <option value="pin">{{ trans('forum::threads.pin') }}</option>
                                @endif
                            @endcan
                            @can ('rename', $thread)
                                <option value="rename">{{ trans('forum::general.rename') }}</option>
                            @endcan
                            @can ('moveThreadsFrom', $category)
                                <option value="move">{{ trans('forum::general.move') }}</option>
                            @endcan
                        @endif

                        @can ('deleteThreads', $category)
                            @if ($thread->trashed())
                                <option value="restore">{{ trans('forum::general.restore') }}</option>
                            @else
                                <option value="delete">{{ trans('forum::general.delete') }}</option>
                            @endif
                            <option value="permadelete">{{ trans('forum::general.perma_delete') }}</option>
                        @endcan
                    </select>
                </div>
                <div class="input-group" v-if="selectedThreadAction == 'move'">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="category-id">{{ trans_choice('forum::categories.category', 1) }}</label>
                    </div>
                    <select name="category_id" id="category-id" class="custom-select">
                        @include ('forum::category.partials.options', ['hide' => $thread->category])
                    </select>
                </div>
                <div class="input-group" v-if="selectedThreadAction == 'rename'">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="new-title">{{ trans('forum::general.title') }}</label>
                    </div>
                    <input type="text" name="title" value="{{ $thread->title }}" class="form-control">
                </div>

                @slot('actions')
                    <button type="submit" class="btn btn-primary" @click="submitThread">{{ trans('forum::general.proceed') }}</button>
                @endslot
            @endcomponent
        @endcan
    </div>

    <style>
    .thread-badges .badge
    {
        font-size: 100%;
    }
    </style>

    <script>
    new Vue({
        el: '.v-thread',
        name: 'Thread',
        data: {
            thread: @json($thread),
            posts: @json($posts),
            threadActionMethods: {
                'delete': 'DELETE',
                'permadelete': 'DELETE',
                'restore': 'PATCH',
                'move': 'PATCH',
                'unlock': 'PATCH',
                'lock': 'PATCH',
                'unpin': 'PATCH',
                'pin': 'PATCH',
                'rename': 'PATCH'
            },
            postActionMethods: {
                'delete': 'DELETE',
                'permadelete': 'DELETE',
                'restore': 'PATCH'
            },
            selectedPostAction: 'delete',
            selectedPosts: [],
            selectedThreadAction: null
        },
        computed: {
            postIds ()
            {
                return this.posts.data.map(post => post.id);
            }
        },
        created ()
        {
            this.selectedThreadAction = this.thread.locked ? 'unlock' : 'lock';
        },
        methods: {
            toggleAll ()
            {
                this.selectedPosts = (this.selectedPosts.length < this.posts.data.length) ? this.postIds : [];
            },
            submitThread (event)
            {
                if (this.threadActionMethods[this.selectedThreadAction] === 'DELETE' && ! confirm("{{ trans('forum::general.generic_confirm') }}"))
                {
                    event.preventDefault();
                }
            },
            submitPosts (event)
            {
                if (this.postActionMethods[this.selectedPostAction] === 'DELETE' && ! confirm("{{ trans('forum::general.generic_confirm') }}"))
                {
                    event.preventDefault();
                }
            }
        }
    });
    </script>
@stop
