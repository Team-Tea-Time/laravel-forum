<?php namespace Riari\Forum\Events;

use App;

class BaseEvent
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->session = App::make('Illuminate\Session\Store');
    }
}
