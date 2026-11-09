<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class BukuRequest extends ApiRequest
{
    public function rules(): array
    {
        $rules = [
            'kode_buku' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9-]+$/', Rule::unique('buku')->ignore($this->route('buku')?->id)],
            'kategori_id' => ['required', 'integer', 'exists:kategori,id'],
            'judul' => ['required', 'string', 'max:150'],
            'penulis' => ['required', 'string', 'max:100'],
            'penerbit' => ['required', 'string', 'max:100'],
            'tahun_terbit' => ['required', 'integer', 'min:1901', 'max:'.min(2155, (int) now()->format('Y'))],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
            'gambar_url' => ['nullable', 'string', 'max:2048'],
            'isbn' => ['nullable', 'regex:/^(?:[0-9]{13}|[0-9]{9}[0-9X])$/'],
            'lokasi_rak' => ['required', 'string', 'max:50'],
        ];
        if ($this->isMethod('post')) {
            $rules['jumlah_total'] = ['required', 'integer', 'min:0', 'max:1000000'];
            $rules['jumlah_tersedia'] = ['required', 'integer', 'min:0', 'lte:jumlah_total'];
        } else {
            foreach (['jumlah_total', 'jumlah_tersedia', 'versi_stok'] as $field) {
                $rules[$field] = ['prohibited'];
            }
        }

        return $rules;
    }
}
