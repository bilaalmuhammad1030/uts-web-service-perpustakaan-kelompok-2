<?php

namespace Tests\Feature;

use App\Adapters\GoogleBooksAdapter;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoogleBooksMetadataTest extends TestCase
{
    public function test_google_books_metadata_sukses_dan_cache(): void
    {
        Cache::flush();
        Config::set('services.google_books.key', 'AIzaSyTestKey');
        Http::preventStrayRequests();

        Http::fake([
            'www.googleapis.com/books/v1/volumes*' => Http::response([
                'kind' => 'books#volumes',
                'totalItems' => 1,
                'items' => [
                    [
                        'id' => 'test-id-123',
                        'volumeInfo' => [
                            'title' => 'Laskar Pelangi',
                            'authors' => ['Andrea Hirata'],
                            'publisher' => 'Bentang Pustaka',
                            'publishedDate' => '2005',
                            'industryIdentifiers' => [
                                ['type' => 'ISBN_13', 'identifier' => '9789793062792'],
                            ],
                            'imageLinks' => [
                                'thumbnail' => 'https://books.google.com/test-thumb.jpg',
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $adapter = new GoogleBooksAdapter;
        $result = $adapter->cari('Laskar Pelangi');

        $this->assertSame('google_books', $result['source']);
        $this->assertFalse($result['fallback']);
        $this->assertFalse($result['cached']);
        $this->assertCount(1, $result['items']);
        $this->assertSame('Laskar Pelangi', $result['items'][0]['judul']);
        $this->assertSame('Andrea Hirata', $result['items'][0]['penulis']);
        $this->assertSame('Bentang Pustaka', $result['items'][0]['penerbit']);
        $this->assertSame(2005, $result['items'][0]['tahun']);
        $this->assertSame('9789793062792', $result['items'][0]['isbn']);
        $this->assertSame('https://books.google.com/test-thumb.jpg', $result['items'][0]['gambar_url']);

        // Uji caching
        $cachedResult = $adapter->cari('Laskar Pelangi');
        $this->assertTrue($cachedResult['cached']);

        Http::assertSentCount(1);
    }

    public function test_google_books_fallback_pada_koneksi_error(): void
    {
        Cache::flush();
        Config::set('services.google_books.key', 'AIzaSyTestKey');

        Http::fake(function () {
            throw new ConnectionException('Timeout connecting to Google');
        });

        $adapter = new GoogleBooksAdapter;
        $result = $adapter->cari('Buku Rusak');

        $this->assertSame('local', $result['source']);
        $this->assertTrue($result['fallback']);
        $this->assertEmpty($result['items']);
    }
}
