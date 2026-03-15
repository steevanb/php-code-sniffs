<?php

declare(strict_types=1);

class Foo
{
    public function bar(): void
    {
        $result = $this->service
            ->getResult();
    }
}
