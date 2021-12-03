<?php
require_once __DIR__ . '/Board.php';
require_once __DIR__ . '/BoardFactory.php';
require_once __DIR__ . '/Tube.php';
require_once __DIR__ . '/HashLog.php';
require_once __DIR__ . '/Color.php';
require_once __DIR__ . '/ProgressRecorder.php';
require_once __DIR__ . '/PrintToTerminal.php';
require_once __DIR__ . '/TerminalRow.php';
require_once __DIR__ . '/Timer.php';
require_once __DIR__ . '/Game.php';

$boardFactory = new BoardFactory();
$boardFactory
    ->addTube(['red', 'yellow', 'lightgreen', 'darkgreen'])
    ->addTube(['red', 'yellow', 'lightgreen', 'darkgreen'])
    ->addTube(['red', 'yellow', 'lightgreen', 'darkgreen'])
    ->addTube(['red', 'yellow', 'lightgreen', 'darkgreen'])
    ->addTube([])
    ->addTube([]);

$board = $boardFactory->createBoard();
$game = new Game($board, new PrintToTerminal(6,10));
$game->main();
