<?php

namespace Riari\Forum\Models\Observers;

use Carbon\Carbon;

abstract class BaseObserver
{
    /**
     * @var Carbon
     */
    protected $carbon;

    /**
     * Create a new model observer instance.
     */
    public function __construct()
    {
        $this->carbon = new Carbon;
    }
}
