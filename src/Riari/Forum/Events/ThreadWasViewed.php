<?php namespace Riari\Forum\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Session\Store;
use Riari\Forum\Models\Thread;

class ThreadWasViewed extends BaseEvent {

    use SerializesModels;

    public $thread;

    /**
     * Create a new event instance.
     *
     * @param  Thread  $thread
     * @return void
     */
    public function __construct(Thread $thread)
    {
        $this->thread = $thread;
    }

}
