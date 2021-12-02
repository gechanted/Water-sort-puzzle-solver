<?php

class Timer
{

    private static int $time_start = 0;
    private static int $last_time = 0;
    private static int $times = 0;

    public static function start(){
        self::$time_start = microtime(true);
        self::$last_time = microtime(true);
    }

    public static function time(string $status = ''){
        if (self::$time_start === 0) {
            self::start();
        }

        self::$times++;
        $totalTime = microtime(true) - self::$time_start;
        $relativeTime = microtime(true) - self::$last_time;
        self::$last_time = microtime(true);
        echo $status . self::$times . ': ' . round($totalTime, 8) . ' - ' . round($relativeTime, 8) . PHP_EOL;
    }
}