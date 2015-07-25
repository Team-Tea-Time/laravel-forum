<?php namespace Riari\Forum\Events;

use Illuminate\Database\Eloquent\Collection;

class UserViewingNew
{
    /**
     * @var Collection
     */
    public $threads;

    /**
     * Create a new event instance.
     *
     * @param  Collection  $threads
     */
    public function __construct(Collection $threads)
    {
        $this->threads = $threads;
    }
}
