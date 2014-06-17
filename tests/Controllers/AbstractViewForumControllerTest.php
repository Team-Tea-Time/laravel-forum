<?php namespace Atrakeur\Forum\Controllers;

use \Atrakeur\Forum\ForumBaseTest;
use \Atrakeur\Forum\Controllers\AbstractViewForumController;

class AbstractViewForumControllerTest extends ForumBaseTest {

	protected $controller;

	private function createController($categories, $topics)
	{
		return $this->getMockForAbstractClass(
			'\Atrakeur\Forum\Controllers\AbstractViewForumController',
			array($categories, $topics)
		);
	}

	private function createCategoriesMock()
	{
		return \Mockery::mock('Eloquent', '\Atrakeur\Forum\Models\ForumCategory');
	}

	private function createTopicsMock()
	{
		return \Mockery::mock('Eloquent', '\Atrakeur\Forum\Models\ForumTopic');
	}

	public function testGetIndex()
	{
		$categoriesMock = $this->createCategoriesMock();
		$topicsMock = $this->createTopicsMock();

		$categoriesMock->shouldReceive('whereTopLevel')->andReturn($categoriesMock);
		$categoriesMock->shouldReceive('with')->andReturn($categoriesMock);
		$categoriesMock->shouldReceive('get')->andReturn($categoriesMock);
		//todo fix property 
		
		$controllerMock = $this->createController($categoriesMock, $topicsMock);

		\App::instance('\Atrakeur\Forum\Models\ForumCategory', $categoriesMock);
		\App::instance('\Atrakeur\Forum\Models\ForumTopic', $topicsMock);
		\App::instance('\Atrakeur\Forum\Controllers\AbstractViewForumController', $controllerMock);
		
		\Route:: get('testRoute', '\Atrakeur\Forum\Controllers\AbstractViewForumController@getIndex');
		$this->call('GET', 'testRoute');
	}

	public function tearDown()
	{
		\Mockery::close();
	}

}
