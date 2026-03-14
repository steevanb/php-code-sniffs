<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Classes\ClassMemberOrder\Fixtures;

class MethodBeforeProperty
{
    public function foo(): void
    {
    }

    public string $bar = 'a';
}
