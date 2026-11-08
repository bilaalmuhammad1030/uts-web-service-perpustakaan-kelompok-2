<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class KategoriRequest extends ApiRequest
{
    public function rules(): array
    {
        return ['nama' => ['required', 'string', 'max:100', Rule::unique('kategori')->ignore($this->route('kategori')?->id)]];
    }
}
