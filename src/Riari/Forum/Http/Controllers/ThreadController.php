<?php namespace Riari\Forum\Http\Controllers;

use Illuminate\Routing\Controller;
use Riari\Forum\Models\Category;
use Riari\Forum\Models\Thread;

class ThreadController extends BaseController
{
    public function show(Category $category, $categoryAlias, Thread $thread)
    {
        return view('forum::thread.show', compact('thread'));
    }
}
