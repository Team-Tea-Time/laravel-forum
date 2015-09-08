<?php

namespace Riari\Forum\Support\Facades;

use Illuminate\Support\Facades\Facade;

class ForumRoute extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'forumroute';
    }
}
