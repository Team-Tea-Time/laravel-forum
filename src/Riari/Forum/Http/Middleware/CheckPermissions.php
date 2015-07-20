<?php namespace Riari\Forum\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Riari\Forum\Libraries\Utils;

class CheckPermissions
{
	/**
	 * @var object
	 */
	protected $user;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
	public function __construct(Utils $utils)
	{
		$this->user = $utils->getCurrentUser();
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  Request  $request
	 * @param  Closure  $next
	 * @return mixed
	 */
	public function handle(Request $request, Closure $next)
	{
		if ($this->permissionGranted($request))
		{

		}

		// Permission is granted; continue
		return $next($request);
	}

	/**
	 * Determine if permission is granted for the current user and route.
	 *
	 * @param  Request  $request
	 * @return boolean
	 */
	public function permissionGranted(Request $request)
	{
		dd($request->route());
		return
	}

	/**
	 * Determine if the current user has access to the specified route.
	 *
	 * @param  string  $route
	 * @param  object  $parameters
	 * @return boolean
	 */

        // Fetch the current user
        $user_callback = config('forum.integration.current_user');
        $user = $user_callback();

        // Check for access permission
        $access_callback = config('forum.permissions.access_category');
        $permission_granted = $access_callback($context, $user);

        if ($permission_granted && ($permission != 'access_category'))
        {
            // Check for action permission
            $action_callback = config('forum.permissions.' . $permission);
            $permission_granted = $action_callback($context, $user);
        }

        if (!$permission_granted && $abort)
        {
            $denied_callback = config('forum.integration.process_denied');
            $denied_callback($context, $user);
        }

        return $permission_granted;
}
