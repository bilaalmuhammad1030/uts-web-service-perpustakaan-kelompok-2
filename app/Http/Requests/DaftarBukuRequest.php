<?php

namespace App\Http\Requests;

class DaftarBukuRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:200'],
            'kategori_id' => ['nullable', 'integer', 'exists:kategori,id'],
            'tersedia' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'in:judul,penulis,jumlah_tersedia,stok_diperbarui_at'],
            'direction' => ['nullable', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
