<?php

class ProgressRecorder
{

    /**
     * @var Board[]
     */
    private array $boardCollection = [];
    /** @var string[] */
    private array $moveCollection = [];

    public function recordBoard(Board $board): void
    {
        $this->boardCollection[] = $board;
    }

    public function recordMove(string $move)
    {
        $this->moveCollection[] = $move;
    }

    /**
     * @return Board[]
     */
    public function getBoardCollection(): array
    {
        return array_reverse($this->boardCollection);
    }

    /**
     * @return string[]
     */
    public function getMoveCollection(): array
    {
        return array_reverse($this->moveCollection);
    }
}