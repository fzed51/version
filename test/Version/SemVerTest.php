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

    public function testNextVersion(): void
    {
        $version = new SemVer(1, 1, 0);
        $version->nextPatch();
        self::assertEquals('v1.1.1', (string)$version);
        $version->nextMinor();
        self::assertEquals('v1.2.0', (string)$version);
        $version->nextMajor();
        self::assertEquals('v2.0.0', (string)$version);
    }
}
