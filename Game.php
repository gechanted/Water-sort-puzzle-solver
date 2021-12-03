<?php

class Game
{
    private Board $startingBoard;
    private Board $board;
    private PrintToTerminal $printer;

    public function __construct(Board $board, PrintToTerminal $printer)
    {
        $this->startingBoard = $board;
        $this->board = $board->clone(true);

        $this->printer = $printer;
    }

    private function move(Tube $tube1, Tube $tube2): bool
    {
        $extract = $tube1->getExtractable();
        if ($tube2->canReceive($extract)) {
            $tube2->doReceive($extract);
            $tube1->doExtract();
            return true;
        }
        return false;
    }

    public function main()
    {
        echo $this->printer->printBoardToRow($this->board)->toString() . PHP_EOL;
        echo 'What do you want to do?' . PHP_EOL . 'help XinY restart possible? tip end' . PHP_EOL;
        while (true) {
            $line = readline();
            $line = trim(strtolower($line));

            if ($line === 'end') {
                break;
            } elseif ($line === 'restart') {
                $this->board = $this->startingBoard->clone(true);
                echo $this->printer->printBoardToRow($this->board)->toString() . PHP_EOL;
                echo 'What do you want to do?' . PHP_EOL . 'help XinY restart possible? tip end' . PHP_EOL;
            } elseif ($line === 'possible' || $line === 'possible?') {
                if ($this->board->clone(true)->solve()) {
                    echo 'This puzzle is still solvable' . PHP_EOL;
                } else {
                    echo 'This puzzle is not possible anymore'. PHP_EOL . "type 'restart' to start over" . PHP_EOL;
                }
            } elseif ($line === 'tip') {
                $board = $this->board->clone(true);
                if ($board->solve()) {
                    echo $board->getRecorder()->getMoveCollection()[0] . PHP_EOL;
                } else {
                    echo 'is not possible anymore'. PHP_EOL . "type 'restart' to start over" . PHP_EOL;
                }
            } elseif ($line === 'help') {
                echo <<<'EOF'
'help' 
    prints information about the commands
'XinY'
    makes a move 
    X stands for the tube-nr you want to put in tube-nr Y
    if the move is not possible, a message is printed out
'restart' 
    resets the board to the original one
'possible'
    tells you if the board is still solvable
'tip'
    gives you a tip of what can be done, 
    or tells you the board is unsolvable
'end'
    ends this program
EOF . PHP_EOL;
            }
            else{
                $position = strpos($line, 'in');
                $firstNumber = substr($line, 0, $position);
                $secondNumber = substr($line, $position + 2);
                if (ctype_digit($firstNumber) && ctype_digit($secondNumber)) {
                    $tube1 = null;
                    $tube2 = null;
                    foreach ($this->board->getTubes() as $tube) {
                        if ($tube->getNr() === (int) $firstNumber) { $tube1 = $tube; }
                        if ($tube->getNr() === (int) $secondNumber) { $tube2 = $tube; }
                    }

                    if ($tube1 === null) {
                        echo 'tube with the number '.$firstNumber.' does not exist' . PHP_EOL;
                    } elseif ($tube2 === null) {
                        echo 'tube with the number ' . $secondNumber . ' does not exist' . PHP_EOL;
                    } else {
                        if ($this->move($tube1, $tube2)) {
                            echo $this->printer->printBoardToRow($this->board)->toString() . PHP_EOL;
                            if ($this->board->isSolved()) {
                                echo 'good job, you did it';
                                break;
                            }
                        } else {
                            echo "you cannot fill tube nr.$firstNumber into nr.$secondNumber" . PHP_EOL;
                        }
                    }
                } else {
                    echo 'I didnt understand that' . PHP_EOL;
                }
            }
        }
    }
}