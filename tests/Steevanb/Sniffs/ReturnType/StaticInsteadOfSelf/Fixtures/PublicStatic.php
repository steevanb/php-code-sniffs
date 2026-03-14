<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\ReturnType\StaticInsteadOfSelf\Fixtures;

class PublicStatic
{
    public static function foo(): self
    {
    }
}
