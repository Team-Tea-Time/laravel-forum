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
use Controller;
use Input;
use Redirect;
use Route;
use View;
use Validator;

abstract class AbstractController extends Controller {

  // Repositories
  private $categories;
  private $threads;
  private $posts;

  // Collections cache
  private $collections = array();

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

  protected function getCurrentUser()
  {
    $user_callback = Config::get('forum::integration.current_user');

    $user = $user_callback();
    if (is_object($user) && get_class($user) == Config::get('forum::integration.user_model'))
    {
      return $user;
    }

    return NULL;
  }

  private function check404()
  {
    foreach($this->collections as $item)
    {
      if($item == NULL)
      {
        App::abort(404);
      }
    }
  }

  private function load($select = array(), $category_with = array())
  {
    $map_model_repos = array(
      'category'  => 'categories',
      'thread'    => 'threads',
      'post'      => 'posts'
    );

    $map_route_permissions = array(
      'forum.get.view.category' => 'access_category',
      'forum.get.view.thread'   => 'access_category',
      'forum.get.create.thread' => 'create_threads',
      'forum.get.delete.thread' => 'delete_threads',
      'forum.get.edit.post'     => 'edit_post',
      'forum.get.delete.post'   => 'delete_posts'
    );

    $route_name = Route::current()->getAction()['as'];

    foreach($select as $model => $id)
    {
      $with = ($model == 'category') ? $category_with : array();

      $this->collections[$model] = $this->$map_model_repos[$model]->getByID($id, $with);

      if(isset($map_route_permissions[$route_name]))
      {
        AccessControl::check($this->collections[$model], $map_route_permissions[$route_name]);
      }
    }

    $this->check404();
  }

  private function makeView($name)
  {
    return View::make($name)->with($this->collections);
  }

  public function getViewIndex()
  {
    $categories = $this->categories->getByParent(NULL, ['subcategories']);

    return View::make('forum::index', compact('categories'));
  }

  public function getViewCategory($categoryID, $categoryAlias)
  {
    $this->load(['category' => $categoryID], ['parentCategory', 'subCategories', 'threads']);

    $with = array(
      'paginationLinks' => $this->threads->getPaginationLinks('parent_category', $categoryID)
    );

    return $this->makeView('forum::category')->with($with);
  }

  public function getViewThread($categoryID, $categoryAlias, $threadID, $threadAlias)
  {
    $this->load(['category' => $categoryID, 'thread' => $threadID]);

    $with = array(
      'posts'           => $this->posts->getByThread($threadID),
      'paginationLinks' => $this->posts->getPaginationLinks('parent_thread', $threadID)
    );

    return $this->makeView('forum::thread')->with($with);
  }

  public function getCreateThread($categoryID, $categoryAlias)
  {
    $this->load(['category' => $categoryID]);

    return $this->makeView('forum::thread-create');
  }

  public function postCreateThread($categoryID, $categoryAlias)
  {
    $user = $this->getCurrentUser();

    $this->load(['category' => $categoryID]);

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

      $this->posts->create($post);

      return Redirect::to($thread->URL)->with('success', 'thread created');
    }
    else
    {
      return Redirect::to($this->collections['category']->postAlias)->withErrors($validator)->withInput();
    }
  }

  public function getReplyToThread($categoryID, $categoryAlias, $threadID, $threadAlias)
  {
    $this->load(['category' => $categoryID, 'thread' => $threadID]);

    if (!$this->collections['thread']->canPost)
    {
      return Redirect::to($this->collections['thread']->URL);
    }

    $with = array(
      'prevPosts'   => $this->posts->getLastByThread($threadID)
    );

    return $this->makeView('forum::thread-reply')->with($with);
  }

  public function postReplyToThread($categoryID, $categoryAlias, $threadID, $threadAlias)
  {
    $user = $this->getCurrentUser();

    $this->load(['category' => $categoryID, 'thread' => $threadID]);

    if (!$this->collections['thread']->canPost)
    {
      return Redirect::to($this->collections['thread']->URL);
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

      $post->thread->touch();

      return Redirect::to($this->collections['thread']->lastPostURL)->with('success', 'thread created');
    }
    else
    {
      return Redirect::to($this->collections['thread']->replyURL)->withErrors($validator)->withInput();
    }
  }

  public function getDeleteThread($categoryID, $categoryAlias, $threadID, $threadAlias)
  {
    $this->load(['category' => $categoryID, 'thread' => $threadID]);

    $this->threads->delete($threadID);

    return Redirect::to($this->collections['category']->URL)->with('success', 'thread deleted');
  }

  public function getEditPost($categoryID, $categoryAlias, $threadID, $threadAlias, $postID)
  {
    $this->load(['category' => $categoryID, 'thread' => $threadID, 'post' => $postID]);

    return $this->makeView('forum::post-edit');
  }

  public function postEditPost($categoryID, $categoryAlias, $threadID, $threadAlias, $postID)
  {
    $user = $this->getCurrentUser();

    $this->load(['category' => $categoryID, 'thread' => $threadID, 'post' => $postID]);

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
      return Redirect::to($this->collections['post']->editURL)->withErrors($validator)->withInput();
    }
  }

  public function getDeletePost($categoryID, $categoryAlias, $threadID, $threadAlias, $postID)
  {
    $this->load(['category' => $categoryID, 'thread' => $threadID, 'post' => $postID]);

    $this->posts->delete($postID);

    // Force deletion of the thread if it has no remaining posts
    if ($this->collections['thread']->posts->count() == 0)
    {
      $this->threads->delete($threadID);

      return Redirect::to($this->collections['category']->URL)->with('success', 'post deleted');
    }

    return Redirect::to($this->collections['thread']->URL)->with('success', 'post deleted');
  }

}
