<?php

namespace TeamTeaTime\Forum\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use TeamTeaTime\Forum\Http\Resources\ThreadResource;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;

class ThreadController
{
    public function recent(Request $request, bool $unreadOnly = false): AnonymousResourceCollection
    {
        $threads = Thread::recent()
            ->get()
            ->filter(function ($thread) use ($request, $unreadOnly)
            {
                return (! $unreadOnly || $thread->userReadStatus !== null)
                    && (! $thread->category->is_private || $request->user() && $request->user()->can('view', $thread));
            });

        return ThreadResource::collection($threads);
    }

    public function unread(Request $request): AnonymousResourceCollection
    {
        return $this->recent($request, true);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $categories = Category::all()
            ->filter(fn($category) => ! $category->is_private || $request->user() && $request->user()->can('view', $category))
            ->keyBy('id');
        
        $categoryIds = $categories->pluck('id');

        $query = Thread::orderBy('created_at');

        $categoryId = $request->query('category_id');
        $createdAfter = $request->query('created_after');
        $createdBefore = $request->query('created_before');
        $updatedAfter = $request->query('updated_after');
        $updatedBefore = $request->query('updated_before');

        if ($categoryId !== null && $categoryIds->contains((int)$categoryId))
        {
            $query = $query->where('category_id', $categoryId);
        }
        else
        {
            $query = $query->whereIn('category_id', $categoryIds);
        }

        if ($createdAfter !== null) $query = $query->where('created_at', '>', Carbon::parse($createdAfter)->toDateString());
        if ($createdBefore !== null) $query = $query->where('created_at', '<', Carbon::parse($createdBefore)->toDateString());
        if ($updatedAfter !== null) $query = $query->where('updated_at', '>', Carbon::parse($updatedAfter)->toDateString());
        if ($updatedBefore !== null) $query = $query->where('updated_at', '<', Carbon::parse($updatedBefore)->toDateString());

        $threads = $query->paginate();

        $threads->setCollection($threads->getCollection()->filter(function ($thread) use ($request, $categories)
        {
            $category = $categories->get($thread->category_id);
            return ! $category->is_private || $request->user() && $request->user()->can('view', $thread);
        }));

        return ThreadResource::collection($threads);
    }
}