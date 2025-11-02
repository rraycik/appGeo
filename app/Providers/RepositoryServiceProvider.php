<?php

namespace App\Providers;

use App\Repositories\LayerRepository;
use App\Repositories\LayerRepositoryInterface;
use Illuminate\Support\ServiceProvider;

/**
 * Service Provider para bindings de repositórios
 * Seguindo o princípio Dependency Inversion (SOLID)
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            LayerRepositoryInterface::class,
            LayerRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
