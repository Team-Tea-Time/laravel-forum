<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation rules
    |--------------------------------------------------------------------------
    |
    | Here we define validation rules for all of the fields in the package.
    | Note that the 'required' rule is automatically enforced where applicable.
    |
    */

    'rules' => [
        'author_id'         => ['integer'],
        'enable_threads'    => ['boolean'],
        'category_id'       => ['integer'],
        'content'           => ['min:5'],
        'locked'            => ['boolean'],
        'pinned'            => ['boolean'],
        'private'           => ['boolean'],
        'description'       => ['string'],
        'thread_id'         => ['integer', 'exists:forum_threads,id'],
        'title'             => ['string', 'min:5'],
        'weight'            => ['integer'],
    ]

];
