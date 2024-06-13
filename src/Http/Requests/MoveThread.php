<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\MoveThread as Action,
    Events\UserMovedThread,
    Http\Requests\Traits\AuthorizesAfterValidation,
    Models\Category,
    Support\Authorization\CategoryAuthorization,
    Support\Validation\ThreadRules,
};

class MoveThread extends FormRequest implements FulfillableRequestInterface
{
    use AuthorizesAfterValidation;

    private Category $destinationCategory;

    public function rules(): array
    {
        return ThreadRules::move();
    }

    public function authorizeValidated(): bool
    {
        return CategoryAuthorization::moveThread($this->user(), $this->route('thread')->category, $this->getDestinationCategory());
    }

    public function fulfill()
    {
        $thread = $this->route('thread');
        $sourceCategory = $thread->category;
        $destinationCategory = $this->getDestinationCategory();

        $action = new Action($thread, $destinationCategory);
        $thread = $action->execute();

        UserMovedThread::dispatch($this->user(), $thread, $sourceCategory, $destinationCategory);

        return $thread;
    }

    private function getDestinationCategory(): Category
    {
        if (!isset($this->destinationCategory)) {
            $this->destinationCategory = Category::find($this->input('category_id'));
        }

        return $this->destinationCategory;
    }
}
