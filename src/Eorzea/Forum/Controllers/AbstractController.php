<?php namespace Eorzea\Forum\Controllers;

use Eorzea\Forum\Models\Category;
use Eorzea\Forum\Models\Thread;
use Eorzea\Forum\Models\Post;
use Eorzea\Forum\Repositories\Categories;
use Eorzea\Forum\Repositories\Threads;
use Eorzea\Forum\Repositories\Posts;
use Eorzea\Forum\AccessControl;

use stdClass;
use App;
use Config;
use Input;
use Redirect;
use View;
use Validator;

abstract class AbstractController extends AbstractBaseController {

  private $categories;
  private $threads;
  private $posts;

  protected $threadRules = array(
    'title' => 'required',
  );

  protected $postRules = array(
    'content' => 'required|min:5',
  );

  public function __construct(Categories $categories, Threads $threads, Posts $posts)
  {
    $this->categories = $categories;
    $this->threads = $threads;
    $this->posts = $posts;
  }

  public function getIndex()
  {
    $categories = $this->categories->getByParent(null, array('subcategories'));

    return View::make('forum::index', compact('categories'));
  }

  public function getCategory($categoryID, $categoryAlias)
  {
    if (!AccessControl::check($this, 'access_forum'))
    {
      return App::abort(403, 'Access denied');
    }

    $category = $this->categories->getByID($categoryID, array('parentCategory', 'subCategories', 'threads'));
    if ($category == NULL)
    {
      return App::abort(404);
    }

    $parentCategory = $category->parentCategory;
    $subCategories  = $category->subCategories;
    $threads = $category->threads;

    return View::make('forum::category', compact('parentCategory', 'category', 'subCategories', 'threads'));

  }

  public function getThread($categoryID, $categoryAlias, $threadID, $threadAlias, $page = 0)
  {
    if (!AccessControl::check($this, 'access_forum'))
    {
      return App::abort(403, 'Access denied');
    }

    $category = $this->categories->getByID($categoryID, array('parentCategory'));
    if ($category == NULL)
    {
      return App::abort(404);
    }

    $thread = $this->threads->getByID($threadID);
    if ($thread == NULL)
    {
      return App::abort(404);
    }

    $parentCategory  = $category->parentCategory;
    $postsPerPage = Config::get('forum::integration.posts_per_page');
    //$this->posts->paginate($postsPerPage);
    $posts = $this->posts->getByThread($thread->id, array('author'));
    $paginationLinks = $this->posts->getPaginationLinks($postsPerPage);

    return View::make('forum::thread', compact('parentCategory', 'category', 'thread', 'posts', 'paginationLinks'));
  }

  public function getCreateThread($categoryID, $categoryAlias)
  {
    if (!AccessControl::check($this, 'create_threads'))
    {
      return App::abort(403, 'Access denied');
    }

    $category       = $this->categories->getByID($categoryID, array('parentCategory'));
    $parentCategory = $category->parentCategory;
    $actionAlias    = $category->postAlias;

    return View::make('forum::thread-create', compact('parentCategory', 'category', 'actionAlias'));
  }

  public function postCreateThread($categoryID, $categoryAlias)
  {
    if (!AccessControl::check($this, 'create_threads'))
    {
      return App::abort(403, 'Access denied');
    }

    $user = $this->getCurrentUser();
    $category  = $this->categories->getByID($categoryID);
    $validator = Validator::make(Input::all(), array_merge($this->threadRules, $this->postRules));
    if ($validator->passes())
    {
      $thread = array(
        'author_id'       => $user->id,
        'parent_category' => $categoryID,
        'title'           => Input::get('title')
      );

      $thread = $this->threads->create($thread);

      $post = array(
        'parent_thread'   => $thread->id,
        'author_id'       => $user->id,
        'content'         => Input::get('content')
      );

      $post = $this->posts->create($post);

      return Redirect::to($thread->URL)->with('success', 'thread created');
    }
    else
    {
      return Redirect::to($category->postAlias)->withErrors($validator)->withInput();
    }
  }

  public function getDeleteThread($threadID)
  {
    if (!AccessControl::check($this, 'delete_threads'))
    {
      return App::abort(403, 'Access denied');
    }
  }

  public function postDeleteThread($threadID)
  {

  }

  public function getCreatePost($categoryID, $categoryAlias, $threadID, $threadAlias)
  {
    if (!AccessControl::check($this, 'create_posts'))
    {
      return App::abort(403, 'Access denied');
    }

    $category = $this->categories->getByID($categoryID, array('parentCategory'));
    $thread = $this->threads->getByID($threadID);
    if ($category == NULL || $thread == NULL)
    {
      return App::abort(404);
    }

    $parentCategory = $category->parentCategory;
    $actionAlias = $thread->postAlias;
    $prevPosts = $this->posts->getLastByThread($threadID);

    return View::make('forum::thread-reply', compact('parentCategory', 'category', 'thread', 'actionAlias', 'prevPosts'));
  }

  public function postCreatePost($categoryID, $categoryAlias, $threadID, $threadAlias)
  {
    $user = $this->getCurrentUser();
    $category = $this->categories->getByID($categoryID);
    $thread = $this->threads->getByID($threadID);

    if (!AccessControl::check($category, 'create_posts'))
    {
      return App::abort(403, 'Access denied');
    }

    $validator = Validator::make(Input::all(), $this->postRules);
    if ($validator->passes())
    {
      $post = array(
        'parent_thread' => $threadID,
        'author_id'     => $user->id,
        'content'       => Input::get('content')
      );

      $post = $this->posts->create($post);

      return Redirect::to($thread->URL)->with('success', 'thread created');
    }
    else
    {
      return Redirect::to($thread->postAlias)->withErrors($validator)->withInput();
    }
  }

  public function getEditPost($categoryID, $categoryAlias, $threadID, $threadAlias, $postID)
  {
    $category = $this->categories->getByID($categoryID, array('parentCategory'));
    $thread = $this->threads->getByID($threadID);
    $post = $this->posts->getByID($postID);

    if ($category == NULL || $thread == NULL || $post == NULL)
    {
      return App::abort(404);
    }

    if (!AccessControl::check($post, 'edit_post'))
    {
      return App::abort(403, 'Access denied');
    }

    $parentCategory = $category->parentCategory;
    $actionAlias = $post->postAlias;

    return View::make('forum::post-edit', compact('parentCategory', 'category', 'thread', 'post', 'actionAlias'));
  }

  public function postEditPost($categoryID, $categoryAlias, $threadID, $threadAlias, $postID)
  {
    $user = $this->getCurrentUser();
    $category = $this->categories->getByID($categoryID, array('parentCategory'));
    $thread = $this->threads->getByID($threadID);
    $post = $this->posts->getByID($postID);

    if ($category == NULL || $thread == NULL || $post == NULL)
    {
      return App::abort(404);
    }

    if (!AccessControl::check($post, 'edit_post'))
    {
      return App::abort(403, 'Access denied');
    }

    $validator = Validator::make(Input::all(), $this->postRules);
    if ($validator->passes())
    {
      $post = array(
        'id'            => $postID,
        'parent_thread' => $threadID,
        'author_id'     => $user->id,
        'content'       => Input::get('content')
      );

      $post = $this->posts->update($post);

      return Redirect::to($post->URL)->with('success', 'thread created');
    }
    else
    {
      return Redirect::to($post->postAlias)->withErrors($validator)->withInput();
    }
  }

}
