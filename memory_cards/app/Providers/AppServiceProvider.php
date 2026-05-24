<?php

namespace App\Providers;

use App\Services\DeeplTranslateService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app
        ->when(DeeplTranslateService::class)
        ->needs('$apiKey')
        ->give(fn() => config('translate.api_key'));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
