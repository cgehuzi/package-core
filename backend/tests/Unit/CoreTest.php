<?php

declare(strict_types=1);

namespace Cgehuzi\Core\Tests\Unit;

use Cgehuzi\Core\Core;
use PHPUnit\Framework\TestCase;

class CoreTest extends TestCase
{
    public function test_version_is_semver(): void
    {
        $this->assertMatchesRegularExpression('/^\d+\.\d+\.\d+$/', Core::VERSION);
    }
}
