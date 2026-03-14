<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Classes\ClassMemberOrder\Fixtures;

trait BarTrait
{
}

class PropertyBeforeUse
{
    public string $foo = 'a';

    use BarTrait;
}
