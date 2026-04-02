<?php

declare(strict_types=1);

class Config
{
    public function __construct(
        private readonly bool $debug = false,
        private readonly int $timeout = 30
    ) {
    }
}

$config = new Config();
