<?php namespace Riari\Forum\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Riari\Forum\Libraries\Alerts;
use Riari\Forum\Libraries\Utils;

abstract class BaseController extends Controller
{
	use DispatchesCommands, ValidatesRequests;

	/**
	 * @var Alerts
	 */
	protected $alerts;

	/**
	 * @var Utils
	 */
	protected $utils;

	/**
	 * Create a controller instance.
	 */
	public function __construct()
	{
		$this->alerts = new Alerts;
		$this->utils = new Utils;
	}
}
