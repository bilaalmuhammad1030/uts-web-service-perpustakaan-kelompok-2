<?php

namespace App\Adapters;

use App\Contracts\BookMetadataProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class OpenLibraryAdapter implements BookMetadataProvider
{
    public function cari(string $query): array
    {
        $key = 'metadata:v1:'.hash('sha256', mb_strtolower(trim($query)));
        if ($cached = Cache::get($key)) {
            return [...$cached, 'cached' => true];
        }
        try {
            $response = Http::acceptJson()->withUserAgent(config('services.openlibrary.user_agent'))
                ->withOptions(['verify' => config('services.openlibrary.ca_bundle')])
                ->connectTimeout(2)->timeout(config('services.openlibrary.timeout'))
                ->retry(2, 200, throw: false)
                ->get(config('services.openlibrary.base_url').'/search.json', [
                    'q' => $query, 'limit' => 5, 'fields' => 'key,title,author_name,first_publish_year,isbn',
                ])->throw();
            $docs = $response->json('docs');
            if (! is_array($docs)) {
                throw new \UnexpectedValueException('Format respons metadata tidak sesuai.');
            }
            $result = ['source' => 'openlibrary', 'fallback' => false, 'cached' => false,
                'message' => 'Metadata tambahan; ketersediaan tetap berasal dari database sekolah.',
                'items' => array_map(fn (array $item) => [
                    'judul' => (string) ($item['title'] ?? ''), 'penulis' => implode(', ', $item['author_name'] ?? []),
                    'tahun' => $item['first_publish_year'] ?? null, 'isbn' => $item['isbn'][0] ?? null,
                ], $docs),
            ];
            Cache::put($key, $result, (int) config('services.openlibrary.cache_seconds'));

            return $result;
        } catch (Throwable $e) {
            Log::warning('Metadata Open Library tidak tersedia', ['type' => $e::class]);

            return ['source' => 'local', 'fallback' => true, 'cached' => false, 'items' => [], 'message' => 'Metadata luar sedang tidak tersedia. Gunakan pencarian koleksi lokal; stok tetap dapat dilihat.'];
        }
    }
}
