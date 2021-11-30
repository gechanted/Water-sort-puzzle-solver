<?php

class Color
{

    public static array $table = [
        '',
        'blue',
        'black',
        'pink',
        'yellow',
        'brown',
        'darkgreen',
        'lightgreen',
        'lime',
        'red',
        'orange',
        'cyan',
        'gray',
    ];

    private string $colorName;
    private int $colorNr;

    public function __construct(string $colorShort)
    {
        $this->colorName = $colorShort;
        $this->colorNr = array_search($colorShort, self::$table);
        if ($this->colorNr === false) {
            throw new RuntimeException("color does not exist");
        }
    }

    public function getColorName(): string
    {
        return $this->colorName;
    }

    public function getColorNr(): int
    {
        return $this->colorNr;
    }
}