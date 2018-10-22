<div class="card category {{ isset($cardClass) ? $cardClass : null }}">
    <div class="card-body">
        <div class="row align-items-center text-center">
            <div class="col-sm text-md-left">
                <h5 class="card-title"><a href="{{ Forum::route('category.show', $category) }}">{{ $category->title }}</a></h5>
                <p class="card-text text-muted">{{ $category->description }}</p>
            </div>
            <div class="col-sm text-md-right">
                <span class="badge badge-primary">
                    {{ trans_choice('forum::threads.thread', 2) }}: {{ $category->thread_count }}
                </span><br>
                <span class="badge badge-primary">
                    {{ trans_choice('forum::posts.post', 2) }}: {{ $category->post_count }}
                </span>
            </div>
            <div class="col-sm text-md-right text-muted">
                @if ($category->newestThread)
                    <div>
                        {{ trans('forum::threads.newest') }}:
                        <a href="{{ Forum::route('thread.show', $category->newestThread) }}">
                            {{ $category->newestThread->title }}
                        </a>
                        ({{ $category->newestThread->authorName }})
                    </div>
                @endif
                @if ($category->latestActiveThread)
                    <div>
                        {{ trans('forum::posts.last') }}:
                        <a href="{{ Forum::route('thread.show', $category->latestActiveThread->lastPost) }}">
                            {{ $category->latestActiveThread->title }}
                        </a>
                        ({{ $category->latestActiveThread->lastPost->authorName }})
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- <tr>
    <td {{ $category->threadsEnabled ? '' : 'colspan=5' }}>
        <span class="text-muted">{{ $category->description }}</span>
    </td>
    @if ($category->threadsEnabled)
        <td>{{ $category->thread_count }}</td>
        <td>{{ $category->post_count }}</td>
        <td>
            @if ($category->newestThread)
                <a href="{{ Forum::route('thread.show', $category->newestThread) }}">
                    {{ $category->newestThread->title }}
                    ({{ $category->newestThread->authorName }})
                </a>
            @endif
        </td>
        <td>
            @if ($category->latestActiveThread)
                <a href="{{ Forum::route('thread.show', $category->latestActiveThread->lastPost) }}">
                    {{ $category->latestActiveThread->title }}
                    ({{ $category->latestActiveThread->lastPost->authorName }})
                </a>
            @endif
        </td>
    @endif
</tr> -->
