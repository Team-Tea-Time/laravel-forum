<?php namespace Atrakeur\Forum\Controllers;

use \Atrakeur\Forum\ForumBaseTest;
use \Atrakeur\Forum\Controllers\AbstractForumController;

class AbstractForumControllerTest extends ForumBaseTest {

	protected function getPackageProviders()
    {
        return array('\Atrakeur\Forum\ForumServiceProvider');
    }

	public function testCurrentUser() 
	{
		//use an simple stdClass object to represent user model in tests
		\Config::set('forum::integration.usermodel', "stdClass");

		//change visibility of getCurrentUser from protected to public
		$controller = $this->getMockForAbstractClass('\Atrakeur\Forum\Controllers\AbstractForumController');

		$reflectionOfUser = new \ReflectionClass('\Atrakeur\Forum\Controllers\AbstractForumController');

		$method = $reflectionOfUser->getMethod('getCurrentUser');
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
