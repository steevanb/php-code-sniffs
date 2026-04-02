<?php

declare(strict_types=1);

class MyService
{
    public function doSomething(int $count = 10, bool $verbose = false): void
    {
    }

    public function caller(): void
    {
        $this->doSomething(5, false);
    }
}
