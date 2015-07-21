<?php namespace Riari\Forum\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
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
     * @var Posts
     */
    protected $posts;

    /**
     * @var Threads
     */
    protected $threads;

    /**
     * Create a new forum controller instance.
     *
     * @param  Categories  $categories
     * @param  Posts  $posts
     * @param  Threads  $threads
     */
    public function __construct(Categories $categories, Posts $posts, Threads $threads)
    {
        $this->categories = $categories;
        $this->posts = $posts;
        $this->threads = $threads;
    }
}
