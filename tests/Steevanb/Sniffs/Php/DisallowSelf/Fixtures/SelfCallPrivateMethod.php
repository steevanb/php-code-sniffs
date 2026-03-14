<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Php\DisallowSelf\Fixtures;

class SelfCallPrivateMethod
{
    private function foo(): void
    {
    }

    public function bar(): void
    {
        self::foo();
    }
}
