@extends ('forum::master')

@section ('content')
@include ('forum::partials.breadcrumbs', compact('category', 'thread'))

<h2>{{ trans('forum::threads.new_thread') }} ({{ $category->title }})</h2>

@include (
    'forum::post.partials.edit',
    [
        'form_url'          => $category->newThreadRoute,
        'method'            => 'POST',
        'show_title_field'  => true,
        'submit_label'      => trans('forum::general.add'),
    ]
)
@overwrite
