<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\Bulk\MoveThreads as Action,
    Events\UserBulkMovedThreads,
    Http\Requests\Traits\AuthorizesAfterValidation,
    Http\Requests\FulfillableRequestInterface,
    Models\BaseModel,
    Models\Category,
    Models\Thread,
    Support\CategoryAccess,
    Support\Validation\ThreadRules,
};

class MoveThreads extends FormRequest implements FulfillableRequestInterface
{
    use AuthorizesAfterValidation;

    private ?Collection $sourceCategories = null;
    private ?Category $destinationCategory = null;

    public function rules(): array
    {
        return ThreadRules::bulkMove();
    }

    public function authorizeValidated(): bool
    {
        $destinationCategory = $this->getDestinationCategory();

        $accessibleCategoryIds = CategoryAccess::getFilteredIdsFor($this->user());

        if (!($accessibleCategoryIds->contains($destinationCategory->id) || $this->user()->can('moveThreadsTo', $destinationCategory))) {
            return false;
        }

        foreach ($this->getSourceCategories() as $category) {
            if (!($accessibleCategoryIds->contains($category->id) || $this->user()->can('moveThreadsFrom', $category))) {
                return false;
            }
        }

        return true;
    }

    public function fulfill()
    {
        $action = new Action(
            $this->validated()['threads'],
            $this->getDestinationCategory(),
            $this->user()->can('viewTrashedThreads')
        );
        $threads = $action->execute();

        if ($threads !== null) {
            UserBulkMovedThreads::dispatch($this->user(), $threads, $this->getSourceCategories(), $this->getDestinationCategory());
        }

        return $threads;
    }

    private function getSourceCategories()
    {
        if (!$this->sourceCategories) {
            $query = Thread::select('category_id')
                ->distinct()
                ->where('category_id', '!=', $this->validated()['category_id'])
                ->whereIn('id', $this->validated()['threads']);

            if (!$this->user()->can('viewTrashedThreads')) {
                $query = $query->whereNull(BaseModel::DELETED_AT);
            }

            $this->sourceCategories = Category::whereIn('id', $query->get()->pluck('category_id'))->get();
        }

        return $this->sourceCategories;
    }

    private function getDestinationCategory()
    {
        if ($this->destinationCategory == null) {
            $this->destinationCategory = Category::find($this->validated()['category_id']);
        }

        return $this->destinationCategory;
    }
}
