<?php namespace Riari\Forum\Models\Observers;

class BaseObserver
{
    /**
     * @var boolean
     */
    protected $softDeletes;

    /**
     * Create a new model observer instance.
     */
    public function __construct()
    {
        $this->softDeletes = config('forum.preferences.misc.soft_delete');
    }
}
