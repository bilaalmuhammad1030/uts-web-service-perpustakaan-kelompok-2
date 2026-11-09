<?php

namespace App\Providers;

use App\Adapters\GoogleBooksAdapter;
use App\Adapters\OpenLibraryAdapter;
use App\Contracts\BookMetadataProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BookMetadataProvider::class, function () {
            if (config('services.metadata_provider') === 'google_books' || (config('services.metadata_provider') !== 'openlibrary' && config('services.google_books.key'))) {
                return new GoogleBooksAdapter;
            }

            return new OpenLibraryAdapter;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('admin-login', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));
        RateLimiter::for('metadata', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));
    }
}
