<?php

namespace TeamTeaTime\Forum\Support\Frontend;

use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Post;
use TeamTeaTime\Forum\Models\Thread;
use Session;

class Forum
{
    public static function alert(string $type, string $transKey, int $transCount = 1, array $transParameters = []): void
    {
        $alerts = [];
        if (Session::has('alerts')) {
            $alerts = Session::get('alerts');
        }

        $message = trans_choice("forum::{$transKey}", $transCount, $transParameters);

        array_push($alerts, compact('type', 'message'));

        Session::flash('alerts', $alerts);
    }

    public static function render(string $content): string
    {
        return nl2br(e($content));
    }

    public static function route(string $route, $model = null): string
    {
        if (! Str::startsWith($route, config('forum.frontend.router.as'))) {
            $route = config('forum.frontend.router.as') . $route;
        }

        $params = [];
        $append = '';

        if ($model) {
            switch (true) {
                case $model instanceof Category:
                    $params = [
                        'category' => $model->id,
                        'category_slug' => static::slugify($model->title),
                    ];
                    break;
                case $model instanceof Thread:
                    $params = [
                        'thread' => $model->id,
                        'thread_slug' => static::slugify($model->title),
                    ];
                    break;
                case $model instanceof Post:
                    $params = [
                        'thread' => $model->thread->id,
                        'thread_slug' => static::slugify($model->thread->title),
                    ];

                    $test = $model->getPerPage();

                    if ($route == config('forum.frontend.router.as') . 'thread.show') {
                        // The requested route is for a thread; we need to specify the page number and append a hash for
                        // the post
                        $params['page'] = ceil($model->sequence / $model->getPerPage());
                        $append = "#post-{$model->sequence}";
                    } else {
                        // Other post routes require the post parameter
                        $params['post'] = $model->id;
                    }
                    break;
            }
        }

        return route($route, $params) . $append;
    }

    public static function slugify(string $string): string
    {
        return Str::slug($string, '-');
    }
}
