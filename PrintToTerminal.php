<?php

class PrintToTerminal
{

    private ?int $showTubesPerRow;
    private int $tubeTextLength;

    public function __construct(int $showTubesPerRow = null, int $tubeTextLength = 4)
    {
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

    public function print(ProgressRecorder $recorder): string
    {
        $fancyLine =  '_-';
        $fancyLine .= str_repeat('_-', ceil($this->rowSize() / 2));

        $output = PHP_EOL . $fancyLine . PHP_EOL . PHP_EOL;
        foreach ($recorder->getBoardCollection() as $board) {
            $output .= ' > Step ' . $board->getDeepness() . PHP_EOL;
            $row = $this->printBoardToRow($board);

            //break the line into the next one
            do {
                $newRow = null;
                if ($this->showTubesPerRow !== null) {
                    $newRow = $row->split($this->rowSize());
                }
                $output .= $row->toString() . PHP_EOL;
                $row = $newRow;
            } while ($newRow !== null && $newRow->isEmpty() === false);

            //prevent drawing the last line (a line too much)
            if ($board->isSolved() === false) {
                $output .= str_repeat('-', $this->rowSize()) . PHP_EOL . PHP_EOL;
            }
        }

        $output .= $fancyLine;

        return $output;
    }

    /**
     * @param Tube $tube
     * @return TerminalRow
     *
     * tube is RENDERED into a Row
     */
    public function renderTubeToRow(Tube $tube): TerminalRow
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

    /**
     * @param Board $board
     * @return TerminalRow
     */
    public function printBoardToRow(Board $board): TerminalRow
    {
        $row = new TerminalRow([], false);
        foreach ($board->getTubes() as $tube) {
            $row->addRow($this->renderTubeToRow($tube));
        }
        return $row;
    }
}
