<?php

namespace TeamTeaTime\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TeamTeaTime\Forum\{
    Actions\CreateThread as Action,
    Events\UserCreatedThread,
    Support\Authorization\CategoryAuthorization,
    Support\Validation\ThreadRules,
};

class CreateThread extends FormRequest implements FulfillableRequestInterface
{
    public function authorize(): bool
    {
        return CategoryAuthorization::createThreads($this->user(), $this->route('category'));
    }

    public function rules(): array
    {
        return ThreadRules::create();
    }

    public function fulfill()
    {
        $input = $this->validated();
        $category = $this->route('category');

        $action = new Action($category, $this->user(), $input['title'], $input['content']);
        $thread = $action->execute();

        UserCreatedThread::dispatch($this->user(), $thread);

        return $thread;
    }
}
