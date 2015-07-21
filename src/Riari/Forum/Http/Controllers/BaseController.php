<?php namespace Riari\Forum\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class BaseController extends Controller
{
	use DispatchesCommands, ValidatesRequests;
}
