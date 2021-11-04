<?php
declare(strict_types=1);

namespace Version;

use Test\TestCase;

/**
 * test de SemVerTest
 */
class SemVerTest extends TestCase
{
    public function testCreateRead(): void
    {
        $version = new \Version\SemVer(1, 0, 0);
        self::assertEquals('v1.0.0', ''.$version);
        self::assertEquals(1, $version->major());
    }
}
