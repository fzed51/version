<?php

namespace Version\Cli;

use PHPUnit\Framework\TestCase;

/**
 * test de SemVerTest
 */
class SemVerTest extends TestCase
{
    /**
     * test de __construct
     */
    public function testConstruct(): void
    {
        $cwd = getcwd();
        chdir(__DIR__."/../../fileVersion");
        $cli = new SemVer();
        $cli->init(true);
        self::assertEquals('version courante : v0.0.1', $cli->get());
        chdir($cwd);

        $cli = new SemVer(__DIR__."/../../fileVersion/versionCustom.json");
        self::assertEquals('version courante : v2.0.0', $cli->get());
    }

    /**
     * Test de l'initialisation d'un fichier de version
     */
    public function testInit(): void
    {
        $cwd = getcwd();
        chdir(__DIR__."/../../fileVersion/forInit");
        $cli = new SemVer();
        if (is_file('./version.json')) {
            unlink('./version.json');
        }
        self::assertEquals(
            'Le fichier de version ./version.json a été créé avec la version : v0.0.1',
            $cli->init()
        );
        self::assertTrue(is_file('./version.json'));
        unlink('./version.json');
        chdir($cwd);
    }

    /**
     *  Test de l'initialisation d'un fichier de version quand le fichier existe déjà
     *  -> test du message d'erreur
     */
    public function testInitWithFileExist()
    {
        $this->expectException(CliMessageException::class);
        $this->expectErrorMessage("./version.json existe déjà, pour forcer l'initialisation, utiliser le flag --force");
        $cwd = getcwd();
        chdir(__DIR__."/../../fileVersion/forInit");
        $cli = new SemVer();
        if (!is_file('./version.json')) {
            touch('./version.json');
        }
        $cli->init();
        unlink('./version.json');
        chdir($cwd);
    }

    /**
     *  Test de l'initialisation d'un fichier de version quand le fichier existe déjà
     *  -> test du flag "force"
     */
    public function testForceInitWithFileExist()
    {

        $cwd = getcwd();
        chdir(__DIR__."/../../fileVersion/forInit");
        $cli = new SemVer();
        if (!is_file('./version.json')) {
            touch('./version.json');
        }
        self::assertEquals(
            'Le fichier de version ./version.json a été créé avec la version : v0.0.1',
            $cli->init(true)
        );
        self::assertTrue(is_file('./version.json'));
        unlink('./version.json');
        chdir($cwd);
    }

    /**
     * test l'increment de version
     */
    public function testIncrement(): void
    {
        $cwd = getcwd();
        chdir(__DIR__."/../../fileVersion");
        $cli = new SemVer();
        $cli->init(true);
        self::assertEquals("ancienne version : v0.0.1\r\nnouvelle version : v0.0.2", $cli->increment('patch'));
        self::assertEquals('version courante : v0.0.2', $cli->get());
        self::assertEquals("ancienne version : v0.0.2\r\nnouvelle version : v0.1.0", $cli->increment('minor'));
        self::assertEquals('version courante : v0.1.0', $cli->get());
        self::assertEquals("ancienne version : v0.1.0\r\nnouvelle version : v1.0.0", $cli->increment('major'));
        self::assertEquals('version courante : v1.0.0', $cli->get());
        chdir($cwd);
    }
}
