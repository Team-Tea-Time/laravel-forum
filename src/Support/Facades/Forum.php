<?php

namespace Riari\Forum\Support\Facades;

use Illuminate\Support\Facades\Facade;

class Forum extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'forum';
    }
}
