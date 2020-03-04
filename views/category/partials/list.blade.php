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
                @if ($category->newestThread)
                    <div>
                        <a href="{{ Forum::route('thread.show', $category->newestThread) }}">{{ $category->newestThread->title }}</a>
                        {{ $category->newestThread->created_at->diffForHumans() }}
                    </div>
                @endif
                @if ($category->mostRecentlyActiveThread)
                    <div>
                        <a href="{{ Forum::route('thread.show', $category->mostRecentlyActiveThread->lastPost) }}">Re: {{ $category->mostRecentlyActiveThread->title }}</a>
                        {{ $category->mostRecentlyActiveThread->lastPost->created_at->diffForHumans() }}
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
                            @if ($subcategory->newestThread)
                                <div>
                                    <a href="{{ Forum::route('thread.show', $subcategory->newestThread) }}">{{ $subcategory->newestThread->title }}</a>
                                    {{ $subcategory->newestThread->created_at->diffForHumans() }}
                                </div>
                            @endif
                            @if ($subcategory->mostRecentlyActiveThread)
                                <div>
                                    <a href="{{ Forum::route('thread.show', $subcategory->mostRecentlyActiveThread->lastPost) }}">Re: {{ $subcategory->mostRecentlyActiveThread->title }}</a>
                                    {{ $subcategory->mostRecentlyActiveThread->lastPost->created_at->diffForHumans() }}
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

.category > .list-group-item {
    z-index: 1000;
}

.category .subcategories .list-group-item:first-child {
    border-radius: 0;
}
</style>
