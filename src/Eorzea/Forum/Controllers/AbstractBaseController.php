<?php namespace Eorzea\Forum\Controllers;

use Eorzea\Forum\Repositories\Categories;
use Eorzea\Forum\Repositories\Threads;
use Eorzea\Forum\Repositories\Posts;
use Eorzea\Forum\AccessControl;

use App;
use Config;
use Controller;
use Input;
use Redirect;
use Route;
use View;
use Validator;

abstract class AbstractBaseController extends Controller {

  // Repositories
  private $categories;
  private $threads;
  private $posts;

  // Collections cache
  private $collections = array();

  // Validator
  private $validator;
  protected $validationRules = array(
    'thread' => [
      'title' => 'required'
    ],
    'post' => [
      'content' => 'required|min:5'
    ]
  );

  // Closures
  private $getCurrentUser;
  private $processAlert;

  public function __construct(Categories $categories, Threads $threads, Posts $posts)
  {
    $this->categories = $categories;
    $this->threads = $threads;
    $this->posts = $posts;

    $this->getCurrentUser = Config::get('forum::integration.current_user');
    $this->processAlert = Config::get('forum::integration.process_alert');
  }

  public function call($closure)
  {
    $args = func_get_args();
    unset($args[0]);

    return call_user_func_array($this->$closure, $args);
  }

  protected function getCurrentUser()
  {
    $user = $this->call('getCurrentUser');
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

  protected function validatePost($post = array())
  {
    $post += array(
      'id'            => 0,
      'parent_thread' => 0,
      'author_id'     => 0,
      'content'       => Input::get('content')
    );

    $this->validator = Validator::make(Input::all(), $this->validationRules['post']);

    if ($this->validator->passes())
    {
      if ($post['id'] > 0)
      {
        $post = $this->posts->update($post);
      }
      else
      {
        unset($post['id']);

        $post = $this->posts->create($post);

        $post->thread->touch();
      }

      return $post->URL;
    }
    else
    {
      $this->processValidationMessages();

      return FALSE;
    }
  }

  protected function processValidationMessages()
  {
    foreach($this->validator->messages()->all() as $message)
    {
      $this->call('processAlert', 'error', $message);
    }
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

    $validator = Validator::make(Input::all(), array_merge($this->validationRules['thread'], $this->validationRules['post']));
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

      $this->call('processAlert', 'success', trans('forum::base.thread_created'));

      return Redirect::to($thread->URL);
    }
    else
    {
      $this->processValidationMessages();

      return Redirect::to($this->collections['category']->postAlias)->withInput();
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
      return Redirect::to($this->collections['thread']->URL);
    }

    if ($this->validatePost(['parent_thread' => $threadID, 'author_id' => $user->id]))
    {
      $this->call('processAlert', 'success', trans('forum::base.reply_added'));

      return Redirect::to($this->collections['thread']->lastPostURL);
    }
    else
    {
      return Redirect::to($this->collections['thread']->replyURL)->withInput();
    }
  }

  public function getDeleteThread($categoryID, $categoryAlias, $threadID, $threadAlias)
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

    $this->call('processAlert', 'success', trans('forum::base.thread_deleted'));

    return Redirect::to($this->collections['category']->URL);
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

    if ($post = $this->validatePost(['id' => $user->id, 'parent_thread' => $threadID, 'author_id' => $user->id]))
    {
      $this->call('processAlert', 'success', trans('forum::base.post_updated'));

      return Redirect::to($post->URL);
    }
    else
    {
      return Redirect::to($this->collections['post']->editURL)->withInput();
    }
  }

  public function getDeletePost($categoryID, $categoryAlias, $threadID, $threadAlias, $postID)
  {
    $this->load(['category' => $categoryID, 'thread' => $threadID, 'post' => $postID]);

    $this->posts->delete($postID);

    $this->call('processAlert', 'success', trans('forum::base.post_deleted'));

    // Force deletion of the thread if it has no remaining posts
    if ($this->collections['thread']->posts->count() == 0)
    {
      $this->threads->delete($threadID);

      return Redirect::to($this->collections['category']->URL);
    }

    return Redirect::to($this->collections['thread']->URL);
  }

}
