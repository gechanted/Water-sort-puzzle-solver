<?php
require_once __DIR__ . '/Color.php';

class Tube
{
    private int $nr;
    private int $height;
    /** @var Color[] */
    private array $content = [];

    public function __construct(int $nr = 0, int $height = 4)
    {
        $this->nr = $nr;
        $this->height = $height;
    }

    public function addColor(Color $color)
    {
        $this->content[] = $color;
    }


    public function getExtractable(): array
    {
        $returnArr = [];
        $color = null;
        for ($i = $this->height; $i >= 0; $i--) {
            if (array_key_exists($i, $this->content)) {
                $loopColor = $this->content[$i];
                if ($color === null) {
                    $color = $loopColor;
                } elseif ($color->getColorNr() !== $loopColor->getColorNr()) {
                    break;
                }
                $returnArr[] = $loopColor;
            }
        }

        return $returnArr;
    }

    public function doExtract(): void
    {
        $keysToUnset = [];
        $color = null;
        for ($i = $this->height; $i >= 0; $i--) {
            if (array_key_exists($i, $this->content)) {
                $loopColor = $this->content[$i];
                if ($color === null) {
                    $color = $loopColor;
                } elseif ($color->getColorNr() !== $loopColor->getColorNr()) {
                    break;
                }
                $keysToUnset[] = $i;
            }
        }

        foreach ($keysToUnset as $key) {
            unset($this->content[$key]);
        }

    }

    /**
     * @param Color[] $package
     * @return bool
     */
    public function canReceive(array $package): bool
    {
        if (count($this->content) + count($package) > $this->height) {
            return false;
        }
        $thisTopColor = null;
        for ($i = $this->height; $i >= 0; $i--) {
            if (array_key_exists($i, $this->content)) {
                $thisTopColor = $this->content[$i];
            }
        }

        $theirColor = null;
        foreach ($package as $tc) { $theirColor = $tc; break; }

        if ($thisTopColor === null) { return true; }
        if ($theirColor === null) { return false; }

        return $thisTopColor->getColorNr() === $theirColor->getColorNr();
    }

    /**
     * @param Color[] $package
     */
    public function doReceive(array $package): void
    {
        $this->content = array_merge($this->content, $package);
    }

    public function hash(): string
    {
        $hash = $this->height;
        foreach ($this->content as $color) {
            $hash .= $color->getColorNr();
        }
        return $hash;
    }

    public function getNr(): int
    {
        return $this->nr;
    }

    public function isSolved(): bool
    {
        $comparisonColor = null;
        foreach ($this->content as $color) {
            if ($comparisonColor === null) {
                $comparisonColor = $color;
            } elseif ($comparisonColor->getColorNr() !== $color->getColorNr()) {
                return false;
            }
        }
        $count = count($this->content);
        return $count === $this->height || $count === 0;
    }

    /**
     * @return Color[]
     */
    public function getContent(): array
    {
        return $this->content;
    }

    public function getHeight(): int
    {
        return $this->height;
    }
}