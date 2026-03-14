<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Syntax\NewWithoutParentheses\Fixtures;

class Foo
{
    public function bar(): void
    {
    }
}

new Foo()->bar();
