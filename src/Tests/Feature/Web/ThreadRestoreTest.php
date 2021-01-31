<?php

namespace TeamTeaTime\Forum\Tests\Feature\Web;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\Factories\UserFactory;
use TeamTeaTime\Forum\Database\Factories\CategoryFactory;
use TeamTeaTime\Forum\Database\Factories\PostFactory;
use TeamTeaTime\Forum\Database\Factories\ThreadFactory;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Post;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Support\Web\Forum;
use TeamTeaTime\Forum\Tests\FeatureTestCase;

class ThreadRestoreTest extends FeatureTestCase
{
    private string $route = 'thread.restore';

    private Category $category;
    private Thread $thread;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $categoryFactory = CategoryFactory::new();
        $postFactory = PostFactory::new();
        $threadFactory = ThreadFactory::new();
        $userFactory = UserFactory::new();
        
        $this->user = $userFactory->createOne();

        $this->category = $categoryFactory->createOne();
        $this->thread = $threadFactory->createOne([
            'author_id' => $this->user->getKey(),
            'category_id' => $this->category->getKey(),
            'deleted_at' => Carbon::now()
        ]);
        $postFactory->createOne(['thread_id' => $this->thread->getKey()]);
    }

    /** @test */
    public function should_bump_category_stats()
    {
        $this->actingAs($this->user)->post(Forum::route($this->route, $this->thread), []);
        
        $category = Category::find($this->category->getKey());

        $this->assertEquals(1, $category->thread_count);
        $this->assertEquals(1, $category->post_count);
    }
}