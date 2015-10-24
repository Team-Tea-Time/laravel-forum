<tr>
    <td {{ $category->threadsEnabled ? '' : 'colspan=5'}}>
        <p class="{{ isset($titleClass) ? $titleClass : '' }}"><a href="{{ $category->route }}">{{ $category->title }}</a></p>
        <span class="text-muted">{{ $category->description }}</span>
    </td>
    @if ($category->threadsEnabled)
        <td>{{ $category->threadCount }}</td>
        <td>{{ $category->postCount }}</td>
        <td>
            @if ($category->newestThread)
                <a href="{{ $category->newestThread->route }}">
                    {{ $category->newestThread->title }}
                    ({{ $category->newestThread->authorName }})
                </a>
            @endif
        </td>
        <td>
            @if ($category->latestActiveThread)
                <a href="{{ $category->latestActiveThread->lastPost->url }}">
                    {{ $category->latestActiveThread->title }}
                    ({{ $category->latestActiveThread->lastPost->authorName }})
                </a>
            @endif
        </td>
    @endif
</tr>
