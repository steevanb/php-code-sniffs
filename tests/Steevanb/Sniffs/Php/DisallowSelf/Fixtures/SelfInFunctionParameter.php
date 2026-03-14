<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Php\DisallowSelf\Fixtures;

class SelfInFunctionParameter
{
    public function foo(self $bar): void
    {
    }
}
