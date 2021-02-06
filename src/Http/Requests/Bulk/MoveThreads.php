<?php

namespace TeamTeaTime\Forum\Http\Requests\Bulk;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use TeamTeaTime\Forum\Actions\MoveThreads as Action;
use TeamTeaTime\Forum\Events\UserBulkMovedThreads;
use TeamTeaTime\Forum\Http\Requests\Traits\AuthorizesAfterValidation;
use TeamTeaTime\Forum\Interfaces\FulfillableRequest;
use TeamTeaTime\Forum\Models\BaseModel;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;

class MoveThreads extends FormRequest implements FulfillableRequest
{
    use AuthorizesAfterValidation;

    private Collection $sourceCategories;
    private Category $destinationCategory;

    public function rules(): array
    {
        return [
            'threads' => ['required', 'array'],
            'category_id' => ['required', 'int', 'exists:forum_categories,id']
        ];
    }

    public function authorizeValidated(): bool
    {
        if (! $this->user()->can('moveThreadsTo', $this->getDestinationCategory())) return false;

        foreach ($this->getSourceCategories() as $category)
        {
            if (! $this->user()->can('moveThreadsFrom', $category)) return false;
        }

        return true;
    }

    public function fulfill()
    {

      $threads=new Collection;

      foreach($this->validated()['threads'] as $thread_id)
      {
        $thread=Thread::where('id',$thread_id)->first();
        $action = new Action(
          $thread,
          $this->getDestinationCategory(),
          $this->user()->can('viewTrashedThreads')
        );
        $threads->add($action->execute());
      }

      if (! is_null($threads))
      {
        event(new UserBulkMovedThreads($this->user(), $threads, $this->getSourceCategories(), $this->getDestinationCategory()));
      }

      return $threads;
    }

    private function getSourceCategories()
    {

      $categories=new Collection;
      foreach($this->validated()['threads'] as $thread_id)
      {
        $thread=Thread::where('id',$thread_id)->first();
        $categories->add($thread->category);
      }
      $this->sourceCategories = $categories;
      return $this->sourceCategories;
    }

    private function getDestinationCategory()
    {
        $this->destinationCategory = Category::find($this->validated()['category_id']);
        return $this->destinationCategory;
    }
}
