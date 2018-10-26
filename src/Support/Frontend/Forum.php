<?php

namespace Riari\Forum\Support\Frontend;

use Illuminate\Routing\Router;
use Riari\Forum\Models\Category;
use Riari\Forum\Models\Post;
use Riari\Forum\Models\Thread;
use Session;

class Forum
{
    /**
     * Process an alert message to display to the user.
     *
     * @param  string  $type
     * @param  string  $transKey
     * @param  string  $transCount
     * @return void
     */
    public static function alert($type, $transKey, $transCount = 1, $transParameters = [])
    {
        $alerts = [];
        if (Session::has('alerts')) {
            $alerts = Session::get('alerts');
        }

        $message = trans_choice("forum::{$transKey}", $transCount, $transParameters);

        array_push($alerts, compact('type', 'message'));

        Session::flash('alerts', $alerts);
    }

    /**
     * Render the given content.
     *
     * @param  string  $content
     * @return string
     */
    public static function render($content)
    {
        return nl2br(e($content));
    }

    /**
     * Generate a URL to a named forum route.
     *
     * @param  string  $route
     * @param  null|\Illuminate\Database\Eloquent\Model  $model
     * @return string
     */
    public static function route($route, $model = null)
    {
        if (!starts_with($route, config('forum.frontend.router.as'))) {
            $route = config('forum.frontend.router.as') . $route;
        }

        $params = [];
        $append = '';

        if ($model) {
            switch (true) {
                case $model instanceof Category:
                    $params = [
                        'category'      => $model->id,
                        'category_slug' => static::slugify($model->title)
                    ];
                    break;
                case $model instanceof Thread:
                    $params = [
                        'category'      => $model->category->id,
                        'category_slug' => static::slugify($model->category->title),
                        'thread'        => $model->id,
                        'thread_slug'   => static::slugify($model->title)
                    ];
                    break;
                case $model instanceof Post:
                    $params = [
                        'category'      => $model->thread->category->id,
                        'category_slug' => static::slugify($model->thread->category->title),
                        'thread'        => $model->thread->id,
                        'thread_slug'   => static::slugify($model->thread->title)
                    ];

                    if ($route == config('forum.routing.as') . 'thread.show') {
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

    /**
     * Register the standard forum routes.
     *
     * @param  Router  $router
     * @return void
     */
    public static function routes(Router $router)
    {
        $controllers = config('forum.frontend.controllers');

        // Forum index
        $router->get('/', ['as' => 'index', 'uses' => "{$controllers['category']}@index"]);

        // New/updated threads
        $router->get('new', ['as' => 'index-new', 'uses' => "{$controllers['thread']}@indexNew"]);
        $router->patch('new', ['as' => 'mark-new', 'uses' => "{$controllers['thread']}@markNew"]);

        // Categories
        $router->post('category/create', ['as' => 'category.store', 'uses' => "{$controllers['category']}@store"]);
        $router->group(['prefix' => '{category}-{category_slug}'], function ($router) use ($controllers) {
            $router->get('/', ['as' => 'category.show', 'uses' => "{$controllers['category']}@show"]);
            $router->patch('/', ['as' => 'category.update', 'uses' => "{$controllers['category']}@update"]);
            $router->delete('/', ['as' => 'category.delete', 'uses' => "{$controllers['category']}@destroy"]);

            // Threads
            $router->get('{thread}-{thread_slug}', ['as' => 'thread.show', 'uses' => "{$controllers['thread']}@show"]);
            $router->get('thread/create', ['as' => 'thread.create', 'uses' => "{$controllers['thread']}@create"]);
            $router->post('thread/create', ['as' => 'thread.store', 'uses' => "{$controllers['thread']}@store"]);
            $router->patch('{thread}-{thread_slug}', ['as' => 'thread.update', 'uses' => "{$controllers['thread']}@update"]);
            $router->delete('{thread}-{thread_slug}', ['as' => 'thread.delete', 'uses' => "{$controllers['thread']}@destroy"]);

            // Posts
            $router->get('{thread}-{thread_slug}/post/{post}', ['as' => 'post.show', 'uses' => "{$controllers['post']}@show"]);
            $router->get('{thread}-{thread_slug}/reply', ['as' => 'post.create', 'uses' => "{$controllers['post']}@create"]);
            $router->post('{thread}-{thread_slug}/reply', ['as' => 'post.store', 'uses' => "{$controllers['post']}@store"]);
            $router->get('{thread}-{thread_slug}/post/{post}/edit', ['as' => 'post.edit', 'uses' => "{$controllers['post']}@edit"]);
            $router->patch('{thread}-{thread_slug}/{post}', ['as' => 'post.update', 'uses' => "{$controllers['post']}@update"]);
            $router->delete('{thread}-{thread_slug}/{post}', ['as' => 'post.delete', 'uses' => "{$controllers['post']}@destroy"]);
        });

        // Bulk actions
        $router->group(['prefix' => 'bulk', 'as' => 'bulk.'], function ($router) use ($controllers) {
            $router->patch('thread', ['as' => 'thread.update', 'uses' => "{$controllers['thread']}@bulkUpdate"]);
            $router->delete('thread', ['as' => 'thread.delete', 'uses' => "{$controllers['thread']}@bulkDestroy"]);
            $router->patch('post', ['as' => 'post.update', 'uses' => "{$controllers['post']}@bulkUpdate"]);
            $router->delete('post', ['as' => 'post.delete', 'uses' => "{$controllers['post']}@bulkDestroy"]);
        });
    }

    /**
     * Convert the given string to a URL-friendly slug.
     *
     * @param  string  $string
     * @return string
     */
    public static function slugify($string)
    {
        return str_slug($string, '-');
    }
}
