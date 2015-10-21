<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation rules
    |--------------------------------------------------------------------------
    |
    | Here we define validation rules for all of the fields in the package.
    | Note that the 'required' rule is already enforced where applicable.
    |
    */

    'rules' => [
        'author_id'     => ['integer'],
        'category_id'   => ['integer', 'exists:forum_categories,id'],
        'content'       => ['min:5'],
        'locked'        => ['boolean'],
        'pinned'        => ['boolean'],
        'subtitle'      => ['string', 'min:5'],
        'thread_id'     => ['integer', 'exists:forum_threads,id'],
        'title'         => ['string', 'min:5'],
        'weight'        => ['integer']
    ]

];
