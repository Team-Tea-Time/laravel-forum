{{-- $thread is passed as NULL to the master layout view to prevent it from showing in the breadcrumbs --}}
@extends('forum::master', ['thread' => null])

@section('content')
    <div class="d-flex flex-row justify-content-between mb-2">
        <h2>
            {{ $category->title }}
            @if ($category->description)
                <small>{{ $category->description }}</small>
            @endif
        </h2>
    </div>
    <div class="v-category-show">
        <div class="btn-group" role="group">
            @can('manageCategories')
                <button type="button" class="btn btn-secondary" @click="isEditModalOpen = true">{{ trans('forum::general.edit') }}</button>
            @endcan
            @can('delete', $category)
                <button type="button" class="btn btn-danger" @click="isDeleteModalOpen = true">{{ trans('forum::general.delete') }}</button>
            @endcan
        </div>

        <hr>

        @if (!$category->children->isEmpty())
            @foreach ($category->children as $subcategory)
                @include('forum::category.partials.list', ['category' => $subcategory])
            @endforeach
        @endif

        <div class="row">
            <div class="col col-xs-8">
                {!! $threads->render() !!}
            </div>
            <div class="col col-xs-4 text-right">
                @if ($category->accepts_threads)
                    @can('createThreads', $category)
                        <a href="{{ Forum::route('thread.create', $category) }}" class="btn btn-primary">{{ trans('forum::threads.new_thread') }}</a>
                    @endcan
                @endif
            </div>
        </div>

        @can('manageThreads', $category)
            <form action="{{ Forum::route('bulk.thread.update') }}" method="POST" data-actions-form>
                {!! csrf_field() !!}
                {!! method_field('delete') !!}
        @endcan

        @if ($category->accepts_threads)
            <table class="table table-thread mt-3">
                <thead>
                    <tr>
                        <th>{{ trans('forum::general.subject') }}</th>
                        <th class="col col-md-2 text-right">{{ trans('forum::general.replies') }}</th>
                        <th class="col col-md-2 text-right">{{ trans('forum::posts.last') }}</th>
                        @can('manageThreads', $category)
                            <th class="col col-md-1 text-right"><input type="checkbox" data-toggle-all></th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @if (!$threads->isEmpty())
                        @foreach ($threads as $thread)
                            <tr class="{{ $thread->trashed() ? "deleted" : "" }}">
                                <td>
                                    <p class="lead">
                                        @if ($thread->locked)
                                            <span class="badge badge-warning">{{ trans('forum::threads.locked') }}</span>
                                        @endif
                                        @if ($thread->pinned)
                                            <span class="badge badge-info">{{ trans('forum::threads.pinned') }}</span>
                                        @endif
                                        @if ($thread->userReadStatus && !$thread->trashed())
                                            <span class="badge badge-primary">{{ trans($thread->userReadStatus) }}</span>
                                        @endif
                                        @if ($thread->trashed())
                                            <span class="badge badge-danger">{{ trans('forum::general.deleted') }}</span>
                                        @endif
                                        <a href="{{ Forum::route('thread.show', $thread) }}">{{ $thread->title }}</a>
                                    </p>
                                    <p>{{ $thread->authorName }} <span class="text-muted">({{ $thread->posted }})</span></p>
                                </td>
                                @if ($thread->trashed())
                                    <td colspan="2">&nbsp;</td>
                                @else
                                    <td class="text-right">
                                        {{ $thread->reply_count }}
                                    </td>
                                    <td class="text-right">
                                        {{ $thread->lastPost->authorName }}
                                        <p class="text-muted">({{ $thread->lastPost->posted }})</p>
                                        <a href="{{ Forum::route('thread.show', $thread->lastPost) }}" class="btn btn-secondary btn-sm">{{ trans('forum::posts.view') }} &raquo;</a>
                                    </td>
                                @endif
                                @can('manageThreads', $category)
                                    <td class="text-right">
                                        <input type="checkbox" name="items[]" value="{{ $thread->id }}">
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>
                                {{ trans('forum::threads.none_found') }}
                            </td>
                            <td class="text-right" colspan="3">
                                @can('createThreads', $category)
                                    <a href="{{ Forum::route('thread.create', $category) }}">{{ trans('forum::threads.post_the_first') }}</a>
                                @endcan
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        @endif

        @can('manageThreads', $category)
                @include('forum::category.partials.thread-actions')
            </form>
        @endcan

        <div class="row">
            <div class="col col-xs-8">
                {!! $threads->render() !!}
            </div>
            <div class="col col-xs-4 text-right">
                @if ($category->accepts_threads)
                    @can('createThreads', $category)
                        <a href="{{ Forum::route('thread.create', $category) }}" class="btn btn-primary">{{ trans('forum::threads.new_thread') }}</a>
                    @endcan
                @endif
            </div>
        </div>

        @can('manageCategories')
            <transition name="slide-fade">
                <div class="modal" tabindex="-1" role="dialog" v-show="isEditModalOpen" @click="onClickModal">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content shadow-sm">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ trans('forum::general.edit') }}</h5>
                                <button type="button" class="close" aria-label="Close" @click="isEditModalOpen = false">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="{{ Forum::route('category.update', $category) }}" method="POST" data-actions-form>
                                @method('PATCH')
                                @csrf

                                <div class="modal-body">
                                    <div class="form-group hidden">
                                        <label for="new-title">{{ trans('forum::general.title') }}</label>
                                        <input type="text" id="new-title" name="title" value="{{ $category->title }}" class="form-control">
                                        <label for="new-description">{{ trans('forum::general.description') }}</label>
                                        <input type="text" id="new-description" name="description" value="{{ $category->description }}" class="form-control">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" @click="isEditModalOpen = false">{{ trans('forum::general.cancel') }}</button>
                                    <button type="submit" class="btn btn-primary pull-right">{{ trans('forum::general.proceed') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </transition>
        @endcan

        @can('delete', $category)
            <transition name="slide-fade">
                <div class="modal" tabindex="-1" role="dialog" v-show="isDeleteModalOpen" @click="onClickModal">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content shadow-sm">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ trans('forum::categories.actions') }}</h5>
                                <button type="button" class="close" aria-label="Close" @click="isEditModalOpen = false">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="{{ Forum::route('category.delete', $category) }}" method="POST" data-actions-form>
                                @method('DELETE')
                                @csrf

                                <div class="modal-body">
                                    <div class="form-group hidden">
                                        <label for="new-title">{{ trans('forum::general.title') }}</label>
                                        <input type="text" id="new-title" name="title" value="{{ $category->title }}" class="form-control">
                                        <label for="new-description">{{ trans('forum::general.description') }}</label>
                                        <input type="text" id="new-description" name="description" value="{{ $category->description }}" class="form-control">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" @click="isEditModalOpen = false">{{ trans('forum::general.cancel') }}</button>
                                    <button type="submit" class="btn btn-primary pull-right">{{ trans('forum::general.proceed') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </transition>
        @endcan
    </div>

    <script>

    new Vue({
        el: '.v-category-show',
        data: {
            isEditModalOpen: false,
            isDeleteModalOpen: false
        },
        methods: {
            onClickModal (event)
            {
                if (event.target.classList.contains('modal'))
                {
                    this.isEditModalOpen = false;
                    this.isDeleteModalOpen = false;
                }
            }
        }
    })
    </script>
@stop
