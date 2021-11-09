<?php
declare(strict_types=1);

namespace Version;

use JsonSerializable;

/**
 * class pour les versions respectant SemVer 2.0
 */
class SemVer implements JsonSerializable
{
    private int $major;
    private int $minor;
    private int $patch;
    private ?string $realease;

    /**
     * @param int $major
     * @param int $minor
     * @param int $patch
     * @param string|null $realease
     */
    public function __construct(int $major, int $minor, int $patch, ?string $realease = null)
    {
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
        $this->realease = $realease;
    }

    /**
     * sérialisation sous forme de chaine
     * @return string
     */
    public function __toString()
    {
        $version = sprintf('v%d.%d.%d', $this->major, $this->minor, $this->patch);
        if ($this->realease !== null) {
            $version .= "-{$this->realease}";
        }
        return $version;
    }

    /**
     * sérialisation en json
     * @return array<string,mixed>
     */
    public function jsonSerialize()
    {
        return [
            'major' => $this->major,
            'minor' => $this->minor,
            'patch' => $this->patch,
            'release' => $this->realease
        ];
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
    public function realease(): ?string
    {
        return $this->realease;
    }
}
