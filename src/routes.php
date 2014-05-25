<?php
if (!isset($routebase) || !isset($controller)) {
	throw new Exception ('This file can\'t be included outside of ForumServiceProvider@boot!');
}

/*
 *  Defines routes used by Forum controller
 */
\Route::get($routebase, $controller.'@getIndex');
\Route::get($routebase.'{categoryId}-{categoryUrl}', $controller.'@getCategory');
\Route::get($routebase.'{categoryId}-{categoryUrl}/{topicId}-{topicUrl}', $controller.'@getTopic');
