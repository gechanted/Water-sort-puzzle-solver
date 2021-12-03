<?php

class BoardFactory
{
    /** @var Tube[] */
    private array $tubes = [];

    /**
     * @param string[] $colorNames
     * @param int $height
     * @return BoardFactory
     */
    public function addTube(array $colorNames, int $height = 4): BoardFactory
    {
        $tube = new Tube(count($this->tubes) + 1, $height);
        foreach (array_reverse($colorNames) as $color) {
            $tube->addColor(new Color($color));
        }
        $this->tubes[] = $tube;
        return $this;
    }

    public function createBoard(bool $echoPath = false, bool $echoTime = false): Board
    {
        return new Board($this->tubes, new ProgressRecorder(), 0, null, $echoPath, $echoTime);
    }
}