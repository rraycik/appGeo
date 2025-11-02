<?php

namespace App\Providers;

use App\Repositories\LayerRepositoryInterface;
use App\Services\LayerService;
use App\Validators\GeoJsonValidator;
use Illuminate\Support\ServiceProvider;

/**
 * Service Provider para Services
 * Injeção de dependências seguindo Dependency Inversion (SOLID)
 */
class ServiceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(LayerService::class, function ($app) {
            return new LayerService(
                $app->make(LayerRepositoryInterface::class),
                new GeoJsonValidator()
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
