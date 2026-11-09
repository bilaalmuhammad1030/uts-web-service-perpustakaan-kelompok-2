<?php

namespace App\Adapters;

use App\Contracts\BookMetadataProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GoogleBooksAdapter implements BookMetadataProvider
{
    public function cari(string $query): array
    {
        $cleanQuery = trim($query);
        if ($cleanQuery === '') {
            return [
                'source' => 'google_books',
                'fallback' => false,
                'cached' => false,
                'message' => 'Kata kunci pencarian kosong.',
                'items' => [],
            ];
        }

        $key = 'metadata:v1:google_books:'.hash('sha256', mb_strtolower($cleanQuery));
        if ($cached = Cache::get($key)) {
            Log::info('Metadata Google Books dimuat dari cache', ['query' => $cleanQuery]);

            return [
                ...$cached,
                'cached' => true,
                'message' => 'Metadata resmi disinkronkan dari Google Books API, sementara ketersediaan koleksi fisik mengacu langsung pada database perpustakaan (dimuat dari cache).',
            ];
        }

        try {
            $apiKey = config('services.google_books.key');
            $baseUrl = rtrim(config('services.google_books.base_url', 'https://www.googleapis.com/books/v1'), '/');

            $params = [
                'q' => $cleanQuery,
                'maxResults' => 5,
                'printType' => 'books',
            ];

            if (! empty($apiKey)) {
                $params['key'] = $apiKey;
            }

            $response = Http::acceptJson()
                ->withOptions(['verify' => config('services.google_books.ca_bundle')])
                ->connectTimeout(3)
                ->timeout(config('services.google_books.timeout', 6))
                ->retry(2, 200, throw: false)
                ->get($baseUrl.'/volumes', $params)
                ->throw();

            $data = $response->json();
            $items = $data['items'] ?? [];

            $mappedItems = [];
            foreach ($items as $item) {
                $volumeInfo = $item['volumeInfo'] ?? [];

                // Ambil ISBN-13 terlebih dahulu, jika tidak ada ambil identifier pertama
                $isbn = null;
                foreach ($volumeInfo['industryIdentifiers'] ?? [] as $identifier) {
                    if (($identifier['type'] ?? '') === 'ISBN_13') {
                        $isbn = $identifier['identifier'] ?? null;
                        break;
                    }
                    $isbn ??= $identifier['identifier'] ?? null;
                }

                $publishedDate = $volumeInfo['publishedDate'] ?? null;
                $tahun = null;
                if ($publishedDate) {
                    $tahun = (int) substr($publishedDate, 0, 4);
                }

                $mappedItems[] = [
                    'id' => $item['id'] ?? null,
                    'judul' => (string) ($volumeInfo['title'] ?? ''),
                    'penulis' => implode(', ', $volumeInfo['authors'] ?? ['Penulis tidak diketahui']),
                    'penerbit' => $volumeInfo['publisher'] ?? null,
                    'tahun' => $tahun ?: null,
                    'isbn' => $isbn,
                    'deskripsi' => $volumeInfo['description'] ?? null,
                    'gambar_url' => $volumeInfo['imageLinks']['thumbnail'] ?? ($volumeInfo['imageLinks']['smallThumbnail'] ?? null),
                ];
            }

            $result = [
                'source' => 'google_books',
                'fallback' => false,
                'cached' => false,
                'message' => 'Metadata resmi disinkronkan dari Google Books API, sementara ketersediaan koleksi fisik mengacu langsung pada database perpustakaan.',
                'items' => $mappedItems,
            ];

            Cache::put($key, $result, (int) config('services.google_books.cache_seconds', 3600));

            return $result;
        } catch (Throwable $e) {
            Log::warning('Metadata Google Books tidak tersedia', [
                'type' => $e::class,
                'message' => $e->getMessage(),
            ]);

            return [
                'source' => 'local',
                'fallback' => true,
                'cached' => false,
                'items' => [],
                'message' => 'Layanan Google Books sedang tidak dapat dijangkau. Menampilkan data lokal perpustakaan.',
            ];
        }
    }
}
