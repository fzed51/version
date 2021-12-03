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

    public function testCompare(): void
    {
        $version1 = new SemVer(1, 1, 0);
        $version2 = new SemVer(11, 0, 0);
        self::assertTrue($version2->gt($version1), "$version2 > $version1");
        self::assertTrue($version2->ge($version1), "$version2 >= $version1");
        self::assertFalse($version2->eq($version1), "not $version2 = $version1");
        self::assertFalse($version2->le($version1), "not $version2 <= $version1");
        self::assertFalse($version2->lt($version1), "not $version2 < $version1");
        self::assertTrue($version2->ne($version1), "$version2 != $version1");
        self::assertFalse($version1->gt($version2), "not $version1 > $version1");
        self::assertFalse($version1->ge($version2), "not $version1 > $version1");
        self::assertFalse($version1->eq($version2), "not $version1 > $version1");
        self::assertTrue($version1->le($version2), "$version1 <= $version2");
        self::assertTrue($version1->lt($version2), "$version1 < $version2");
        self::assertTrue($version1->ne($version2), "$version1 != $version2");
        self::assertFalse($version1->ne($version1), "not $version1 != $version1");
        self::assertTrue($version1->ge($version1), "$version1 >= $version1");
        self::assertTrue($version1->le($version1), "$version1 <= $version1");
        $version3 = new SemVer(1, 1, 0, 'rc1');
        self::assertTrue($version1->gt($version3), "$version1 > $version3");
        $version4 = new SemVer(1, 1, 0, 'rc2');
        self::assertTrue($version4->gt($version3), "$version2 > $version4");
    }

    public function testCompareWithPreRelease(): void
    {
        $version1 = new SemVer(1, 0, 0);
        $version2 = new SemVer(1, 0, 0, 'alpha');
        $version3 = new SemVer(1, 0, 0, 'RC');
        $version4 = new SemVer(1, 0, 0, 'RC.1');
        $version5 = new SemVer(1, 0, 0, 'RC.2');
        self::assertTrue($version1->gt($version2), "$version1 > $version2");
        self::assertTrue($version1->gt($version3), "$version1 > $version3");
        self::assertTrue($version1->gt($version4), "$version1 > $version4");
        self::assertTrue($version1->gt($version5), "$version1 > $version5");
        self::assertTrue($version2->lt($version3), "$version2 < $version3");
        self::assertTrue($version2->lt($version4), "$version2 < $version4");
        self::assertTrue($version2->lt($version5), "$version2 < $version5");
        self::assertTrue($version3->gt($version4), "$version2 > $version4");
        self::assertTrue($version3->gt($version5), "$version2 > $version5");
        self::assertTrue($version4->lt($version5), "$version4 < $version5");
    }

    public function testJson(): void
    {
        $version = new SemVer(1, 0, 0, 'rc1', '01012001');
        /** @noinspection JsonEncodingApiUsageInspection */
        $json = json_encode($version);
        self::assertEquals(JSON_ERROR_NONE, json_last_error());
        self::assertNotFalse($json);
        $version2 = SemVer::fromJson($json);
        self::assertEquals('v1.0.0-rc1+01012001', (string)$version2);
    }
}
