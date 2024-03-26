<?php

namespace TeamTeaTime\Forum\Http\Livewire\Types;

use Livewire\Wireable;

class Alert implements Wireable
{
    protected AlertType $type;
    protected string $message;

    public function __construct(AlertType $type, string $message)
    {
        $this->type = $type;
        $this->message = $message;
    }

    public function toLivewire()
    {
        $test = [
            'type' => $this->type->value,
            'message' => $this->message,
        ];

        return $test;
    }

    public static function fromLivewire($value)
    {
        return new static(AlertType::from($value['type']), $value['message']);
    }
}
