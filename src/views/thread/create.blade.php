@extends ('forum::master', ['breadcrumb_other' => trans('forum::threads.new_thread')])

@section ('content')
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
