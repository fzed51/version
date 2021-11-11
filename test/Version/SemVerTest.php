<?php
declare(strict_types=1);

namespace Version;

use Test\TestCase;
use function PHPUnit\Framework\assertTrue;

/**
 * test de SemVerTest
 */
class SemVerTest extends TestCase
{
    public function testCreateRead(): void
    {
        $version = new SemVer(1, 0, 0);
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

    public function testSetter():void
    {
        $version = new SemVer(1, 0, 0, 'dev');
        self::assertEquals('v1.0.0-dev', (string)$version);
        $version->set(null, 4, null);
        self::assertEquals('v1.4.0-dev', (string)$version);
        $version->setPreRelease('rc1');
        self::assertEquals('v1.4.0-rc1', (string)$version);
        $version->setPreRelease();
        self::assertEquals('v1.4.0', (string)$version);
        $version->setMetaBuild('DQptZXJjcmVkaSAxMCB');
        self::assertEquals('v1.4.0+DQptZXJjcmVkaSAxMCB', (string)$version);
        $version->setMetaBuild();
        self::assertEquals('v1.4.0', (string)$version);
    }

    public function testCreateFromString(): void
    {
        $version = SemVer::fromString('v2.4.0');
        self::assertEquals('v2.4.0', (string)$version);
        $version = SemVer::fromString('2.4.0');
        self::assertEquals('v2.4.0', (string)$version);
        $version = SemVer::fromString('v2.4.0-alpha');
        self::assertEquals('v2.4.0-alpha', (string)$version);
        $version = SemVer::fromString('v2.4.0-alpha+20211011-0821');
        self::assertEquals('v2.4.0-alpha+20211011-0821', (string)$version);
    }
}
