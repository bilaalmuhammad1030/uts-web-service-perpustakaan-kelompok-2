<?php

namespace App\Http\Requests;

class MetadataRequest extends ApiRequest
{
    public function rules(): array
    {
        return ['q' => ['required', 'string', 'min:2', 'max:200']];
    }
}
