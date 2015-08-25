<?php

namespace Riari\Forum\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

abstract class BaseController extends Controller
{
    use ValidatesRequests;

    /**
     * @var array
     */
    protected $rules;
}
