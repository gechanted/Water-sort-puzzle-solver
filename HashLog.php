<?php

class HashLog
{

    /** @var string[] */
    private array $log = [];

    public function search(string $hash): bool
    {
        return array_search($hash, $this->log);
    }

    public function add(string $hash): void
    {
        $this->log[] = $hash;
    }
}