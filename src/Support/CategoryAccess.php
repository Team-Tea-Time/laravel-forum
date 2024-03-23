<?php

namespace TeamTeaTime\Forum\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;
use Kalnoy\Nestedset\Collection as NestedCollection;
use TeamTeaTime\Forum\Models\Category;

/**
 * CategoryAccess provides utilities for retrieving category data based on category privacy and user authorisation.
 */
class CategoryAccess
{
    const DEFAULT_SELECT = ['*'];
    const DEFAULT_WITH = ['newestThread', 'latestActiveThread', 'newestThread.lastPost', 'latestActiveThread.lastPost'];

    public static function getPrivateAncestor(?User $user, Category $category): ?Category
    {
        return $user && $user->can('manageCategories')
            ? Category::defaultOrder()
                ->where('is_private', true)
                ->ancestorsOf($category->id)
                ->first()
            : null;
    }

    public static function isAccessibleTo(?User $user, int $categoryId): bool
    {
        return static::getFilteredAncestorsFor($user, $categoryId, $select = ['id'], $with = [])->keys()->contains($categoryId);
    }

    public static function getFilteredIdsFor(?User $user): Collection
    {
        return static::getFilteredTreeFor($user, $select = ['id'], $with = [])->keys();
    }

    public static function getFilteredAncestorsFor(?User $user, int $categoryId, array $select = self::DEFAULT_SELECT, array $with = self::DEFAULT_WITH): NestedCollection
    {
        $categories = static::getQuery($select, $with)
            ->ancestorsAndSelf($categoryId)
            ->keyBy('id');

        return static::filter($categories, $user);
    }

    public static function getFilteredDescendantsFor(?User $user, int $categoryId, array $select = self::DEFAULT_SELECT, array $with = self::DEFAULT_WITH): NestedCollection
    {
        $categories = static::getQuery($select, $with)
            ->descendantsAndSelf($categoryId)
            ->keyBy('id');

        return static::filter($categories, $user);
    }

    public static function getFilteredTreeFor(?User $user, array $select = self::DEFAULT_SELECT, array $with = self::DEFAULT_WITH): NestedCollection
    {
        $categories = static::getQuery($select, $with)
            ->get()
            ->keyBy('id');

        // TODO: This is a workaround for a serialisation issue. See: https://github.com/lazychaser/laravel-nestedset/issues/487
        //       Doing this yields the same result as toTree(), avoiding the infinite loop.
        //       Once the issue is fixed, this can be removed.
        foreach ($categories as $id => $category) {
            if ($category->parent_id != null) {
                $categories->forget($id);
            }
        }

        $categories = $categories->filter(fn($category) => $category->parent == null);

        return static::filter($categories, $user);
    }

    private static function getQuery(array $select = self::DEFAULT_SELECT, array $with = self::DEFAULT_WITH): Builder
    {
        // 'is_private' and 'parent_id' fields are required for filtering
        return Category::select(array_merge($select, ['is_private', 'parent_id']))
            ->with($with)
            ->defaultOrder();
    }

    private static function filter(NestedCollection $categories, ?User $user, ?NestedCollection $rejected = null): NestedCollection
    {
        if ($rejected == null) {
            $rejected = $categories->reject(function ($category, $id) use ($user) {
                return !$category->is_private || (!is_null($user) && $user->can('view', $category));
            });
        }

        $categories = $categories->whereNotIn('id', $rejected->keys());
        $rejected = $categories->whereIn('parent_id', $rejected->keys());

        if ($rejected->count() > 0) {
            $categories = static::filter($categories, $user, $rejected);
        }

        return $categories;
    }
}
