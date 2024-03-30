<?php

namespace TeamTeaTime\Forum\Http\Livewire\Pages;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Attributes\Locked;
use Livewire\Component;
use TeamTeaTime\Forum\{
    Actions\CreateCategory as Action,
    Events\UserCreatingCategory,
    Events\UserCreatedCategory,
    Models\Category,
    Support\Access\CategoryAccess,
    Support\Authorization\CategoryAuthorization,
    Support\Validation\CategoryRules,
};

class CategoryCreate extends Component
{
    #[Locked]
    public Collection $categories;

    #[Url]
    public int $parent_id;

    // Form fields
    public string $title;
    public string $description = "";
    public string $color_light_mode;
    public string $color_dark_mode;
    public int $parent_category;
    public bool $accepts_threads = false;
    public bool $is_private = false;

    public function mount(Request $request)
    {
        $categories = CategoryAccess::getFilteredTreeFor($request->user())->toTree();

        // TODO: This is a workaround for a serialisation issue. See: https://github.com/lazychaser/laravel-nestedset/issues/487
        //       Once the issue is fixed, this can be removed.
        $this->categories = CategoryAccess::removeParentRelationships($categories);
        $this->color_light_mode = config('forum.frontend.default_category_color');
        $this->color_dark_mode = config('forum.frontend.default_category_color');

        if (isset($this->parent_id)) {
            $this->parent_category = $this->parent_id;
        }

        if ($request->user() !== null) {
            UserCreatingCategory::dispatch($request->user());
        }
    }

    public function create(Request $request)
    {
        if (!CategoryAuthorization::create($request->user())) {
            abort(403);
        }

        $validated = $this->validate(CategoryRules::create());

        $action = new Action($validated['title'], $validated['description'], $validated['color_light_mode'], $validated['color_dark_mode'], $validated['accepts_threads'], $validated['is_private']);
        $category = $action->execute();

        if ($validated['parent_category'] > 0) {
            $parent = Category::find($validated['parent_category']);
            $parent->appendNode($category);
        }

        UserCreatedCategory::dispatch($request->user(), $category);

        return $this->redirect($category->route);
    }

    public function render(): View
    {
        return ViewFactory::make('forum::pages.category.create')
            ->layout('forum::layouts.main');
    }
}
