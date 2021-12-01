<?php

class PrintToTerminal
{

    private ProgressRecorder $recorder;
    private ?int $showTubesPerRow;
    private int $tubeTextLength;

    public function __construct(ProgressRecorder $recorder, int $showTubesPerRow = null, int $tubeTextLength = 4)
    {
        $this->recorder = $recorder;
        $this->showTubesPerRow = $showTubesPerRow;
        $this->tubeTextLength = $tubeTextLength;
    }

    private function tubeSize(): int
    {
        return 4 + $this->tubeTextLength;
    }

    private function rowSize(): int
    {
        return $this->tubeSize() * $this->showTubesPerRow;
    }

    public function print(): string
    {
        $fancyLine =  '_-';
        $fancyLine .= str_repeat('_-', ceil($this->rowSize() / 2));
        $line = '-';
        $line .= str_repeat('-', $this->rowSize());

        $output = PHP_EOL . $fancyLine . PHP_EOL . PHP_EOL;
        foreach ($this->recorder->getBoardCollection() as $board) {
            $row = new TerminalRow([], false);
            foreach ($board->getTubes() as $tube) {
                $row->addRow($this->tubeToRow($tube));
            }

            do {
                $newRow = null;
                if ($this->showTubesPerRow !== null) {
                    $newRow = $row->split($this->rowSize());
                }
                $output .= $row->toString() . PHP_EOL;
                $row = $newRow;
            } while ($newRow !== null && $newRow->isEmpty() === false);

            if ($board->isSolved() === false) {
                $output .= $line . PHP_EOL . PHP_EOL;
            }
        }

        $output .= $fancyLine;

        return $output;
    }

    public function tubeToRow(Tube $tube): TerminalRow
    {
        $colors = array_reverse($tube->getContent());
        $height = $tube->getHeight();
        $contentHeight = count($colors);
        $filler = $height - $contentHeight;

        $rows = ['   '. $this->toXchars($tube->getNr(), $this->tubeTextLength) . ' '];
        for ($i = 0; $i < $filler; $i++) {
            $rows[] = ' |'. str_repeat(' ', $this->tubeTextLength) .'| ';
        }
        foreach ($colors as $color) {
            $rows[] = ' |'. $this->toXchars($color->getColorName(), $this->tubeTextLength) .'| ';
        }
        $rows[] = ' +'. str_repeat('-', $this->tubeTextLength) .'+ ';

        return new TerminalRow($rows, false);
    }

    private function toXchars(string $string, int $wishedStringLength = 4): string
    {
        $length = strlen($string);
        if ($length < $wishedStringLength) {
            return str_pad($string, $wishedStringLength, ' ',STR_PAD_BOTH);
        } else {
            return substr($string, 0, $wishedStringLength);
        }
    }
}
