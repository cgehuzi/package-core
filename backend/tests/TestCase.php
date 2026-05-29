<?php

declare(strict_types=1);

namespace Cgehuzi\Core\Tests;

use Cgehuzi\Core\CoreServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * Базовый TestCase — обёртка над Orchestra Testbench: поднимает Laravel-приложение,
 * регистрирует CoreServiceProvider и SQLite in-memory.
 */
abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [CoreServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
