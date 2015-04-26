@extends('forum::layouts.master')

@section('content')
@include('forum::partials.breadcrumbs')

<h2>
    @if ($thread->locked)
        [{{ trans('forum::base.locked') }}]
    @endif
    @if ($thread->pinned)
        [{{ trans('forum::base.pinned') }}]
    @endif
    {{{ $thread->title }}}
</h2>

@if ($thread->canLock || $thread->canPin || $thread->canDelete)
    <div class="thread-tools dropdown">
        <button class="btn btn-default dropdown-toggle" type="button" id="thread-actions" data-toggle="dropdown" aria-expanded="true">
            {{ trans('forum::base.actions') }}
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" role="menu">
            @if ($thread->canLock)
                <li><a href="{{ $thread->lockRoute }}" data-method="post">{{ trans('forum::base.lock_thread') }}</a></li>
            @endif
            @if ($thread->canPin)
                <li><a href="{{ $thread->pinRoute }}" data-method="post">{{ trans('forum::base.pin_thread') }}</a></li>
            @endif
            @if ($thread->canDelete)
                <li><a href="{{ $thread->deleteRoute }}" data-confirm data-method="delete">{{ trans('forum::base.delete_thread') }}</a></li>
            @endif
        </ul>
    </div>
    <hr>
@endif

@if ($thread->canReply)
    <div class="row">
        <div class="col-xs-4">
            <div class="btn-group" role="group">
                <a href="{{ $thread->replyRoute }}" class="btn btn-default">{{ trans('forum::base.new_reply') }}</a>
                <a href="#quick-reply" class="btn btn-default">{{ trans('forum::base.quick_reply') }}</a>
            </div>
        </div>
        <div class="col-xs-8 text-right">
            {!! $thread->pageLinks !!}
        </div>
    </div>
@endif

<table class="table">
    <thead>
        <tr>
            <th class="col-md-2">
                {{ trans('forum::base.author') }}
            </th>
            <th>
                {{ trans('forum::base.post') }}
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($thread->postsPaginated as $post)
            @include('forum::partials.post', compact('post'))
        @endforeach
    </tbody>
</table>

{!! $thread->pageLinks !!}

@if ($thread->canReply)
    <h3>{{ trans('forum::base.quick_reply') }}</h3>
    <div id="quick-reply">
        @include(
            'forum::partials.forms.post',
            array(
                'form_url'            => $thread->replyRoute,
                'form_classes'        => '',
                'show_title_field'    => false,
                'post_content'        => '',
                'submit_label'        => trans('forum::base.reply'),
                'cancel_url'          => ''
            )
        )
    </div>
@endif
@overwrite
