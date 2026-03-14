<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Classes\ClassMemberOrder\Fixtures;

class ConstructBeforeProperty
{
    public function __construct()
    {
    }

    public string $foo = 'a';
}
