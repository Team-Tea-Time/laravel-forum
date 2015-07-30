<tr>
    <td>
        <a href="{{ $subcategory->route }}">{{ $subcategory->title }}</a>
        <br>
        {{ $subcategory->subtitle }}
        @if ($subcategory->newestThread)
            <div class="text-muted">
                <br>
                {{ trans('forum::threads.newest') }}:
                <a href="{{ $subcategory->newestThread->route }}">
                    {{ $subcategory->newestThread->title }}
                    ({{ $subcategory->newestThread->authorName }})</a>
                <br>
                {{ trans('forum::posts.last') }}:
                <a href="{{ $subcategory->latestActiveThread->lastPost->url }}">
                    {{ $subcategory->latestActiveThread->title }}
                    ({{ $subcategory->latestActiveThread->lastPost->authorName }})
                </a>
            </div>
        @endif
    </td>
    <td>{{ $subcategory->threadCount }}</td>
    <td>{{ $subcategory->postCount }}</td>
</tr>
