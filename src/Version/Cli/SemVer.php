<?php

namespace Version\Cli;

use Exception;

class SemVer
{
    /** @var string nom du fichier de version */
    private string $versionFile;

    /**
     * @param string $versionFile
     */
    public function __construct(string $versionFile = './version.json')
    {

        $this->versionFile = $versionFile;
    }



    /**
     * @return string
     */
    public function get(): string
    {
        $this->testFileVersion();
            $version = $this->getVersionFromFileVersion();
            return "version courante : " . (string) $version;
    }

    /**
     * @param bool $force force l'initialisation d'un nouveau fichier de version
     * @return string
     */
    public function init($force = false): string
    {
        if (is_file($this->versionFile) && !$force) {
            throw new CliMessageException(
                "$this->versionFile existe déjà, pour forcer l'initialisation, utiliser le flag --force"
            );
        }
        $version = new \Version\SemVer(0, 0, 1);
        $this->setVersionToFileVersion($version);
        return "Le fichier de version {$this->versionFile} a été créé avec la version : $version";
    }

    /**
     * @param string $level
     * @return string
     */
    public function increment(string $level): string
    {
        $this->testFileVersion();
        $currentVersionJson = file_get_contents(realpath($this->versionFile));
        $version = \Version\SemVer::fromJson($currentVersionJson);
        $out = "ancienne version : " . (string) $version . PHP_EOL;
        switch ($level) {
            case 'major':
                $version->nextMajor();
                break;
            case 'minor':
                $version->nextMinor();
                break;
            case 'patch':
                $version->nextPatch();
                break;
            default:
                throw new CliMessageException(
                    "la version $level ne peut être incrementé, utiliser le therme major|minor|`patch"
                );
        }
        $out.= "nouvelle version : " . (string) $version;
        $this->setVersionToFileVersion($version);
        return $out;
    }

    /**
     * Test la présence du fileVersion
     */
    protected function testFileVersion(): void
    {
        if (!is_file($this->versionFile)) {
            throw new CliMessageException(
                "$this->versionFile n'est pas un fichier valide, veuillez utiliser le flag --init"
            );
        }
    }

    /**
     * @return \Version\SemVer
     */
    protected function getVersionFromFileVersion(): \Version\SemVer
    {
        try {
            $currentVersionJson = file_get_contents(realpath($this->versionFile));
            return \Version\SemVer::fromJson($currentVersionJson);
        } catch (Exception $e) {
            throw new CliMessageException("Le fichier de version n'est pas un fichier de version valide");
        }
    }

    /**
     * @param \Version\SemVer $version
     */
    protected function setVersionToFileVersion(\Version\SemVer $version): void
    {
        try {
            file_put_contents($this->versionFile, json_encode($version, JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR));
        } catch (Exception $e) {
            throw new CliMessageException("Le fichier de version n'est pas un fichier de version valide");
        }
    }

    /**
     * @param string $preRelease
     * @return string
     */
    public function preRelease(string $preRelease, bool $force = false): string
    {
        $this->testFileVersion();
        $version = $this->getVersionFromFileVersion();
        $out = "ancienne version : " . (string) $version . PHP_EOL;
        $oldVersion = clone $version;
        $version->setPreRelease($preRelease);
        if (!$force && $version->lt($oldVersion)) {
            throw new CliMessageException(
                "Vous allez assigner une version inférieure à la version actuelle. Pour effectuer quand même "
                ."cette opération, utiliser le flag --force"
            );
        }
        $out.= "nouvelle version : " . (string) $version;
        $this->setVersionToFileVersion($version);
        return $out;
    }

    public function set(string $strVersion, bool $force = false): string
    {
        $this->testFileVersion();
        $oldVersion = $this->getVersionFromFileVersion();
        $out = "ancienne version : " . (string) $oldVersion . PHP_EOL;
        $version = \Version\SemVer::fromString($strVersion);
        if (!$force && $version->lt($oldVersion)) {
            throw new CliMessageException(
                "Vous allez assigner une version inférieure à la version actuelle. Pour effectuer quand même "
                ."cette opération, utiliser le flag --force"
            );
        }
        $out.= "nouvelle version : " . (string) $version;
        $this->setVersionToFileVersion($version);
        return $out;
    }

    public function usage(): string
    {
        return <<<USAGE
Usage:
-------

version-manager [--path chemin/version.json] [commande] [option]

* --path -p [chemin] : indique la location du fichier de version
                       ./version.json est pris comme valeur si le path n'est pas utilisé
    
Commandes :
------------

* sans commande        : lit le fichier de version
* --init -i            : initialise le fichier de version (v0.0.1)
* --increment -u [val] : incrément un element de la version 
                         val = major | minor | patch
* --preRelease -p [pr] : modifie l'élément preRelease de la version
* --set -s [ver]       : modifie le numero de version 

Options :
----------

* --force -f : force la commande (init, preRelease et set)
* --help -h  : affiche ce message
USAGE;
    }
}
