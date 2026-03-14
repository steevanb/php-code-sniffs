<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Classes\ClassMemberOrder\Fixtures;

class StaticMethodBeforeConstant
{
    public static function foo(): void
    {
    }

    public const string BAR = 'a';
}
