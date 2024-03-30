<?php

namespace TeamTeaTime\Forum\Http\Livewire\Pages;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use TeamTeaTime\Forum\{
    Actions\EditCategory,
    Events\UserEditingCategory,
    Events\UserEditedCategory,
    Models\Category,
    Support\Access\CategoryAccess,
    Support\Authorization\CategoryAuthorization,
    Support\Validation\CategoryRules,
    Support\Frontend\Forum,
};

class CategoryEdit extends Component
{
    #[Locked]
    public Category $category;

    #[Locked]
    public Collection $categories;

    // Form fields
    public string $title;
    public string $description;
    public string $color_light_mode;
    public string $color_dark_mode;
    public ?int $parent_category = null;
    public bool $accepts_threads = false;
    public bool $is_private = false;

    public function mount(Request $request)
    {
        $categories = CategoryAccess::getFilteredTreeFor($request->user())->toTree();

        // TODO: This is a workaround for a serialisation issue. See: https://github.com/lazychaser/laravel-nestedset/issues/487
        //       Once the issue is fixed, this can be removed.
        $this->categories = CategoryAccess::removeParentRelationships($categories);

        $category = $request->route('category');
        $this->category = $category;
        $this->title = $category->title;
        $this->description = $category->description;
        $this->color_light_mode = $category->color_light_mode;
        $this->color_dark_mode = $category->color_dark_mode;
        $this->parent_category = $category->parent_id;
        $this->accepts_threads = $category->accepts_threads;
        $this->is_private = $category->is_private;

        if ($request->user() !== null) {
            UserEditingCategory::dispatch($request->user(), $category);
        }
    }

    public function save(Request $request)
    {
        if (!CategoryAuthorization::edit($request->user(), $this->category)) {
            abort(403);
        }

        $validated = $this->validate(CategoryRules::create());

        $action = new EditCategory($this->category, $validated['title'], $validated['description'], $validated['color_light_mode'], $validated['color_dark_mode'], $validated['accepts_threads'], $validated['is_private']);
        $action->execute();

        if ($validated['parent_category'] > 0) {
            $parent = Category::find($validated['parent_category']);
            $parent->appendNode($this->category);
        }

        UserEditedCategory::dispatch($request->user(), $this->category);

        return $this->redirect($this->category->route);
    }

    public function delete(Request $request)
    {
        if (!CategoryAuthorization::delete($request->user(), $this->category)) {
            abort(403);
        }

        $this->category->delete();

        return $this->redirect(Forum::route('category.index'));
    }

    public function render(): View
    {
        return ViewFactory::make('forum::pages.category.edit')
            ->layout('forum::layouts.main');
    }
}
