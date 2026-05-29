<?php

declare(strict_types=1);

namespace Cgehuzi\Core;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * Точка интеграции пакета с Laravel-приложением проекта (автодискавери через
 * extra.laravel.providers). На этапе каркаса (v0.0.x) регистрирует конфиг,
 * миграции и диагностические роуты. Контент (Page/render/Filament) добавляется
 * по мере переноса референс-среза.
 */
class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/core.php', 'core');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__ . '/../config/core.php' => config_path('core.php'),
        ], 'core-config');

        if (config('core.register_routes', true)) {
            $this->registerRoutes();
        }
    }

    private function registerRoutes(): void
    {
        Route::middleware('api')
            ->prefix('api/core')
            ->group(__DIR__ . '/../routes/api.php');
    }
}
