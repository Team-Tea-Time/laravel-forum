<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Controllers
    |--------------------------------------------------------------------------
    |
    | Here we specify the namespace and controllers to use. Change these if
    | you want to extend the provided classes and use your own instead.
    |
    */

    'controllers' => [
        'namespace' => 'Riari\Forum\Http\Controllers',
        'category'  => 'CategoryController',
        'thread'    => 'ThreadController',
        'post'      => 'PostController'
    ],

    /*
    |--------------------------------------------------------------------------
    | Policies
    |--------------------------------------------------------------------------
    |
    | Here we specify the policy classes to use. Change these if you want to
    | extend the provided classes and use your own instead.
    |
    */

    'policies' => [
        'forum' => Riari\Forum\Policies\ForumPolicy::class,
        'model' => [
            Riari\Forum\Models\Category::class  => Riari\Forum\Policies\CategoryPolicy::class,
            Riari\Forum\Models\Thread::class    => Riari\Forum\Policies\ThreadPolicy::class,
            Riari\Forum\Models\Post::class      => Riari\Forum\Policies\PostPolicy::class
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Application user model
    |--------------------------------------------------------------------------
    |
    | Your application's user model.
    |
    */

    'user_model' => App\User::class,

    /*
    |--------------------------------------------------------------------------
    | Application user name
    |--------------------------------------------------------------------------
    |
    | The attribute to use for the username.
    |
    */

    'user_name' => 'name',

    /*
    |--------------------------------------------------------------------------
    | Closure: process alert messages
    |--------------------------------------------------------------------------
    |
    | Change this if your app has its own user alert/notification system.
    | NOTE: Remember to override the forum views to remove the default alerts
    | if you no longer use them.
    |
    */

    /**
     * @param  string  $type    The type of alert ('success' or 'danger')
     * @param  string  $message The alert message
     */
    'process_alert' => function ($type, $message)
    {
        $alerts = [];
        if (Session::has('alerts')) {
            $alerts = Session::get('alerts');
        }

        array_push($alerts, compact('type', 'message'));

        Session::flash('alerts', $alerts);
    },

];
