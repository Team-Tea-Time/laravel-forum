@extends ('forum::master', ['thread' => null, 'breadcrumb_other' => trans('forum::threads.new_updated')])

@section ('content')
  <div id="new-posts">
      <h2>{{ trans('forum::threads.new_updated') }}</h2>

      @if (!$threads->isEmpty())
          <table class="table table-index">
              <thead>
                  <tr>
                      <th>{{ trans('forum::general.subject') }}</th>
                      <th class="col col-md-2">{{ trans('forum::general.replies') }}</th>
                      <th class="col col-md-2 text-right">{{ trans('forum::posts.last') }}</th>
                  </tr>
              </thead>
              <tbody>
                  @foreach ($threads as $thread)
                      <tr>
                          <td>
                              <span class="pull-right">
                                  @if ($thread->locked)
                                      <span class="badge badge-danger">{{ trans('forum::threads.locked') }}</span>
                                  @endif
                                  @if ($thread->pinned)
                                      <span class="badge badge-info">{{ trans('forum::threads.pinned') }}</span>
                                  @endif
                                  @if ($thread->userReadStatus)
                                      <span class="badge badge-primary">{{ trans($thread->userReadStatus) }}</span>
                                  @endif
                              </span>
                              <p class="lead">
                                  <a href="{{ Forum::route('thread.show', $thread) }}">{{ $thread->title }}</a>
                              </p>
                              <p>
                                  {{ $thread->authorName }}
                                  <span class="text-muted">(<em><a href="{{ Forum::route('category.show', $thread->category) }}">{{ $thread->category->title }}</a></em>, {{ $thread->posted }})</span>
                              </p>
                          </td>
                          <td>
                              {{ $thread->reply_count }}
                          </td>
                          <td class="text-right">
                              {{ $thread->lastPost->authorName }}
                              <p class="text-muted">({{ $thread->lastPost->posted }})</p>
                              <a href="{{ Forum::route('thread.show', $thread->lastPost) }}" class="btn btn-primary btn-xs">{{ trans('forum::posts.view') }} &raquo;</a>
                          </td>
                      </tr>
                  @endforeach
              </tbody>
          </table>

          @can ('markNewThreadsAsRead')
              <div class="text-center">
                  <form action="{{ Forum::route('mark-new') }}" method="POST" data-confirm>
                      {!! csrf_field() !!}
                      {!! method_field('patch') !!}
                      <button class="btn btn-primary btn-small">{{ trans('forum::general.mark_read') }}</button>
                  </form>
              </div>
          @endcan
      @else
          <p class="text-center">
              {{ trans('forum::threads.none_found') }}
          </p>
      @endif
  </div>
@stop
