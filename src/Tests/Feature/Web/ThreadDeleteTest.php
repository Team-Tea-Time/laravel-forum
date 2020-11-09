<?php

namespace TeamTeaTime\Forum\Tests\Feature\Web;

use Illuminate\Foundation\Auth\User;
use Orchestra\Testbench\Factories\UserFactory;
use TeamTeaTime\Forum\Database\Factories\ThreadFactory;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Models\Post;
use TeamTeaTime\Forum\Support\Web\Forum;
use TeamTeaTime\Forum\Tests\FeatureTestCase;

class ThreadDeleteTest extends FeatureTestCase
{
    private string $route = 'thread.delete';

    private UserFactory $userFactory;
    private ThreadFactory $threadFactory;

    private User $user;
    private Thread $thread;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->threadFactory = ThreadFactory::new();
        $this->userFactory = UserFactory::new();

        $this->user = $this->userFactory->createOne();
        $this->thread = $this->threadFactory->createOne(['author_id' => $this->user->getKey()]);
    }

    /** @test */
    public function should_302_when_not_logged_in()
    {
        $response = $this->delete(Forum::route($this->route, $this->thread), []);
        $response->assertStatus(302);
    }

    /** @test */
    public function should_404_with_invalid_thread_id()
    {
        $thread = $this->thread;
        $thread->id++;
        $response = $this->actingAs($this->user)
            ->delete(Forum::route($this->route, $thread), []);
        $response->assertStatus(404);
    }
}