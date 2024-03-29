<?php

namespace TeamTeaTime\Forum\Http\Livewire\Forms;

use Illuminate\Http\Request;
use Livewire\Form;
use TeamTeaTime\Forum\{
    Actions\DeleteThread,
    Actions\LockThread,
    Actions\MoveThread,
    Actions\PinThread,
    Actions\RenameThread,
    Actions\RestoreThread,
    Actions\UnlockThread,
    Actions\UnpinThread,
    Events\UserDeletedThread,
    Events\UserLockedThread,
    Events\UserMovedThread,
    Events\UserPinnedThread,
    Events\UserRenamedThread,
    Events\UserRestoredThread,
    Events\UserUnlockedThread,
    Events\UserUnpinnedThread,
    Models\Category,
    Models\Thread,
    Support\Authorization\CategoryAuthorization,
    Support\Authorization\ThreadAuthorization,
    Support\Validation\ThreadRules,
    Support\Traits\HandlesDeletion,
};

class ThreadEditForm extends Form
{
    use HandlesDeletion;

    public string $title;

    public function delete(Request $request, Thread $thread, bool $permadelete): Thread
    {
        if (!ThreadAuthorization::delete($request->user(), $thread)) {
            abort(403);
        }

        $action = new DeleteThread($thread, $this->shouldPermaDelete($permadelete));
        $thread = $action->execute();

        UserDeletedThread::dispatch($request->user(), $thread);

        return $thread;
    }

    public function restore(Request $request, Thread $thread): Thread
    {
        if (!ThreadAuthorization::restore($request->user(), $thread)) {
            abort(403);
        }

        $action = new RestoreThread($thread);
        $thread = $action->execute();

        UserRestoredThread::dispatch($request->user(), $thread);

        return $thread;
    }

    public function lock(Request $request, Thread $thread): Thread
    {
        if (!CategoryAuthorization::lockThreads($request->user(), $thread->category)) {
            abort(403);
        }

        $action = new LockThread($thread);
        $thread = $action->execute();

        UserLockedThread::dispatch($request->user(), $thread);

        return $thread;
    }

    public function unlock(Request $request, Thread $thread): Thread
    {
        if (!CategoryAuthorization::lockThreads($request->user(), $thread->category)) {
            abort(403);
        }

        $action = new UnlockThread($thread);
        $thread = $action->execute();

        UserUnlockedThread::dispatch($request->user(), $thread);

        return $thread;
    }

    public function pin(Request $request, Thread $thread): Thread
    {
        if (!CategoryAuthorization::pinThreads($request->user(), $thread->category)) {
            abort(403);
        }

        $action = new PinThread($thread);
        $thread = $action->execute();

        UserPinnedThread::dispatch($request->user(), $thread);

        return $thread;
    }

    public function unpin(Request $request, Thread $thread): Thread
    {
        if (!CategoryAuthorization::pinThreads($request->user(), $thread->category)) {
            abort(403);
        }

        $action = new UnpinThread($thread);
        $thread = $action->execute();

        UserUnpinnedThread::dispatch($request->user(), $thread);

        return $thread;
    }

    public function rename(Request $request, Thread $thread): Thread
    {
        if (!ThreadAuthorization::rename($request->user(), $thread)) {
            abort(403);
        }

        $validated = $this->validate(ThreadRules::rename());

        $action = new RenameThread($thread, $validated['title']);
        $thread = $action->execute();

        UserRenamedThread::dispatch($request->user(), $thread);

        return $thread;
    }

    public function move(Request $request, Thread $thread, Category $destination): Thread
    {
        if (!CategoryAuthorization::moveThread($request->user(), $thread->category, $destination)) {
            abort(403);
        }

        $action = new MoveThread($thread, $destination);
        $thread = $action->execute();

        UserMovedThread::dispatch($request->user(), $thread, $destination);

        return $thread;
    }
}
