<?php

declare(strict_types=1);

namespace Cgehuzi\Core\Tests\Feature;

use Cgehuzi\Core\Core;
use Cgehuzi\Core\Tests\TestCase;

class HealthRouteTest extends TestCase
{
    public function test_health_route_is_registered_by_provider(): void
    {
        $this->getJson('/api/core/health')
            ->assertOk()
            ->assertJson([
                'package' => 'cgehuzi/core-backend',
                'version' => Core::VERSION,
                'status' => 'ok',
            ]);
    }
}
