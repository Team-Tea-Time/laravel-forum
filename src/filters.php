<?php

Route::filter('inline_csrf', function($route, $request)
{
  if (Session::token() != $route->getParameter('token'))
  {
    throw new Illuminate\Session\TokenMismatchException;
  }
});
