<?php

class TerminalRow
{
    /** @var string[] */
    private array $rows;
    private bool $inCaseOfHeightDifferenceStayTopside;

    /**
     * @param string[] $rows
     * @param bool $inCaseOfHeightDifferenceStayTopside
     */
    public function __construct(array $rows = [], bool $inCaseOfHeightDifferenceStayTopside = true)
    {
        $this->inCaseOfHeightDifferenceStayTopside = $inCaseOfHeightDifferenceStayTopside;
        $this->rows = $rows;
    }

    public function addRow(TerminalRow $addingRow): void
    {
        $toAdd = $addingRow->getRows();
        $lineDiff = count($this->rows) - count($toAdd);
        if ($lineDiff < 0) { //if lineDiff is smaller than zero
            //buff $this->rows to fit
            if ($this->isInCaseOfHeightDifferenceStayTopside()) {
                $this->rows = $this->buffArrayBottom($this->rows, -$lineDiff);
            } else {
                $this->rows = $this->buffArrayTop($this->rows, -$lineDiff);
            }
        } elseif ($lineDiff > 0) {//if lineDiff is bigger than zero
            //buff $toAdd to fit
            if ($addingRow->isInCaseOfHeightDifferenceStayTopside()) {
                $toAdd = $this->buffArrayBottom($toAdd, $lineDiff);
            } else {
                $toAdd = $this->buffArrayTop($toAdd, $lineDiff);
            }
        }

        foreach ($this->rows as $key => $v) {
            $this->rows[$key] .= $toAdd[$key];
        }
    }

    public function split(int $splitPoint = 96): TerminalRow
    {
        //if this can't be split give back an empty
        if (array_key_exists(0, $this->rows)) {
            if (strlen($this->rows[0]) < $splitPoint) {
                return new TerminalRow([], $this->inCaseOfHeightDifferenceStayTopside);
            }
        }
        $resultArr = [];

        foreach ($this->rows as $key => $cmdlineLine) {
            //key just in case the array is in disorder:
            //foreach doesn't care about a logical key order
            $resultArr[$key] = substr($cmdlineLine, $splitPoint);
            $this->rows[$key] = substr($cmdlineLine, 0, $splitPoint);
        }

        return new TerminalRow($resultArr, $this->inCaseOfHeightDifferenceStayTopside);
    }

    public function toString(): string
    {
        return implode(PHP_EOL, $this->rows);
    }

    /**
     * @return string[]
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    private function buffArrayBottom(array $array, int $lineDiff): array
    {
        $buff = $this->getBuff($array);

        for ($i = 0; $i < $lineDiff; $i++) {
            $array[] = $buff;
        }

        return $array;
    }

    private function buffArrayTop(array $array, int $lineDiff): array
    {
        $buff = $this->getBuff($array);

        for ($i = -1; $i >= -$lineDiff; $i--) {
            $array[$i] = $buff;
        }

        ksort($array);
        return array_values($array);

//        $arr  =['ha', -1 => 'ho', 'he', -2 => 'hu', 'hi'];
//        var_dump($arr);
//        ksort($arr);
//        var_dump($arr);
//        $arr = array_values($arr);
//        var_dump($arr);
    }

    private function getBuff(array $array): string
    {
        $buff = '';
        if (array_key_exists(0, $array)) {
            $counter = strlen($array[0]);
            $buff = str_repeat(' ', $counter);
        }
        return $buff;
    }

    public function isInCaseOfHeightDifferenceStayTopside(): bool
    {
        return $this->inCaseOfHeightDifferenceStayTopside;
    }

    public function isEmpty(): bool
    {
        return array_key_exists(0, $this->rows) === false || $this->rows[0] === '';
    }
}