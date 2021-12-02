<?php
require_once __DIR__ . '/Board.php';
require_once __DIR__ . '/BoardFactory.php';
require_once __DIR__ . '/Tube.php';
require_once __DIR__ . '/Color.php';
require_once __DIR__ . '/ProgressRecorder.php';
require_once __DIR__ . '/PrintToTerminal.php';
require_once __DIR__ . '/TerminalRow.php';
require_once __DIR__ . '/Timer.php';
Timer::start();

$boardFactory = new BoardFactory();
$boardFactory
    ->addTube(['red', 'yellow', 'lightgreen', 'darkgreen'])
    ->addTube(['pink', 'red', 'orange', 'yellow'])
    ->addTube(['red', 'yellow', 'lime', 'lightgreen'])
    ->addTube(['gray', 'purple', 'lime', 'pink'])
    ->addTube(['lime', 'brown', 'brown', 'pink'])
    ->addTube(['gray', 'gray', 'brown', 'yellow'])
    ->addTube(['orange', 'lime', 'darkgreen', 'lightgreen'])
    ->addTube(['lightgreen', 'blue', 'darkgreen', 'darkgreen'])
    ->addTube(['purple', 'pink', 'cyan', 'cyan'])
    ->addTube(['red', 'cyan', 'orange', 'cyan'])
    ->addTube(['orange', 'gray', 'blue', 'blue'])
    ->addTube(['purple', 'brown', 'blue', 'purple'])
    ->addTube([])
    ->addTube([]);

$board = $boardFactory->createBoard();
var_dump($board->solve());

$printer = new PrintToTerminal(6, 6);
echo $printer->print($board->getRecorder());
