<?php namespace Eorzea\Forum\Controllers;

use \Eorzea\Forum\ForumBaseTest;

class AbstractViewForumControllerTest extends ForumBaseTest {

	protected function getPackageProviders()
	{
		return array('\Eorzea\Forum\ForumServiceProvider');
	}

	private function createController($categories, $threads, $posts)
	{
		return $this->getMockForAbstractClass(
			'\Eorzea\Forum\Controllers\AbstractViewForumController',
			array($categories, $threads, $posts)
		);
	}

	private function createCategoriesMock()
	{
		return \Mockery::mock('Eloquent', '\Eorzea\Forum\Repositories\CategoriesRepository');
	}

	private function createThreadsMock()
	{
		return \Mockery::mock('Eloquent', '\Eorzea\Forum\Repositories\ThreadsRepository');
	}

	private function createpostsMock()
	{
		return \Mockery::mock('Eloquent', '\Eorzea\Forum\Repositories\postsRepository');
	}

	public function testGetIndex()
	{
		$categoriesMock = $this->createCategoriesMock();
		$threadsMock = $this->createThreadsMock();
		$postsMock = $this->createpostsMock();

		$categoryMock = new \stdClass();
		$categoryMock->url = 'url';
		$categoryMock->title = 'title';
		$categoryMock->subtitle = 'title';
		$categoryMock->subcategories = array();
		$categoriesMock->shouldReceive('getByParent')->andReturn(array($categoryMock));

		$controller = $this->createController($categoriesMock, $threadsMock, $postsMock);

		\App::instance('\Eorzea\Forum\Repositories\CategoriesRepository', $categoriesMock);
		\App::instance('\Eorzea\Forum\Models\ForumThread', $threadsMock);
		\App::instance('\Eorzea\Forum\Controllers\AbstractViewForumController', $controller);

		\Route:: get('testRoute', '\Eorzea\Forum\Controllers\AbstractViewForumController@getIndex');
		$this->call('GET', 'testRoute');

		//$this->assertViewHas('categories');
	}

	public function testGetCategoryInvalid()
	{
		$categoriesMock = $this->createCategoriesMock();
		$threadsMock = $this->createThreadsMock();
		$postsMock = $this->createpostsMock();

		$categoriesMock->shouldReceive('getByID')->once()->with(31415, array('parentCategory', 'subCategories', 'threads'))->andReturn(null);

		$controller = $this->createController($categoriesMock, $threadsMock, $postsMock);

		\App::instance('\Eorzea\Forum\Repositories\CategoriesRepository', $categoriesMock);
		\App::instance('\Eorzea\Forum\Models\ForumThread', $threadsMock);
		\App::instance('\Eorzea\Forum\Controllers\AbstractViewForumController', $controller);

		$this->setExpectedException('\Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
		\Route:: get('testRoute/{categoryID}/{categoryURL}', '\Eorzea\Forum\Controllers\AbstractViewForumController@getCategory');
		$this->call('GET', 'testRoute/31415/FalseTestName');
	}

	public function testGetCategory()
	{
		$categoriesMock = $this->createCategoriesMock();
		$threadsMock = $this->createThreadsMock();
		$postsMock = $this->createpostsMock();

		$categoryMock = new \stdClass();
		$categoryMock->url = 'url';
		$categoryMock->postURL = 'url';
		$categoryMock->title = 'title';
		$categoryMock->canPost = false;
		$categoryMock->subtitle = 'title';
		$categoryMock->subCategories = array();
		$categoryMock->threads = array();
		$categoryMock->parentCategory = null;
		$categoriesMock->shouldReceive('getByID')->once()->with(1, array('parentCategory', 'subCategories', 'threads'))->andReturn($categoryMock);

		$controller = $this->createController($categoriesMock, $threadsMock, $postsMock);

		\App::instance('\Eorzea\Forum\Repositories\CategoriesRepository', $categoriesMock);
		\App::instance('\Eorzea\Forum\Models\ForumThread', $threadsMock);
		\App::instance('\Eorzea\Forum\Controllers\AbstractViewForumController', $controller);

		//$this->setExpectedException('\Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
		\Route:: get('testRoute/{categoryID}/{categoryURL}', '\Eorzea\Forum\Controllers\AbstractViewForumController@getCategory');
		$this->call('GET', 'testRoute/1/title');
	}

	public function tearDown()
	{
		\Mockery::close();
	}

}
