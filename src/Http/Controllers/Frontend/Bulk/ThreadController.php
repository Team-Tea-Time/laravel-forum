<?php

namespace TeamTeaTime\Forum\Http\Controllers\Frontend\Bulk;

use Illuminate\Http\RedirectResponse;
use TeamTeaTime\Forum\Http\Controllers\Frontend\BaseController;
use TeamTeaTime\Forum\Http\Requests\Bulk\DestroyThreads;
use TeamTeaTime\Forum\Http\Requests\Bulk\MoveThreads;
use TeamTeaTime\Forum\Http\Requests\Bulk\LockThreads;
use TeamTeaTime\Forum\Http\Requests\Bulk\PinThreads;
use TeamTeaTime\Forum\Http\Requests\Bulk\UnlockThreads;
use TeamTeaTime\Forum\Http\Requests\Bulk\UnpinThreads;
use TeamTeaTime\Forum\Http\Requests\Bulk\RenameThreads;
use TeamTeaTime\Forum\Http\Requests\Bulk\RestoreThreads;
use TeamTeaTime\Forum\Support\Frontend\Forum;

class ThreadController extends BaseController
{
    public function rename(RenameThreads $request): RedirectResponse
    {
        $count = $request->fulfill();

        return $this->bulkActionResponse($count, 'threads.updated');
    }

    public function move(MoveThreads $request): RedirectResponse
    {
        $count = $request->fulfill();

        return $this->bulkActionResponse($count, 'threads.updated');
    }

    public function lock(LockThreads $request): RedirectResponse
    {
        $count = $request->fulfill();

        return $this->bulkActionResponse($count, 'threads.updated');
    }

    public function unlock(UnlockThreads $request): RedirectResponse
    {
        $count = $request->fulfill();

        return $this->bulkActionResponse($count, 'threads.updated');
    }

    public function pin(PinThreads $request): RedirectResponse
    {
        $count = $request->fulfill();

        return $this->bulkActionResponse($count, 'threads.updated');
    }

    public function unpin(UnpinThreads $request): RedirectResponse
    {
        $count = $request->fulfill();

        return $this->bulkActionResponse($count, 'threads.updated');
    }

    public function destroy(DestroyThreads $request): RedirectResponse
    {
        $count = $request->fulfill();

        return $this->bulkActionResponse($count, 'threads.updated');
    }

    public function restore(RestoreThreads $request): RedirectResponse
    {
        $count = $request->fulfill();

        return $this->bulkActionResponse($count, 'threads.updated');
    }
}
