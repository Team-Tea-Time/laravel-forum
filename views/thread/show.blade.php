@extends ('forum::master')

@section ('content')
    <div id="thread">
        <h2>
            <span v-if="locked">[{{ trans('forum::threads.locked') }}]</span>
            <span v-if="pinned">[{{ trans('forum::threads.pinned') }}]</span>
            {{ $thread->title }}
        </h2>

        @can (['lockThreads', 'pinThreads', 'moveThreads', 'deleteThreads'], $category)
            <div class="thread-tools dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="thread-actions" data-toggle="dropdown" aria-expanded="true">
                    {{ trans('forum::general.actions') }}
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li>
                        <a href="{{ $thread->updateRoute }}" v-on="click: toggleLock">
                            <span v-if="!locked">{{ trans('forum::threads.lock') }}</span>
                            <span v-if="locked">{{ trans('forum::threads.unlock') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $thread->updateRoute }}" v-on="click: togglePin">
                            <span v-if="!pinned">{{ trans('forum::threads.pin') }}</span>
                            <span v-if="pinned">{{ trans('forum::threads.unpin') }}</span>
                        </a>
                    </li>
                    @can ('deleteThreads', $category)
                        <li>
                            <a href="#" v-on="click: toggleDelete">
                                <span v-if="!deleted">{{ trans('forum::general.delete') }}</span>
                                <span v-if="deleted">{{ trans('forum::general.restore') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" v-on="click: permaDelete">
                                {{ trans('forum::general.perma_delete') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </div>
            <hr>
        @endcan

        <alert v-repeat="alerts" v-transition="fade"></alert>

        @can ('reply', $thread)
            <div class="row">
                <div class="col-xs-4" v-if="!locked && !deleted">
                    <div class="btn-group" role="group">
                        <a href="{{ $thread->replyRoute }}" class="btn btn-default">{{ trans('forum::general.new_reply') }}</a>
                        <a href="#quick-reply" class="btn btn-default">{{ trans('forum::general.quick_reply') }}</a>
                    </div>
                </div>
                <div class="col-xs-8 text-right">
                    {!! $thread->pageLinks !!}
                </div>
            </div>
        @endcan

        <table class="table" v-class="deleted: deleted">
            <thead>
                <tr>
                    <th class="col-md-2">
                        {{ trans('forum::general.author') }}
                    </th>
                    <th>
                        {{ trans_choice('forum::posts.post', 1) }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($posts as $post)
                    @include ('forum::post.partials.list', compact('post'))
                @endforeach
            </tbody>
        </table>

        {!! $thread->pageLinks !!}

        @can ('createThreads', $category)
            <div v-if="!locked && !deleted">
                <h3>{{ trans('forum::general.quick_reply') }}</h3>
                <div id="quick-reply">
                    @include (
                        'forum::post.partials.edit',
                        [
                            'form_url'          => $thread->replyRoute,
                            'show_title_field'  => false,
                            'submit_label'      => trans('forum::general.reply'),
                            'post'              => null
                        ]
                    )
                </div>
            </div>
        @endcan
    </div>

    <script>
    new Forum({
        el: '#thread',

        data: {
            locked: {{ $thread->locked }},
            pinned: {{ $thread->pinned }},
            deleted: {{ $thread->deleted }},
            categoryRoute: '{{ $thread->category->route }}',
            updateRoute: '{{ $thread->updateRoute }}',
            deleteRoute: '{{ $thread->deleteRoute }}',
            forceDeleteRoute: '{{ $thread->forceDeleteRoute }}',
            restoreRoute: '{{ $thread->restoreRoute }}',
            alerts: []
        },

        methods: {
            toggleLock: function (e) {
                e.preventDefault();
                Pace.restart();
                this.$http.put(this.updateRoute, { locked: !this.locked }, function (response) {
                    this.createAlert('success', response.message);
                    this.$set('locked', response.data.locked);
                });
            },
            togglePin: function (e) {
                e.preventDefault();
                Pace.restart();
                this.$http.put(this.updateRoute, { pinned: !this.pinned }, function (response) {
                    this.createAlert('success', response.message);
                    this.$set('pinned', response.data.pinned);
                });
            },
            toggleDelete: function (e) {
                e.preventDefault();
                Pace.restart();
                if (!this.deleted) {
                    if (!confirm(confirmMessage)) {
                        return false;
                    }

                    this.$http.delete(this.deleteRoute, function (response) {
                        this.createAlert('success', response.message);
                        this.$set('deleted', 1);
                    });
                } else {
                    this.$http.patch(this.restoreRoute, function (response) {
                        this.createAlert('success', response.message);
                        this.$set('deleted', 0);
                    }, { emulateHTTP: true });
                }
            },
            permaDelete: function (e) {
                e.preventDefault();
                Pace.restart();
                if (!confirm(confirmMessage)) {
                    return false;
                }
                this.$http.delete(this.forceDeleteRoute, function (response) {
                    window.location.replace('{{ $thread->category->route }}');
                });
            }
        }
    });
    </script>
@overwrite