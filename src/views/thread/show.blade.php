@extends ('forum::master')

@section ('content')
    <div id="thread">
        <h2>
            <span v-if="locked">[{{ trans('forum::threads.locked') }}]</span>
            <span v-if="pinned">[{{ trans('forum::threads.pinned') }}]</span>
            {{ $thread->title }}
        </h2>

        @include ('forum::partials.alert', ['type' => 'success'])

        @if (Forum::userCan(['api.v1.thread.update']))
            <div class="thread-tools dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="thread-actions" data-toggle="dropdown" aria-expanded="true">
                    {{ trans('forum::general.actions') }}
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li>
                        <a href="{{ $thread->updateRoute }}" data-method="PATCH">
                            {{ trans('forum::threads.lock') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ $thread->updateRoute }}" data-method="PATCH">
                            {{ trans('forum::threads.pin') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ $thread->deleteRoute }}" data-method="DELETE">
                            {{ trans('forum::general.delete') }}
                        </a>
                    </li>
                </ul>
            </div>
            <hr>
        @endif

        @if ($thread->userCanReply)
            <div class="row">
                <div class="col-xs-4">
                    <div class="btn-group" role="group">
                        <a href="{{ $thread->replyRoute }}" class="btn btn-default">{{ trans('forum::general.new_reply') }}</a>
                        <a href="#quick-reply" class="btn btn-default">{{ trans('forum::general.quick_reply') }}</a>
                    </div>
                </div>
                <div class="col-xs-8 text-right">
                    {!! $thread->pageLinks !!}
                </div>
            </div>
        @endif

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
                @foreach ($thread->postsPaginated as $post)
                    @include ('forum::post.partials.list', compact('post'))
                @endforeach
            </tbody>
        </table>

        {!! $thread->pageLinks !!}

        @if ($thread->userCanReply)
            <h3>{{ trans('forum::general.quick_reply') }}</h3>
            <div id="quick-reply">
                @include (
                    'forum::post.partials.edit',
                    [
                        'form_url'          => $thread->replyRoute,
                        'show_title_field'  => false,
                        'submit_label'      => trans('forum::general.reply')
                    ]
                )
            </div>
        @endif
    </div>

    <script>
    new Vue({
        el: '#thread',

        data: {
            locked: {{ $thread->locked }},
            pinned: {{ $thread->pinned }},
            deleted: {{ $thread->deleted }},
            message: null
        },

        ready: function() {
        },

        methods: {
        }
    });
    </script>
@overwrite
