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
        $colors = $tube->getContent();
        $height = $tube->getHeight();
        $contentHeight = count($colors);
        $filler = $height - $contentHeight;

        $rows = ['   '. $this->to4chars($tube->getNr()) . ' '];
        for ($i = 0; $i < $filler; $i++) {
            $rows[] = ' |    | ';
        }
        foreach ($colors as $color) {
            $rows[] = ' |'. $this->to4chars($color->getColorName()) .'| ';
        }
        $rows[] = ' +----+ ';

        return new TerminalRow($rows);
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
