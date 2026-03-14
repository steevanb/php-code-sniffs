<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Classes\ClassMemberOrder\Fixtures;

class PrivateBeforePublic
{
    private function foo(): void
    {
    }

    public function bar(): void
    {
    }
}
