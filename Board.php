<?php
require_once __DIR__ . '/Tube.php';
require_once __DIR__ . '/ProgressRecorder.php';

class Board
{

    /**
     * @var string[]
     */
    private static array $generalLog = [];

    private ProgressRecorder $recorder;

    /**
     * @var Tube[]
     */
    private array $tubes;

    public function __construct(array $tubes, ProgressRecorder $recorder)
    {
        $this->tubes = $tubes;
        $this->recorder = $recorder;
    }

    public function isSolved(): bool
    {
        $isSolved = true;
        foreach ($this->tubes as $tube) {
            if ($tube->isSolved() === false) {
                $isSolved = false;
                break;
            }
        }

        return $isSolved;
    }

    public function solve(): bool
    {
        $isSolved = $this->isSolved();

        if ($isSolved) {
            $this->recorder->recordBoard($this);
            return true;
        }

        foreach ($this->tubes as $tube1) {
            foreach ($this->tubes as $tube2) {
                if ($tube1 !== $tube2) {
                    if ($tube2->canReceive($tube1->getExtractable())) {
                        //spawn new thread
                        $newBoard = $this->clone();
                        $result = $newBoard->solvingMove(
                            array_search($tube1, $this->tubes),
                            array_search($tube2, $this->tubes)
                        );
                        if ($result) {
                            $this->recorder->recordBoard($this);
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    public function solvingMove($tube1Index, $tube2Index): bool
    {
        $tube1 = $this->tubes[$tube1Index];
        $tube2 = $this->tubes[$tube2Index];

        $tube2->doReceive($tube1->getExtractable());
        $tube1->doExtract();

        $hash = $this->hash();
        if (array_search($hash, self::$generalLog)) {
            return false;
        }
        self::$generalLog[] = $hash;
        if ($this->solve()) {
            return true;
        }
        return false;
    }



    public function clone(): Board
    {
        $tubeArr = [];
        foreach ($this->tubes as $tube) {
            $tubeArr[] = clone $tube;
        }

        return new Board($tubeArr, $this->recorder);
    }

    public function hash(): string
    {
        $hashes = [];
        foreach ($this->tubes as $tube) {
            $hashes[] = intval($tube->hash());
        }
        sort($hashes);

        $wholeHash = '';
        foreach ($hashes as $hash) {
            $wholeHash .= str_pad($hash, 4, "0", STR_PAD_LEFT);
        }
        return $wholeHash;
    }

    /**
     * @return Tube[]
     */
    public function getTubes(): array
    {
        return $this->tubes;
    }

}