{{--
	$category is passed as NULL to the master layout view to prevent it from
	showing in the breadcrumbs
--}}
@extends ('forum::master', ['category' => null])

@section ('content')
    <h2>{{ trans('forum::general.index') }}</h2>

    @foreach ($categories as $category)
        <table class="table table-index">
            <thead>
                <tr>
					<th>{{ trans_choice('forum::categories.category', 1) }}</th>
					<th class="col-md-2">{{ trans_choice('forum::threads.thread', 2) }}</th>
					<th class="col-md-2">{{ trans_choice('forum::posts.post', 2) }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <p class="lead"><a href="{{ $category->route }}">{{ $category->title }}</a></p>
                        {{ $category->subtitle }}

                        @if ($category->newestThread)
        				<div class="text-muted">
                            <br>
                            {{ trans('forum::threads.newest') }}:
                            <a href="{{ $category->newestThread->route }}">
                                {{ $category->newestThread->title }}
                                ({{ $category->newestThread->authorName }})</a>
                            <br>
                            {{ trans('forum::posts.last') }}:
                            <a href="{{ $category->latestActiveThread->lastPost->url }}">
                                {{ $category->latestActiveThread->title }}
                                ({{ $category->latestActiveThread->lastPost->authorName }})
                            </a>
        				</div>
                        @endif
                    </td>
                    <td>{{ $category->threadCount }}</td>
                    <td>{{ $category->postCount }}</td>
                </tr>
                @if (!$category->children->isEmpty())
                    <tr>
                        <th colspan="3">{{ trans('forum::categories.subcategories') }}</th>
                    </tr>
                    @foreach ($category->children as $subcategory)
                        @include ('forum::category.partials.list')
                    @endforeach
                @else
                    <tr>
                        <th colspan="3">
                            {{ trans('forum::categories.none_found') }}
                        </th>
                    </tr>
                @endif
            </tbody>
        </table>
    @endforeach
@overwrite
