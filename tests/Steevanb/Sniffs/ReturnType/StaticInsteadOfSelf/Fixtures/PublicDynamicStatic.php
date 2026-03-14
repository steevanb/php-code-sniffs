<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\ReturnType\StaticInsteadOfSelf\Fixtures;

class PublicDynamicStatic
{
    public function foo(): static
    {
    }
}
