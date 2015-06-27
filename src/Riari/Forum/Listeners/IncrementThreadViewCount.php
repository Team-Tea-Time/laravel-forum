<?php namespace Riari\Forum\Handlers\Events;

use Illuminate\Session\Store;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Riari\Forum\Events\ThreadWasViewed;
use Riari\Forum\Libraries\Utils;
use Riari\Forum\Models\Thread;

class IncrementThreadViewCount implements ShouldBeQueued {

    private $session;

    /**
     * Create a new event handler instance.
     *
     * @param  Store  $session
     * @return void
     */
    public function __construct(Store $session)
    {
        $this->session = $session;
    }

    /**
     * Handle the passed event.
     *
     * @param  ThreadWasViewed  $event
     * @return void
     */
    public function handle(ThreadWasViewed $event)
    {
        $this->throttle();

        if (!$this->isThreadViewed($event->thread))
        {
            $event->thread->timestamps = false;
            $event->thread->increment('view_count');

            $this->markThreadViewed($event->thread);
        }

        $user = Utils::getCurrentUser();

        if (!is_null($user))
        {
            $event->thread->markAsRead($user->id);
        }
    }

    /**
     * Keep the list of threads viewed by the user up to date based on the
     * forum.preferences.thread.throttle_view_count_interval config option.
     *
     * @return boolean
     */
    public function throttle()
    {
        $threads = $this->getViewedThreads();

        if (!is_null($threads))
        {
            $threads = $this->cleanExpiredViews($threads);
            $this->storeViewedThreads($threads);
        }
    }

    /**
     * Determine if a thread has been viewed by the user.
     *
     * @param  Thread  $thread
     * @return boolean
     */
    private function isThreadViewed(Thread $thread)
    {
        $viewed = $this->session->get('viewed_threads', []);
        return array_key_exists($thread->id, $viewed);
    }

    /**
     * Mark a thread as viewed by the user.
     *
     * @param  Thread  $thread
     * @return void
     */
    private function markThreadViewed(Thread $thread)
    {
        $key = "viewed_threads.{$thread->id}";
        $this->session->put($key, time());
    }

    /**
     * Get the user's viewed threads.
     *
     * @return mixed
     */
    private function getViewedThreads()
    {
        return $this->session->get('viewed_threads', null);
    }

    /**
     * Clear viewed threads older than the throttle interval from the user's
     * session.
     *
     * @param  Array  $threads
     * @return Array
     */
    private function cleanExpiredViews(Array $threads)
    {
        $time = time();
        $throttleTime = config('forum.preferences.thread.throttle_view_count_interval');

        return array_filter($threads, function ($timestamp) use ($time, $throttleTime)
        {
            return ($timestamp + $throttleTime) > $time;
        });
    }

    /**
     * Store threads viewed by the user.
     *
     * @param  Array  $threads
     * @return void
     */
    private function storeViewedThreads(Array $threads)
    {
        $this->session->put('viewed_threads', $threads);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen(
            'Riari\Forum\Events\ThreadWasViewed',
            'Riari\Forum\Handlers\Events\IncrementThreadViewCount@handle'
        );
    }

}
