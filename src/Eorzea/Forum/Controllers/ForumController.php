<?php namespace Eorzea\Forum\Controllers;

use Eorzea\Forum\Repositories\CategoriesRepository;
use Eorzea\Forum\Repositories\ThreadsRepository;
use Eorzea\Forum\Repositories\PostsRepository;

abstract class AbstractForumCRUDController extends AbstractForumController {

  private $categories;
  private $threads;
  private $posts;

  protected $threadRules = array(
    'title' => 'required',
  );

  protected $postRules = array(
    'data' => 'required|min:5',
  );

  public function __construct(CategoriesRepository $categories, ThreadsRepository $threads, PostsRepository $posts)
  {
    $this->categories = $categories;
    $this->threads = $threads;
    $this->posts = $posts;
  }

  public function getIndex()
  {
    $categories = $this->categories->getByParent(null, array('subcategories'));

    $this->layout->content = View::make('forum::index', compact('categories'));
  }

  public function getCategory($categoryID, $categoryAlias)
  {
    $category = $this->categories->getByID($categoryID, array('parentCategory', 'subCategories', 'threads'));
    if ($category == NULL)
    {
      return App::abort(404);
    }

    $parentCategory = $category->parentCategory;
    $subCategories  = $category->subCategories;
    $threads = $category->threads;

    $this->layout->content = View::make('forum::category', compact('parentCategory', 'category', 'subCategories', 'threads'));

  }

  public function getThread($categoryID, $categoryAlias, $threadID, $threadAlias, $page = 0)
  {
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
    $postsPerPage = Config::get('forum::integration.postsperpage');
    //$this->posts->paginate($postsPerPage);
    $posts        = $this->posts->getByThread($thread->id, array('author'));
    $paginationLinks = $this->posts->getPaginationLinks($postsPerPage);

    $this->layout->content = View::make('forum::thread', compact('parentCategory', 'category', 'thread', 'posts', 'paginationLinks'));
  }

  public function getCreateThread($categoryID, $categoryAlias)
  {
    $user = $this->getCurrentUser();
    if ($user == NULL)
    {
      return App::abort(403, 'Access denied');
    }

    $category       = $this->categories->getByID($categoryID, array('parentCategory'));
    $parentCategory = $category->parentCategory;
    $actionAlias      = $category->postAlias;

    $this->layout->content = View::make('forum::post', compact('parentCategory', 'category', 'actionAlias'));
  }

  public function postCreateThread($categoryID, $categoryAlias)
  {
    if( !$this->userCan('create_threads') )
    {
      return App::abort(403, 'Access denied');
    }

    $category  = $this->categories->getByID($categoryID);
    $validator = Validator::make(Input::all(), array_merge($this->threadRules, $this->postRules));
    if ($validator->passes())
    {
      $title = Input::get('title');
      $data  = Input::get('data');

      $thread                  = new stdClass();
      $thread->author_id       = $user->id;
      $thread->parent_category = $category->id;
      $thread->title           = $title;

      $thread = $this->threads->create($thread);

      $post               = new stdClass();
      $post->parent_thread = $thread->id;
      $post->author_id    = $user->id;
      $post->data         = $data;

      $post = $this->posts->create($post);

      return Redirect::to($thread->url)->with('success', 'thread created');
    }
    else
    {
      return Redirect::to($category->postAlias)->withErrors($validator)->withInput();
    }
  }

  public function getDeleteThread($threadID)
  {
    if( !$this->userCan('delete_threads') )
    {
      return App::abort(403, 'Access denied');
    }
  }

  public function getNewPost($categoryID, $categoryAlias, $threadID, $threadAlias)
  {
    if( !$this->userCan('create_posts') )
    {
      return App::abort(403, 'Access denied');
    }

    $category = $this->categories->getByID($categoryID, array('parentCategory'));
    $thread    = $this->threads->getByID($threadID);
    if ($category == NULL || $thread == NULL)
    {
      return App::abort(404);
    }

    $parentCategory = $category->parentCategory;
    $actionAlias      = $thread->postAlias;
    $prevPosts   = $this->posts->getLastByThread($threadID);

    $this->layout->content = View::make('forum::reply', compact('parentCategory', 'category', 'thread', 'actionAlias', 'prevPosts'));
  }

  public function postCreatePost($categoryID, $categoryAlias, $threadID, $threadAlias)
  {
    if( !$this->userCan('create_posts') )
    {
      return App::abort(403, 'Access denied');
    }

    $category  = $this->categories->getByID($categoryID);
    $thread     = $this->threads->getByID($threadID);
    $validator = Validator::make(Input::all(), $this->postRules);
    if ($validator->passes())
    {
      $data = Input::get('data');

      $post               = new stdClass();
      $post->parent_thread = $thread->id;
      $post->author_id    = $user->id;
      $post->data         = $data;

      $post = $this->posts->create($post);

      return Redirect::to($thread->url)->with('success', 'thread created');
    }
    else
    {
      return Redirect::to($thread->postAlias)->withErrors($validator)->withInput();
    }
  }

  public function getEditPost($categoryID, $categoryAlias, $threadID, $threadAlias, $postID)
  {
    if( !$this->userCan('update_post') )
    {
      return App::abort(403, 'Access denied');
    }

    $category = $this->categories->getByID($categoryID, array('parentCategory'));
    $thread    = $this->threads->getByID($threadID);
    $post  = $this->posts->getByID($postID);
    if ($category == NULL || $thread == NULL || $post == NULL)
    {
      return App::abort(404);
    }

    $parentCategory = $category->parentCategory;
    $actionAlias      = $post->postAlias;

    $this->layout->content = View::make('forum::edit', compact('parentCategory', 'category', 'thread', 'post', 'actionAlias'));
  }

  public function postEditPost($categoryID, $categoryAlias, $threadID, $threadAlias, $postID)
  {
    if( !$this->userCan('update_post') )
    {
      return App::abort(403, 'Access denied');
    }

    $category = $this->categories->getByID($categoryID, array('parentCategory'));
    $thread    = $this->threads->getByID($threadID);
    $post  = $this->posts->getByID($postID);
    if ($category == NULL || $thread == NULL || $post == NULL)
    {
      return App::abort(404);
    }

    $validator = Validator::make(Input::all(), $this->postRules);
    if ($validator->passes())
    {
      $data = Input::get('data');

      $post               = new stdClass();
      $post->id           = $postID;
      $post->parent_thread = $thread->id;
      $post->author_id    = $user->id;
      $post->data         = $data;

      $post = $this->posts->update($post);

      return Redirect::to($post->url)->with('success', 'thread created');
    }
    else
    {
      return Redirect::to($post->postAlias)->withErrors($validator)->withInput();
    }
  }

}
