<?php
if (!isset($routebase) || !isset($viewController) || !isset($postController)) {
	throw new Exception ('This file can\'t be included outside of ForumServiceProvider@boot!');
}

/*
 *  Defines routes used by Forum controller
 */
\Route:: get($routebase, $viewController.'@getIndex');
\Route:: get($routebase.'{categoryID}-{categoryURL}', $viewController.'@getCategory');
\Route:: get($routebase.'{categoryID}-{categoryURL}/{threadID}-{threadURL}', $viewController.'@getThread');

\Route:: get($routebase.'{categoryID}-{categoryURL}/new', $postController.'@getNewThread');
\Route::post($routebase.'{categoryID}-{categoryURL}/new', $postController.'@postNewThread');

\Route:: get($routebase.'{categoryID}-{categoryURL}/{threadID}-{threadURL}/new', $postController.'@getNewPost');
\Route::post($routebase.'{categoryID}-{categoryURL}/{threadID}-{threadURL}/new', $postController.'@postNewPost');

\Route:: get($routebase.'{categoryID}-{categoryURL}/{threadID}-{threadURL}/edit/{postID}', $postController.'@getEditPost');
\Route::post($routebase.'{categoryID}-{categoryURL}/{threadID}-{threadURL}/edit/{postID}', $postController.'@postEditPost');
