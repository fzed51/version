<?php
declare(strict_types=1);

namespace Version;

use JsonSerializable;
use RuntimeException;

/**
 * class pour les versions respectant SemVer 2.0
 */
class SemVer implements JsonSerializable
{
    private int $major;
    private int $minor;
    private int $patch;
    private ?string $preRelease;
    private ?string $metaBuild;

    /**
     * @param int $major
     * @param int $minor
     * @param int $patch
     * @param string|null $preRelease
     * @param string|null $metaBuild
     */
    public function __construct(int     $major, int $minor, int $patch, ?string $preRelease = null,
                                ?string $metaBuild = null
    ) {
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
        $this->preRelease = $preRelease;
        $this->metaBuild = $metaBuild;
    }

    /**
     * création d'une SemVer à partir d'une chaine de caractère
     * @param string $version
     * @return \Version\SemVer
     */
    public static function fromString(string $version): self
    {
        if (preg_match("/v?(?'maj'\d+)\.(?'min'\d+)\.(?'pat'\d+)(-(?'pre'[\w\-\.]+))?(\+(?'met'[\w\-]+))?/", $version, $matches) === 1) {
            $major = (int)$matches['maj'];
            $minor = (int)($matches['min'] ?? 0);
            $patch = (int)($matches['pat'] ?? 0);
            $preRelease = null;
            if (($matches['pre'] ?? null) !== null && $matches['pre'] !== '') {
                $preRelease = $matches['pre'];
            }
            $metaBuild = null;
            if (($matches['met'] ?? null) !== null && $matches['met'] !== '') {
                $metaBuild = $matches['met'];
            }
            return new self($major, $minor, $patch, $preRelease, $metaBuild);
        }
        throw new RuntimeException("$version n'est pas une version valide");
    }

    /**
     * sérialisation sous forme de chaine
     * @return string
     */
    public function __toString()
    {
        $version = sprintf('v%d.%d.%d', $this->major, $this->minor, $this->patch);
        if ($this->preRelease !== null) {
            $version .= "-{$this->preRelease}";
        }
        if ($this->metaBuild !== null) {
            $version .= "+{$this->metaBuild}";
        }
        return $version;
    }

    /**
     * sérialisation en json
     * @return array<string,mixed>
     */
    public function jsonSerialize()
    {
        $version = [
            'major' => $this->major,
            'minor' => $this->minor,
            'patch' => $this->patch,
        ];
        if (null !== $this->preRelease) {
            $version['pre-release'] = $this->preRelease;
        }
        if (null !== $this->metaBuild) {
            $version['meta-build'] = $this->metaBuild;
        }
        return $version;
    }

    /**
     * @return int
     */
    public function major(): int
    {
        return $this->major;
    }

    /**
     * @return int
     */
    public function minor(): int
    {
        return $this->minor;
    }

    /**
     * @return int
     */
    public function patch(): int
    {
        return $this->patch;
    }

    /**
     * @return string|null
     */
    public function PreRelease(): ?string
    {
        return $this->preRelease;
    }

    /**
     * @return string|null
     */
    public function metaBuild(): ?string
    {
        return $this->metaBuild;
    }

    /**
     * update le patch
     */
    public function nextPatch(): void
    {
        $this->patch++;
    }

    /**
     * update la version mineure
     */
    public function nextMinor(): void
    {
        $this->patch = 0;
        $this->minor++;
    }

    /**
     * update la version majeure
     */
    public function nextMajor(): void
    {
        $this->patch = 0;
        $this->minor = 0;
        $this->major++;
    }

    /**
     * setter pour la version
     * @param int|null $major
     * @param int|null $minor
     * @param int|null $patch
     */
    public function set(?int $major, ?int $minor, ?int $patch): void
    {
        $this->patch = $patch ?? $this->patch;
        $this->minor = $minor ?? $this->minor;
        $this->major = $major ?? $this->major;
    }

    /**
     * setter pour la pre-release
     * @param string|null $preRelease
     */
    public function setPreRelease(?string $preRelease = null): void
    {
        $this->preRelease = $preRelease;
    }

    /**
     * setter pour les meta de build
     * @param string|null $metaBuild
     */
    public function setMetaBuild(?string $metaBuild = null): void
    {
        $this->metaBuild = $metaBuild;
    }

    private function comparePreRelease(?string $pre1 = '', ?string $pre2 = ''): int {
        if($pre1 === $pre2){
            return 0;
        }
        $pre1Elements = explode('.', $pre1 ?? '');
        $pre2Elements = explode('.', $pre2 ?? '');
        foreach ($pre1Elements as $idx => $pre1Element){
            $pre2Element = $pre2Elements[$idx] ?? '';
            if($pre1Element !== $pre2Element && $pre1Element === ''){
                return 1;
            }
            if($pre1Element !== $pre2Element && $pre2Element === ''){
                return -1;
            }
            if($pre1Element > $pre2Element){
                return 1;
            }
            if($pre1Element > $pre2Element){
                return -1;
            }
        }
        if(count($pre1Elements) < count($pre2Elements)){
            return 1;
        }
        return 0;
    }

    /**
     * @param \Version\SemVer $version
     * @return bool
     */
    public function gt(SemVer $version): bool{
        return (
            $this->major > $version->major
            || ($this->major === $version->major
                && $this->minor > $version->minor)
            || ($this->major === $version->major
                && $this->minor === $version->minor
                && $this->patch > $version->patch)
            || ($this->major === $version->major
                && $this->minor === $version->minor
                && $this->patch === $version->patch
                && $this->comparePreRelease($this->preRelease , $version->preRelease) > 0)
        );
    }
    /**
     * @param \Version\SemVer $version
     * @return bool
     */
    public function ge(SemVer $version): bool{
        return !$this->lt($version);
    }
    /**
     * @param \Version\SemVer $version
     * @return bool
     */
    public function eq(SemVer $version): bool{
        return (
            $this->major === $version->major
            && $this->minor === $version->minor
            && $this->patch === $version->patch
            && $this->comparePreRelease($this->preRelease , $version->preRelease) === 0
        );
    }
    /**
     * @param \Version\SemVer $version
     * @return bool
     */
    public function ne(SemVer $version): bool{
        return !$this->eq($version);
    }
    /**
     * @param \Version\SemVer $version
     * @return bool
     */
    public function le(SemVer $version): bool{
        return !$this->gt($version);
    }
    /**
     * @param \Version\SemVer $version
     * @return bool
     */
    public function lt(SemVer $version): bool{
        return $this->major < $version->major
        || ($this->major === $version->major
            && $this->minor < $version->minor)
        || ($this->major === $version->major
            && $this->minor === $version->minor
            && $this->patch < $version->patch)
        || ($this->major === $version->major
            && $this->minor === $version->minor
            && $this->patch === $version->patch
            && $this->comparePreRelease($this->preRelease , $version->preRelease) < 0);
    }
}
