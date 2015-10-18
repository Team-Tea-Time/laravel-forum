<?php

namespace Riari\Forum\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

abstract class BaseRequest extends FormRequest
{
    /**
     * @var array
     */
    protected $rules;

    /**
     * Create a new request instance.
     */
    public function __construct()
    {
        $this->rules = config('forum.validation.rules');
    }

    /**
     * Build up the request validation rules.
     *
     * @param  array  $sets
     * @return array
     */
    public function buildRules($sets)
    {
        $sets = (array) $sets;

        switch ($this->getMethod()) {
            case 'POST':
                $type = 'create';
                break;
            case 'PUT':
            case 'PATCH':
                $type = 'update';
                break;
        }

        $rulesets = config('forum.validation.rules');

        $rules = [];
        foreach ($sets as $set) {
            $rules += $rulesets[$type][$set];
        }

        return $rules + $rulesets['base'];
    }

    /**
     * Authorize requests by default.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
