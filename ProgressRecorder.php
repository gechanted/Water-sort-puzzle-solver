<?php

class ProgressRecorder
{

    /**
     * @var Board[]
     */
    private array $boardCollection = [];
    private bool $flippedForInput = true;

    public function recordBoard(Board $board): void
    {
        $this->flip(true);
        $this->boardCollection[] = $board;
    }

    public function flip(bool $flipForInput): void
    {
        if ($flipForInput !== $this->flippedForInput) {
            $this->boardCollection = array_reverse($this->boardCollection);
        }
    }

    /**
     * @return Board[]
     */
    public function getBoardCollection(): array
    {
        $this->flip(false);
        return $this->boardCollection;
    }
}