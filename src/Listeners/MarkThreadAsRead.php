<?php

namespace Riari\Forum\Listeners;

use Riari\Forum\Events\UserViewingThread;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MarkThreadAsRead
{
    /**
     * @var Guard
     */
    private $auth;

    /**
     * Create the event listener.
     *
     * @param  Guard  $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle the event.
     *
     * @param  PodcastWasPurchased  $event
     * @return void
     */
    public function handle(UserViewingThread $event)
    {
        $primaryKey = $this->auth->user()->getKeyName();
        $event->thread->markAsRead($this->auth->user()->{$primaryKey});
    }
}
