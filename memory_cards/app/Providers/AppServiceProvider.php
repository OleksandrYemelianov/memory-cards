<?php

namespace App\Providers;

use App\Repositories\Contracts\MemoryCardRepositoryInterface;
use App\Repositories\Contracts\GroupRepositoryInterface;
use App\Repositories\Contracts\LangRepositoryInterface;
use App\Repositories\Eloquent\EloquentMemoryCardRepository;
use App\Repositories\Eloquent\EloquentGroupRepository;
use App\Repositories\Eloquent\EloquentLangRepository;
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

        $this->app->bind(
            MemoryCardRepositoryInterface::class,
            EloquentMemoryCardRepository::class
        );

        $this->app->bind(
            GroupRepositoryInterface::class,
            EloquentGroupRepository::class
        );
        
        $this->app->bind(
            LangRepositoryInterface::class,
            EloquentLangRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
