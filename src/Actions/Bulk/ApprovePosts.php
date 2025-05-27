<?php

namespace TeamTeaTime\Forum\Actions\Bulk;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\{
    Actions\BaseAction,
    Models\Post,
};

class ApprovePosts extends BaseAction
{
    private array $postIds;

    public function __construct(array $postIds)
    {
        $this->postIds = $postIds;
    }

    protected function transact()
    {
        $posts = Post::whereIn('id', $this->postIds)
            ->notDeleted()
            ->notFirstInThread()
            ->pendingApproval()
            ->get();

        if ($posts->count() == 0) {
            return null;
        }

        // We only want to execute the action on the valid subset of the selection
        $eligiblePostIds = $posts->pluck('id');

        // Use the raw query builder to prevent touching updated_at
        $query = DB::table(Post::getTableName())->whereIn('id', $eligiblePostIds);
        $rowsAffected = $query->whereNull('approved_at')
            ->orWhere('approved_at', '>', Carbon::now()->toDateTimeString())
            ->update(['approved_at' => DB::raw('now()')]);

        if ($rowsAffected == 0) {
            return null;
        }

        return $posts;
    }
}
