<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Pagination\LengthAwarePaginator;

class BookService
{
    public function searchAndPaginateBooks(array $filters): LengthAwarePaginator
    {
        $perPage = $filters['per_page'] ?? 10;
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $order = $filters['order'] ?? 'desc';

        return Book::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                        ->orWhere('penulis', 'like', "%{$search}%")
                        ->orWhere('kode_buku', 'like', "%{$search}%");
                });
            })
            ->when($filters['id_kategori'] ?? null, function ($query, $categoryId) {
                $query->where('id_kategori', $categoryId);
            })
            ->orderBy($sortBy, $order)
            ->paginate($perPage);
    }
}
