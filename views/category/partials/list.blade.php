<div class="category list-group my-4">
    <div class="list-group-item shadow-sm">
        <div class="row align-items-center text-center">
            <div class="col-sm text-md-left">
                <h5 class="card-title">
                    <a href="{{ Forum::route('category.show', $category) }}" style="color: {{ $category->color }};">{{ $category->title }}</a>
                </h5>
                <p class="card-text text-muted">{{ $category->description }}</p>
            </div>
            <div class="col-sm text-md-right">
                <span class="badge badge-primary" style="background: {{ $category->color }};">
                    {{ trans_choice('forum::threads.thread', 2) }}: {{ $category->thread_count }}
                </span>
                <br>
                <span class="badge badge-primary" style="background: {{ $category->color }};">
                    {{ trans_choice('forum::posts.post', 2) }}: {{ $category->post_count }}
                </span>
            </div>
            <div class="col-sm text-md-right text-muted">
                <em><a href="#" style="color: {{ $category->color }};">Saltuna</a> 3 hours ago</em>
                <br>
                <em><a href="#" style="color: {{ $category->color }};">Re: Saltuna</a> 2 hours ago</em>
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

    @if ($category->children->count() > 0)
        <div class="subcategories">
            @foreach ($category->children as $subcategory)
                <div class="list-group-item">
                    <div class="row">
                        <div class="col-sm text-md-left">
                            <a href="{{ Forum::route('category.show', $subcategory) }}" style="color: {{ $subcategory->color }};">{{ $subcategory->title }}</a>
                            <div class="text-muted">{{ $subcategory->description }}</div>
                        </div>
                        <div class="col-sm text-md-right">
                            <span class="badge badge-primary" style="background: {{ $subcategory->color }};">
                                {{ trans_choice('forum::threads.thread', 2) }}: {{ $subcategory->thread_count }}
                            </span>
                            <br>
                            <span class="badge badge-primary" style="background: {{ $subcategory->color }};">
                                {{ trans_choice('forum::posts.post', 2) }}: {{ $subcategory->post_count }}
                            </span>
                        </div>
                        <div class="col-sm text-md-right text-muted">
                            <em><a href="#" style="color: {{ $subcategory->color }};">Saltuna</a> 3 hours ago</em>
                            <br>
                            <em><a href="#" style="color: {{ $subcategory->color }};">Re: Saltuna</a> 2 hours ago</em>
                            @if ($subcategory->newestThread)
                                <div>
                                    {{ trans('forum::threads.newest') }}:
                                    <a href="{{ Forum::route('thread.show', $subcategory->newestThread) }}">
                                        {{ $subcategory->newestThread->title }}
                                    </a>
                                    ({{ $subcategory->newestThread->authorName }})
                                </div>
                            @endif
                            @if ($subcategory->latestActiveThread)
                                <div>
                                    {{ trans('forum::posts.last') }}:
                                    <a href="{{ Forum::route('thread.show', $subcategory->latestActiveThread->lastPost) }}">
                                        {{ $subcategory->latestActiveThread->title }}
                                    </a>
                                    ({{ $subcategory->latestActiveThread->lastPost->authorName }})
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
.category .subcategories {
    background: #fff;
}

.category .subcategories .list-group-item {
    opacity: 0.5;
    transition: opacity .3s ease;
}

.category .subcategories .list-group-item:first-child {
    border-radius: 0;
}

.category:hover .subcategories .list-group-item {
    opacity: 1;
}
</style>
