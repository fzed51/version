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
        if (!is_file($this->versionFile)) {
            throw new CliMessageException(
                "$this->versionFile n'est pas un fichier valide, veuillez utiliser le flag --init"
            );
        }
        try {
            $currentVersionJson = file_get_contents(realpath($this->versionFile));
            $version = \Version\SemVer::fromJson($currentVersionJson);
            return "version courante : " . (string) $version;
        } catch (Exception $e) {
            throw new CliMessageException("Le fichier de version n'est pas un fichier de version valide");
        }
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
        file_put_contents($this->versionFile, json_encode($version, JSON_PRETTY_PRINT));
        return "Le fichier de version {$this->versionFile} a été créé avec la version : $version";
    }

    /**
     * @param string $level
     * @return string
     */
    public function increment(string $level)
    {
        if (!is_file($this->versionFile)) {
            throw new CliMessageException(
                "$this->versionFile n'est pas un fichier valide, veuillez utiliser le flag --init"
            );
        }
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
        file_put_contents($this->versionFile, json_encode($version, JSON_PRETTY_PRINT));
        return $out;
    }


}
