<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Php\DisallowSelf\Fixtures;

class StaticCallPublicMethod
{
    public function foo(): void
    {
    }

    public function bar(): void
    {
        static::foo();
    }
}
