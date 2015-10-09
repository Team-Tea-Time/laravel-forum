<?php

namespace Riari\Forum\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Riari\Forum\Routing\Dispatcher;

abstract class BaseController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @var array
     */
    protected $rules;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * Create a frontend controller instance.
     *
     * @param  Dispatcher  $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
}
