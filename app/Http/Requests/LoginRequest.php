<?php

namespace App\Http\Requests;

class LoginRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'username' => ['required_without:email', 'string', 'max:255'],
            'email' => ['required_without:username', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ];
    }
}
