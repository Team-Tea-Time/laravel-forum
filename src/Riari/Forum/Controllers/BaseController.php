<?php namespace Riari\Forum\Controllers;

use Riari\Forum\Repositories\Categories;
use Riari\Forum\Repositories\Threads;
use Riari\Forum\Repositories\Posts;
use Riari\Forum\Libraries\AccessControl;
use Riari\Forum\Libraries\Alerts;
use Riari\Forum\Libraries\Validation;

use App;
use Config;
use Controller;
use Input;
use Redirect;
use Route;
use View;
use Validator;

abstract class BaseController extends Controller {

  // Repositories
  private $categories;
  private $threads;
  private $posts;

  // Collections cache
  private $collections = array();

  public function __construct(Categories $categories, Threads $threads, Posts $posts)
  {
    $this->categories = $categories;
    $this->threads = $threads;
    $this->posts = $posts;
  }

  protected function getCurrentUser()
  {
    $current_user_callback = Config::get('forum::integration.current_user');

    $user = $current_user_callback();
    if (is_object($user) && get_class($user) == Config::get('forum::integration.user_model'))
    {
      return $user;
    }

    return NULL;
  }

  protected function check404()
  {
    foreach($this->collections as $item)
    {
      if($item == NULL)
      {
        App::abort(404);
      }
    }
  }

  protected function load($select = array(), $category_with = array())
  {
    $map_model_repos = array(
      'category'  => 'categories',
      'thread'    => 'threads',
      'post'      => 'posts'
    );

    $map_route_permissions = array(
      'forum.get.view.category'   => 'access_category',
      'forum.get.view.thread'     => 'access_category',
      'forum.get.create.thread'   => 'create_threads',
      'forum.post.lock.thread'    => 'lock_threads',
      'forum.post.pin.thread'     => 'pin_threads',
      'forum.delete.thread'       => 'delete_threads',
      'forum.get.edit.post'       => 'edit_post',
      'forum.delete.post'         => 'delete_posts'
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

  protected function makeView($name)
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

    $thread_valid = Validation::check('thread');
    $post_valid = Validation::check('post');
    if ($thread_valid && $post_valid)
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

      Alerts::add('success', trans('forum::base.thread_created'));

      return Redirect::to($thread->route);
    }
    else
    {
      return Redirect::to($this->collections['category']->newThreadRoute)->withInput();
    }
  }

  public function getReplyToThread($categoryID, $categoryAlias, $threadID, $threadAlias)
  {
    $this->load(['category' => $categoryID, 'thread' => $threadID]);

    if (!$this->collections['thread']->canPost)
    {
      return Redirect::to($this->collections['thread']->route);
    }

    $with = array(
      'prevPosts' => $this->posts->getLastByThread($threadID)
    );

    return $this->makeView('forum::thread-reply')->with($with);
  }

  public function postReplyToThread($categoryID, $categoryAlias, $threadID, $threadAlias)
  {
    $user = $this->getCurrentUser();

    $this->load(['category' => $categoryID, 'thread' => $threadID]);

    if (!$this->collections['thread']->canPost)
    {
      return Redirect::to($this->collections['thread']->route);
    }

    $post_valid = Validation::check('post');
    if ($post_valid)
    {
      $post = array(
        'parent_thread' => $threadID,
        'author_id'     => $user->id,
        'content'       => Input::get('content')
      );

      $post = $this->posts->create($post);

      $post->thread->touch();

      Alerts::add('success', trans('forum::base.reply_added'));

      return Redirect::to($this->collections['thread']->lastPostRoute);
    }
    else
    {
      return Redirect::to($this->collections['thread']->replyRoute)->withInput();
    }
  }

  public function postLockThread($categoryID, $categoryAlias, $threadID, $threadAlias)
  {
    $this->load(['thread' => $threadID]);

    $this->collections['thread']->toggle('locked');

    return Redirect::to($this->collections['thread']->route);
  }

  public function postPinThread($categoryID, $categoryAlias, $threadID, $threadAlias)
  {
    $this->load(['thread' => $threadID]);

    $this->collections['thread']->toggle('pinned');

    return Redirect::to($this->collections['thread']->route);
  }

  public function deleteThread($categoryID, $categoryAlias, $threadID, $threadAlias)
  {
    $this->load(['category' => $categoryID, 'thread' => $threadID]);

    if (Config::get('forum::preferences.soft_delete'))
    {
      $this->collections['thread']->posts()->delete();
    }
    else
    {
      $this->collections['thread']->posts()->forceDelete();
    }

    $this->threads->delete($threadID);

    Alerts::add('success', trans('forum::base.thread_deleted'));

    return Redirect::to($this->collections['category']->route);
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

    $post_valid = Validation::check('post');
    if ($post_valid)
    {
      $post = array(
        'id'            => $postID,
        'parent_thread' => $threadID,
        'author_id'     => $user->id,
        'content'       => Input::get('content')
      );

      $post = $this->posts->update($post);

      Alerts::add('success', trans('forum::base.post_updated'));

      return Redirect::to($post->route);
    }
    else
    {
      return Redirect::to($this->collections['post']->editRoute)->withInput();
    }
  }

  public function deletePost($categoryID, $categoryAlias, $threadID, $threadAlias, $postID)
  {
    $this->load(['category' => $categoryID, 'thread' => $threadID, 'post' => $postID]);

    $this->posts->delete($postID);

    Alerts::add('success', trans('forum::base.post_deleted'));

    // Force deletion of the thread if it has no remaining posts
    if ($this->collections['thread']->posts->count() == 0)
    {
      $this->threads->delete($threadID);

      return Redirect::to($this->collections['category']->route);
    }

    return Redirect::to($this->collections['thread']->route);
  }

}
