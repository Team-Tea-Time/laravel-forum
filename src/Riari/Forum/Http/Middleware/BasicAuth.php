<?php namespace Riari\Forum\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BasicAuth
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
		if (auth()->check()) {
			return $next($request);
		}

		return auth()->onceBasic() ?: $next($request);
	}
}
