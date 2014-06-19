<?php namespace Atrakeur\Forum\Controllers;

use \Atrakeur\Forum\ForumBaseTest;
use \Atrakeur\Forum\Models\ForumCategory;
use \Atrakeur\Forum\Controllers\AbstractForumController;

class AbstractForumControllerTest extends ForumBaseTest {

	protected function getPackageProviders()
	{
		return array('\Atrakeur\Forum\ForumServiceProvider');
	}

	public function testFireEvent()
	{
		\Event::shouldReceive('fire')->once()->with('randomEvent', 'randomData');

		//change visibility of fireEvent from protected to public
		$controller = $this->getMockForAbstractClass('\Atrakeur\Forum\Controllers\AbstractForumController');

		$reflectionOfController = new \ReflectionClass('\Atrakeur\Forum\Controllers\AbstractForumController');
		$method = $reflectionOfController->getMethod('fireEvent');
		$method->setAccessible(true);

		$method->invoke($controller, 'randomEvent', 'randomData');
	}

	public function testCurrentUser() 
	{
		//use an simple stdClass object to represent user model in tests
		\Config::set('forum::integration.usermodel', "stdClass");

		//change visibility of getCurrentUser from protected to public
		$controller = $this->getMockForAbstractClass('\Atrakeur\Forum\Controllers\AbstractForumController');

		$reflectionOfController = new \ReflectionClass('\Atrakeur\Forum\Controllers\AbstractForumController');
		$method = $reflectionOfController->getMethod('getCurrentUser');
		$method->setAccessible(true);

		//test good current user
		$user = new \stdClass();
		\Config::set('forum::integration.currentuser', function() use ($user)
		{
			return $user;
		});
		$data = $method->invoke($controller);
		$this->assertEquals($user, $data);

		//test falsey responce
		\Config::set('forum::integration.currentuser', function() {
			return false;
		});
		$this->assertEquals(null, $method->invoke($controller));

		//test true like responce
		\Config::set('forum::integration.currentuser', function() {
			return true;
		});
		$this->assertEquals(null, $method->invoke($controller));
	}

}
