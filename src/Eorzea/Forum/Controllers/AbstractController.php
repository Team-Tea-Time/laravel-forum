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

  private function check404(Array $items = array())
  {
    foreach($items as $item)
    {
      if($item == NULL)
      {
        App::abort(404);
      }
    }
  }

  public function getIndex()
  {
    $categories = $this->categories->getByParent(NULL, ['subcategories']);

    return View::make('forum::index', compact('categories'));
  }

  public function getCategory($categoryID, $categoryAlias)
  {
    $category = $this->categories->getByID($categoryID, ['parentCategory', 'subcategories', 'threads']);

    $this->check404([$category]);

    AccessControl::check($category, 'access_category');

    $parentCategory = $category->parentCategory;
    $subcategories  = $category->subcategories;
    $threads = $category->threads;

    return View::make('forum::category', compact('parentCategory', 'category', 'subcategories', 'threads'));
  }

  public function getThread($categoryID, $categoryAlias, $threadID, $threadAlias, $page = 0)
  {
    $category = $this->categories->getByID($categoryID, ['parentCategory']);
    $thread = $this->threads->getByID($threadID);

    $this->check404([$category, $thread]);

    AccessControl::check($category, 'access_category');

    $parentCategory  = $category->parentCategory;
    $posts = $this->posts->getByThread($thread->id);
    $paginationLinks = $this->posts->getPaginationLinks('parent_thread', $thread->id);

    return View::make('forum::thread', compact('parentCategory', 'category', 'thread', 'posts', 'paginationLinks'));
  }

  public function getCreateThread($categoryID, $categoryAlias)
  {
    $category = $this->categories->getByID($categoryID, ['parentCategory']);

    AccessControl::check($category, 'create_threads');

    $parentCategory = $category->parentCategory;
    $actionAlias = $category->postAlias;

    return View::make('forum::thread-create', compact('parentCategory', 'category', 'actionAlias'));
  }

  public function postCreateThread($categoryID, $categoryAlias)
  {
    $user = $this->getCurrentUser();
    $category = $this->categories->getByID($categoryID);

    AccessControl::check($category, 'create_threads');

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
      return Redirect::to($category->postAlias)->withErrors($validator)->withInput();
    }
  }

  public function getDeleteThread($threadID)
  {
    $thread = $this->threads->getByID($threadID);

    AccessControl::check($thread, 'delete_threads');
  }

  public function postDeleteThread($threadID)
  {

  }

  public function getCreatePost($categoryID, $categoryAlias, $threadID, $threadAlias)
  {

    $category = $this->categories->getByID($categoryID, ['parentCategory']);
    $thread = $this->threads->getByID($threadID);

    $this->check404([$category, $thread]);

    AccessControl::check($thread, 'create_posts');

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

    AccessControl::check($category, 'create_posts');

    $validator = Validator::make(Input::all(), $this->postRules);
    if ($validator->passes())
    {
      $post = array(
        'parent_thread' => $threadID,
        'author_id'     => $user->id,
        'content'       => Input::get('content')
      );

      $this->posts->create($post);

      return Redirect::to($thread->URL)->with('success', 'thread created');
    }
    else
    {
      return Redirect::to($thread->postAlias)->withErrors($validator)->withInput();
    }
  }

  public function getEditPost($categoryID, $categoryAlias, $threadID, $threadAlias, $postID)
  {
    $category = $this->categories->getByID($categoryID, ['parentCategory']);
    $thread = $this->threads->getByID($threadID);
    $post = $this->posts->getByID($postID);

    $this->check404([$category, $thread, $post]);

    AccessControl::check($post, 'edit_post');

    $parentCategory = $category->parentCategory;
    $actionAlias = $post->postAlias;

    return View::make('forum::post-edit', compact('parentCategory', 'category', 'thread', 'post', 'actionAlias'));
  }

  public function postEditPost($categoryID, $categoryAlias, $threadID, $threadAlias, $postID)
  {
    $user = $this->getCurrentUser();
    $category = $this->categories->getByID($categoryID, ['parentCategory']);
    $thread = $this->threads->getByID($threadID);
    $post = $this->posts->getByID($postID);

    $this->check404([$category, $thread, $post]);

    AccessControl::check($post, 'edit_post');

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
