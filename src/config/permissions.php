<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ACCESS: Category permissions
    |--------------------------------------------------------------------------
    |
    | Determines whether or not the current user is allowed to access a given
    | category. All categories are open by default.
    |
    */

    'access_category' => function($category, $user)
    {
        return true;
    },

    /*
    |--------------------------------------------------------------------------
    | ACTION: New thread permission
    |--------------------------------------------------------------------------
    |
    | Determines whether or not the current user is allowed to post new
    | threads.
    |
    */

    'create_threads' => function($category, $user)
    {
        if ($user == null)
        {
            return false;
        }

        return true;
    },

    /*
    |--------------------------------------------------------------------------
    | ACTION: Lock thread permission
    |--------------------------------------------------------------------------
    |
    | Determines whether or not the current user is allowed to lock threads.
    |
    */

    'lock_threads' => function($thread, $user)
    {
        return false;
    },

    /*
    |--------------------------------------------------------------------------
    | ACTION: Pin thread permission
    |--------------------------------------------------------------------------
    |
    | Determines whether or not the current user is allowed to pin threads.
    |
    */

    'pin_threads' => function($thread, $user)
    {
        return false;
    },

    /*
    |--------------------------------------------------------------------------
    | ACTION: Delete thread permission
    |--------------------------------------------------------------------------
    |
    | Determines whether or not the current user is allowed to delete threads.
    |
    */
    
    'delete_threads' => function($thread, $user)
    {
        return false;
    },

    /*
    |--------------------------------------------------------------------------
    | ACTION: Reply to thread permission
    |--------------------------------------------------------------------------
    |
    | Determines whether or not the current user is allowed to post thread
    | replies.
    |
    */

    'reply_to_thread' => function($thread, $user)
    {
        if ($user == null || $thread->locked)
        {
            return false;
        }

        return true;
    },

    /*
    |--------------------------------------------------------------------------
    | ACTION: Edit post permission
    |--------------------------------------------------------------------------
    |
    | Determines whether or not the current user is allowed to edit a given
    | post.
    |
    */

    'edit_post' => function($post, $user)
    {
        if ($user == null || ($user->id != $post->author_id))
        {
            return false;
        }

        return true;
    },

    /*
    |--------------------------------------------------------------------------
    | ACTION: Delete post permission
    |--------------------------------------------------------------------------
    |
    | Determines whether or not the current user is allowed to delete posts.
    |
    */

    'delete_posts' => function($post, $user)
    {
        return false;
    }

];
