<?php

declare(strict_types=1);

namespace Cgehuzi\Core\Filament;

use Cgehuzi\Core\Filament\Resources\Pages\PageResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

/**
 * Filament-плагин ядра. Регистрирует ресурсы пакета на панели проекта.
 *
 * Подключение в проекте (app/Providers/Filament/AdminPanelProvider.php):
 *   ->plugin(\Cgehuzi\Core\Filament\CorePlugin::make())
 */
class CorePlugin implements Plugin
{
    public function getId(): string
    {
        return 'cgehuzi-core';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            PageResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }
}
