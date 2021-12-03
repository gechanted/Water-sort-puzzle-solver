<?php
require_once __DIR__ . '/Board.php';
require_once __DIR__ . '/BoardFactory.php';
require_once __DIR__ . '/Tube.php';
require_once __DIR__ . '/Color.php';
require_once __DIR__ . '/ProgressRecorder.php';
require_once __DIR__ . '/PrintToTerminal.php';
require_once __DIR__ . '/TerminalRow.php';
require_once __DIR__ . '/Timer.php';
require_once __DIR__ . '/HashLog.php';

function getFullTube(int $nr): Tube {
    $tube = new Tube($nr, 3);
    $tube->addColor(new Color('eyebleach'));
    $tube->addColor(new Color('eyebleach'));
    $tube->addColor(new Color('eyebleach'));
    return $tube;
}

$recorder = new ProgressRecorder();

$tube1 = new Tube(1);
$tube1->addColor(new Color('blue'));
$tube1->addColor(new Color('blue'));
$tube1->addColor(new Color('blue'));
$tube1->addColor(new Color('pink'));

$tube2 = new Tube(2, 3);
$tube2->addColor(new Color('pink'));
$tube2->addColor(new Color('blue'));
$tube2->addColor(new Color('blue'));

$tube3 = new Tube(3, 3);

$tube4 = new Tube(4, 2);
$tube4->addColor(new Color('blue'));
$tube4->addColor(new Color('pink'));

$tube5 = getFullTube(5);
$tube6 = getFullTube(6);
$tube7 = getFullTube(7);
$tube8 = getFullTube(8);
$tube9 = getFullTube(9);
$tube10 = getFullTube(10);
$tube11 = getFullTube(11);
$tube12 = getFullTube(12);
$tube13 = getFullTube(13);
$tube14 = getFullTube(14);
$tube15 = getFullTube(15);

$board = new Board([
    $tube1, $tube2, $tube3, $tube4
//    , $tube5, $tube6, $tube7, $tube8, $tube9, $tube10, $tube11, $tube12, $tube13, $tube14
], $recorder, 0, true, true);

var_dump($board->solve());

$printer = new PrintToTerminal( 4, 10);
echo $printer->print($recorder);

