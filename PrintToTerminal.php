<?php

class PrintToTerminal
{

    private ProgressRecorder $recorder;
    private ?int $limit;

    public function __construct(ProgressRecorder $recorder, int $limit = null)
    {
        $this->recorder = $recorder;
        $this->limit = $limit;
    }

    public function print(): string
    {
        $fancyLine =  '_-';
        $fancyLine .= str_repeat('_-', $this->limit / 2);
        $line = '-';
        $line .= str_repeat('-', $this->limit);

        $output = PHP_EOL . $fancyLine . PHP_EOL . PHP_EOL;
        foreach ($this->recorder->getBoardCollection() as $board) {
            $row = new TerminalRow();
            foreach ($board->getTubes() as $tube) {
                $row->addRow($this->tubeToRow($tube));
            }

            do {
                $newRow = null;
                if ($this->limit !== null) {
                    $newRow = $row->split($this->limit);
                }
                $output .= $row->toString() . PHP_EOL;
                $row = $newRow;
            } while ($newRow !== null && $newRow->getFirst() !== '');
            if ($board->isSolved() === false) {
                $output .= $line . PHP_EOL . PHP_EOL;
            }
        }

        $output .= $fancyLine;

        return $output;
    }

    public function tubeToRow(Tube $tube): TerminalRow
    {
        $colors = $tube->getContent();
        for ($i = 0; $i <= 3;$i++) {
            if (array_key_exists($i, $colors) === false) {
                $colors[$i] = new Color('');
            }
        }
        return new TerminalRow(
            ' |'. $this->to4chars($colors[3]->getColorName()) .'| ',
            ' |'. $this->to4chars($colors[2]->getColorName()) .'| ',
            ' |'. $this->to4chars($colors[1]->getColorName()) .'| ',
            ' |'. $this->to4chars($colors[0]->getColorName()) .'| ',
            ' +----+ '
        );
    }

    private function to4chars(string $string): string
    {
        $length = strlen($string);
        if ($length < 4) {
            return str_pad($string, 4);
        } else {
            return substr($string, 0, 4);
        }
    }
}

class TerminalRow
{
    private string $first;
    private string $second;
    private string $third;
    private string $forth;
    private string $fifth;

    public function __construct(
        string $first = '',
        string $second = '',
        string $third = '',
        string $forth = '',
        string $fifth = ''
    )
    {
        $this->first = $first;
        $this->second = $second;
        $this->third = $third;
        $this->forth = $forth;
        $this->fifth = $fifth;
    }

    public function add(
        string $first,
        string $second,
        string $third,
        string $forth,
        string $fifth
    )
    {
        $this->first .= $first;
        $this->second .= $second;
        $this->third .= $third;
        $this->forth .= $forth;
        $this->fifth .= $fifth;
    }

    public function addRow(TerminalRow $row): void
    {
        $this->add(
            $row->getFirst(),
            $row->getSecond(),
            $row->getThird(),
            $row->getForth(),
            $row->getFifth(),
        );
    }

    public function split(int $splitPoint = 96): TerminalRow
    {
        return new TerminalRow(
            $this->strsplit($this->first, $splitPoint),
            $this->strsplit($this->second, $splitPoint),
            $this->strsplit($this->third, $splitPoint),
            $this->strsplit($this->forth, $splitPoint),
            $this->strsplit($this->fifth, $splitPoint),
        );
    }

    private function strsplit(string &$string, int $splitPoint): string
    {
        $cutoff = substr($string, $splitPoint);
        $string = substr($string, 0, $splitPoint);
        return $cutoff;
    }

//$tr = new TerminalRow();
//$string = '1234567890-1234567890-1234567890';
//$result = $tr->strsplit($string, 15);
//
//echo $string . $result . PHP_EOL;
//var_dump($string, $result);}

    public function getFirst(): string { return $this->first; }
    public function getSecond(): string { return $this->second; }
    public function getThird(): string { return $this->third; }
    public function getForth(): string { return $this->forth; }
    public function getFifth(): string { return $this->fifth; }

    public function toString(): string
    {
        return $this->getFirst() . PHP_EOL
            . $this->getSecond() . PHP_EOL
            . $this->getThird() . PHP_EOL
            . $this->getForth() . PHP_EOL
            . $this->getFifth() . PHP_EOL;
    }
}