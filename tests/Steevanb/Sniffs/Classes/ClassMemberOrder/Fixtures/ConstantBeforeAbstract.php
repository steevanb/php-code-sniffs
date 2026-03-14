<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Classes\ClassMemberOrder\Fixtures;

abstract class ConstantBeforeAbstract
{
    public const string FOO = 'a';

    abstract public function bar(): void;
}
