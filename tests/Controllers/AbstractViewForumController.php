<?php namespace Atrakeur\Forum\Controllers;

use \Atrakeur\Forum\ForumBaseTest;

class AbstractViewForumControllerTest extends ForumBaseTest {

	protected function getPackageProviders()
	{
		return array('\Atrakeur\Forum\ForumServiceProvider');
	}

	private function createController($categories, $topics)
	{
		return $this->getMockForAbstractClass(
			'\Atrakeur\Forum\Controllers\AbstractViewForumController',
			array($categories, $topics)
		);
	}

	private function createCategoriesMock()
	{
		return \Mockery::mock('Eloquent', '\Atrakeur\Forum\Repositories\CategoriesRepository');
	}

	private function createTopicsMock()
	{
		return \Mockery::mock('Eloquent', '\Atrakeur\Forum\Models\ForumTopic');
	}

	public function testGetIndex()
	{
		$categoriesMock = $this->createCategoriesMock();
		$topicsMock = $this->createTopicsMock();

		$categoryMock = new \stdClass();
		$categoryMock->url = 'url';
		$categoryMock->title = 'title';
		$categoryMock->subtitle = 'title';
		$categoryMock->subcategories = array();
		$categoriesMock->shouldReceive('getByParent')->andReturn(array($categoryMock));

		$controller = $this->createController($categoriesMock, $topicsMock);

		\App::instance('\Atrakeur\Forum\Repositories\CategoriesRepository', $categoriesMock);
		\App::instance('\Atrakeur\Forum\Models\ForumTopic', $topicsMock);
		\App::instance('\Atrakeur\Forum\Controllers\AbstractViewForumController', $controller);

		\Route:: get('testRoute', '\Atrakeur\Forum\Controllers\AbstractViewForumController@getIndex');
		$this->call('GET', 'testRoute');

		//$this->assertViewHas('categories');
	}

	public function tearDown()
	{
		\Mockery::close();
	}

}
