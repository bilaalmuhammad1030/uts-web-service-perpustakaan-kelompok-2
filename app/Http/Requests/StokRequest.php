<?php

namespace App\Http\Requests;

class StokRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'jumlah_total' => ['required', 'integer', 'min:0', 'max:1000000'],
            'jumlah_tersedia' => ['required', 'integer', 'min:0', 'lte:jumlah_total'],
            'versi_stok' => ['required', 'integer', 'min:1'],
            'alasan' => ['required', 'string', 'min:3', 'max:255'],
        ];
    }
}
