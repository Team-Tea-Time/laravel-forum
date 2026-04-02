<?php

namespace TeamTeaTime\Forum\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if ($this->forceDeleting) {
            $this->forceDeleting = !config('forum.preferences.soft_deletes');
        }
    }

    public static function getTableName()
    {
        return (new static)->getTable();
    }

    public function updatedSince(Model &$model): bool
    {
        return $this->updated_at > $model->updated_at;
    }

    public function hasBeenUpdated(): bool
    {
        return $this->updated_at > $this->created_at;
    }
}
