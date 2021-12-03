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
    private int $deepness;
    private bool $echoPath;
    private bool $echoTime;

    public function __construct(array $tubes, ProgressRecorder $recorder, int $deepness = 0,
                                bool $echoPath = false, bool $echoTime = false)
    {
        $this->tubes = $tubes;
        $this->recorder = $recorder;
        $this->deepness = $deepness;
        $this->echoPath = $echoPath;
        $this->echoTime = $echoTime;
    }

    public function isSolved(): bool
    {
        foreach ($this->tubes as $tube) {
            if ($tube->isSolved() === false) {
                return false;
            }
        }

        return true;
    }

    public function solve(): bool
    {
        if ($this->echoTime) { Timer::time(str_repeat('  ', $this->deepness)); }
        $isSolved = $this->isSolved();

        if ($isSolved) {
            $this->recorder->recordBoard($this);
            return true;
        }

        foreach ($this->tubes as $k1 => $tube1) {
            foreach ($this->tubes as $k2 => $tube2) {
                if ($tube1 !== $tube2) {
                    if ($tube2->canReceive($tube1->getExtractable())) {
                        if ($this->echoPath) { echo str_repeat('  ', $this->deepness) . $tube1->getNr() . ' into ' . $tube2->getNr() . PHP_EOL;}
                        //spawn new thread
                        $newBoard = $this->clone();
                        $result = $newBoard->solvingMove($k1, $k2);
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

        $extract = $tube1->getExtractable();
        $tube2->doReceive($extract);
        $tube1->doExtract();

        $hash = $this->hash();
        if (array_search($hash, self::$generalLog)) {
            return false;
        }
        self::$generalLog[] = $hash;

        $result = $this->solve();
        if ($result) {
            $this->recorder->recordMove($tube1->getNr() . ' -> ' . $extract[0]->getColorName() . ' -> ' . $tube2->getNr());
            return true;
        }
        if ($this->echoPath) { echo str_repeat('  ', $this->deepness) . ' <- fail' . PHP_EOL;}
        return false;
    }



    public function clone(): Board
    {
        $tubeArr = [];
        foreach ($this->tubes as $tube) {
            $tubeArr[] = clone $tube;
        }

        return new Board($tubeArr, $this->recorder, $this->deepness +1,
            $this->echoPath, $this->echoTime);
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

    public function getRecorder(): ProgressRecorder
    {
        return $this->recorder;
    }

    public function getDeepness(): int
    {
        return $this->deepness;
    }
}