<?php

namespace Riari\Forum\Http\Requests;

class CreatePostRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->buildRules('post');
    }
}
