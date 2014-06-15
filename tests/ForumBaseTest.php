<?php namespace Atrakeur\Forum;

abstract class ForumBaseTest extends \Orchestra\Testbench\TestCase {

	protected function getPackageProviders()
    {
        return array('\Atrakeur\Forum\ForumServiceProvider');
    }

}
