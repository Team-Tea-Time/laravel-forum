<?php namespace Atrakeur\Forum\Repositories;

use \Atrakeur\Forum\ForumBaseTest;
use \Atrakeur\Forum\Repositories\CategoriesRepository;

class CategoriesRepositoryTest extends ForumBaseTest {

	protected function getPackageProviders()
	{
		return array('\Atrakeur\Forum\ForumServiceProvider');
	}

	public function testGetById()
	{
		$modelMock = \Mockery::mock('\Atrakeur\Forum\Models\ForumCategory');

		$modelMock->shouldReceive('where')->with('id', '=', 1)->once()->andReturn($modelMock);
		$modelMock->shouldReceive('with')->with(array())->once()->andReturn($modelMock);
		$modelMock->shouldReceive('first')->once()->andReturn($modelMock);
		$modelMock->shouldReceive('convertToObject')->once()->andReturn($modelMock);

		$repository = new CategoriesRepository($modelMock);

		$this->assertEquals($modelMock, $repository->getById(1));
	}

	public function testGetByIdNull()
	{
		$modelMock = \Mockery::mock('\Atrakeur\Forum\Models\ForumCategory');
		$repository = new CategoriesRepository($modelMock);

		$this->setExpectedException('\InvalidArgumentException');
		$this->assertEquals(array(), $repository->getById("a"));
	}

	private function getCategoryModelForParentTest($id, $with, $return)
	{
		$modelMock = \Mockery::mock('\Atrakeur\Forum\Models\ForumCategory');

		$modelMock->shouldReceive('where')->with('parent_category', '=', $id)->once()->andReturn($modelMock);
		$modelMock->shouldReceive('with')->with($with)->once()->andReturn($modelMock);
		$modelMock->shouldReceive('get')->once()->andReturn($modelMock);
		$modelMock->shouldReceive('convertToObject')->once()->andReturn($return);

		return new CategoriesRepository($modelMock);
	}

	public function testGetByParentNull()
	{
		$repository = $this->getCategoryModelForParentTest(null, array(), array());

		$this->assertEquals(array(), $repository->getByParent(null));
	}

	public function testGetByParentInvalid()
	{
		$modelMock = \Mockery::mock('\Atrakeur\Forum\Models\ForumCategory');
		$repository = new CategoriesRepository($modelMock);

		$this->setExpectedException('\InvalidArgumentException');
		$this->assertEquals(array(), $repository->getByParent("a"));
	}

	public function testGetByParentInteger()
	{
		//mock the ForumCategory model
		$repository = $this->getCategoryModelForParentTest(1, array(), array());

		$this->assertEquals(array(), $repository->getByParent(1));
	}

	public function testGetByParentArray()
	{
		//mock the ForumCategory model
		$repository = $this->getCategoryModelForParentTest(1, array(), array());

		$this->assertEquals(array(), $repository->getByParent(array('id' => 1)));
	}

	public function tearDown()
	{
		\Mockery::close();
	}

}
