<?php

namespace TeamTeaTime\Forum\Tests\Feature\Web;

use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Support\Access\CategoryAccess;

class CategoryManageTest extends TestCase
{
    #[Test]
    public function should_remove_recursive_parent_relationships_before_json_serialisation()
    {
        $parent = new Category(['title' => 'Parent category']);
        $child = new Category(['title' => 'Child category']);

        $parent->setAttribute('id', 1);
        $child->setAttribute('id', 2);

        $parent->setAppends([]);
        $child->setAppends([]);

        $child->setRelation('parent', $parent);
        $child->setRelation('children', new Collection());
        $parent->setRelation('children', new Collection([$child]));

        $categories = new Collection([$parent]);

        $originalJson = json_encode($categories);

        $this->assertNotFalse($originalJson);
        $this->assertStringContainsString('"parent":{"title":"Parent category"', $originalJson);

        $sanitisedCategories = CategoryAccess::removeParentRelationships($categories);
        $json = json_encode($sanitisedCategories);

        $this->assertSame($categories, $sanitisedCategories);
        $this->assertNull($child->getRelation('parent'));
        $this->assertSame(JSON_ERROR_NONE, json_last_error(), json_last_error_msg());
        $this->assertNotFalse($json, 'Expected the manage page category tree to be JSON serializable.');
        $this->assertStringContainsString('Parent category', $json);
        $this->assertStringContainsString('Child category', $json);
        $this->assertStringContainsString('"parent":null', $json);
    }

    #[Test]
    public function should_not_set_children_as_attribute_when_removing_parent_relationships()
    {
        $parent = new Category(['title' => 'Parent category']);
        $child = new Category(['title' => 'Child category']);

        $parent->setAttribute('id', 1);
        $child->setAttribute('id', 2);

        $parent->setAppends([]);
        $child->setAppends([]);

        $child->setRelation('parent', $parent);
        $child->setRelation('children', new Collection());
        $parent->setRelation('children', new Collection([$child]));

        $categories = new Collection([$parent]);

        CategoryAccess::removeParentRelationships($categories);

        $this->assertArrayNotHasKey('children', $parent->getAttributes(),
            'removeParentRelationships should not set children as a model attribute');
        $this->assertArrayNotHasKey('children', $child->getAttributes(),
            'removeParentRelationships should not set children as a model attribute');

        $this->assertTrue($parent->relationLoaded('children'));
        $this->assertCount(1, $parent->getRelation('children'));
        $this->assertNull($child->getRelation('parent'));
    }
}
