<?php namespace Riari\Forum\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Riari\Forum\Repositories\Categories;
use Riari\Forum\Repositories\Posts;
use Riari\Forum\Repositories\Threads;

abstract class BaseController extends Controller
{
	use DispatchesCommands, ValidatesRequests;

    /**
     * @var Categories
     */
    protected $categories;

    /**
     * @var Threads
     */
    protected $threads;

    /**
     * @var Posts
     */
    protected $posts;

    /**
     * Create a new forum controller instance.
     *
     * @param  Categories  $categories
     * @param  Threads  $threads
     * @param  Posts  $posts
     */
    public function __construct(Categories $categories, Threads $threads, Posts $posts)
    {
        $this->categories = $categories;
        $this->threads = $threads;
        $this->posts = $posts;
    }
}
