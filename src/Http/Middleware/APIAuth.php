<?php namespace Riari\Forum\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class APIAuth
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
        $tokenHeader = 'Token token="' . config('forum.api.token') . '"';
        if (auth()->check() || $request->header('Authorization') == $tokenHeader) {
            // User is authenticated or a valid API token was provided; continue the request
            return $next($request);
        } else {
            // No authentication/authorization

            if ($request->ajax()) {
                // For AJAX requests, just return the appropriate response
                return response()->json(['error' => "User must be authenticated to access this resource."], 401);
            }

            // For all other request types, attempt HTTP basic authentication
            return auth()->onceBasic() ?: $next($request);
        }
    }
}
