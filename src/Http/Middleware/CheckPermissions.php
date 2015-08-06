<?php

namespace Riari\Forum\Http\Middleware;

use Closure;
use Forum;
use ForumRoute;
use Illuminate\Http\Request;
use Riari\Forum\Http\Config\API\Error;

class CheckPermissions
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  Request  $request
	 * @param  Closure  $next
	 * @return mixed
	 */
	public function handle(Request $request, Closure $next)
	{
		$route = $request->route();

		if (!Forum::userCan(
			$route->getName(),
			$request->all() + $route->parameters()
		)) {
			if (ForumRoute::isAPI()) {
				return response()->json(['error' => "Authenticated user does not have permission to access this resource."], 403);
			}

			abort(403);
		}

		// Permission is granted; continue
		return $next($request);
	}
}
