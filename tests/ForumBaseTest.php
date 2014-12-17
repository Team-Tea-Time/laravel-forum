<?php namespace Eorzea\Forum;

abstract class ForumBaseTest extends \Orchestra\Testbench\TestCase {

	protected function getPackageProviders()
    {
        return array('\Eorzea\Forum\ForumServiceProvider');
    }

}
