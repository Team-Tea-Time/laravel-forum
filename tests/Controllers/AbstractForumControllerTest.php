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
		$controller = $this->getMockForAbstractClass('\Atrakeur\Forum\Controllers\AbstractForumController');
		$reflectionOfUser = new \ReflectionClass('\Atrakeur\Forum\Controllers\AbstractForumController');
		$method = $reflectionOfUser->getMethod('getCurrentUser');
		$method->setAccessible(true);

		\Config::set('forum::integration.currentuser', function() {
			$user = $this->getMock('\User');
			return $user;
		});
		$this->assertEquals(null, $method->invoke($controller));

		\Config::set('forum::integration.currentuser', function() {
			return false;
		});
		$this->assertEquals(null, $method->invoke($controller));

		\Config::set('forum::integration.currentuser', function() {
			return true;
		});
		$this->assertEquals(null, $method->invoke($controller));
	}

}
