<?php

namespace Riari\Forum\Http\Requests;

class BulkUpdateThreadsRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return ['action' => 'in:move,restore,pin,unpin,lock,unlock'];
    }
}
