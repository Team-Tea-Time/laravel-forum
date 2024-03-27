<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\MoveThread as Action,
    Events\UserMovedThread,
    Http\Requests\Traits\AuthorizesAfterValidation,
    Models\Category,
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
        $thread = $this->route('thread');
        $destinationCategory = $this->getDestinationCategory();

        return $this->user()->can('moveThreadsFrom', $thread->category) && $this->user()->can('moveThreadsTo', $destinationCategory);
    }

    public function fulfill()
    {
        $thread = $this->route('thread');
        $sourceCategory = $thread->category;
        $destinationCategory = $this->getDestinationCategory();

        $action = new Action($thread, $destinationCategory);
        $thread = $action->execute();

        if (! $thread === null) {
            UserMovedThread::dispatch($this->user(), $thread, $sourceCategory, $destinationCategory);
        }

        return $thread;
    }

    private function getDestinationCategory(): Category
    {
        if (! isset($this->destinationCategory)) {
            $this->destinationCategory = Category::find($this->input('category_id'));
        }

        return $this->destinationCategory;
    }
}
