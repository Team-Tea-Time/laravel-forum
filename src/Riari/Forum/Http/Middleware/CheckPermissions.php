<?php namespace Riari\Forum\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Riari\Forum\Libraries\AccessControl;
use Riari\Forum\Libraries\Utils;

class CheckPermissions
{
	/**
	 * @var AccessControl
	 */
	protected $access;

	/**
	 * @var object
	 */
	protected $user;

	/**
	 * The parameters to pass to the permission checker.
	 *
	 * @var array
	 */
	protected $parameters = ['category', 'post', 'thread'];

	/**
	 * Create a new filter instance.
	 *
	 * @param  AccessControl  $access
	 * @param  Utils  $utils
	 * @return void
	 */
	public function __construct(AccessControl $access, Utils $utils)
	{
		$this->access = $access;
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
		$this->access->check(
			$request->route()->parameters(),
			$request->route()->getName(),
			$this->user
		);

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
		return;
	}
}
