<?php

namespace TeamTeaTime\Forum\Tests\Feature\Web;

use Illuminate\Foundation\Auth\User;
use Orchestra\Testbench\Factories\UserFactory;
use TeamTeaTime\Forum\Database\Factories\CategoryFactory;
use TeamTeaTime\Forum\Database\Factories\PostFactory;
use TeamTeaTime\Forum\Database\Factories\ThreadFactory;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Post;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Support\Web\Forum;
use TeamTeaTime\Forum\Tests\FeatureTestCase;

class CategoryShowTest extends FeatureTestCase
{
    private const ROUTE = 'category.show';

    private CategoryFactory $categoryFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryFactory = CategoryFactory::new();
    }

    /** @test */
    public function should_404_when_viewing_child_of_inaccessible_category()
    {
        $secondLevelCategory = $this->seedCategories();

        $response = $this->get(Forum::route(self::ROUTE, $secondLevelCategory));
        $response->assertStatus(404);
    }

    private function seedCategories(): Category
    {
        $topLevelCategory = $this->categoryFactory->createOne([
            'is_private' => true
        ]);
        $secondLevelCategory = $this->categoryFactory->createOne();
        $topLevelCategory->appendNode($secondLevelCategory);

        return $secondLevelCategory;
    }
}
