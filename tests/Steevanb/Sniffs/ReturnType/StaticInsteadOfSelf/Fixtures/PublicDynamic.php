<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\ReturnType\StaticInsteadOfSelf\Fixtures;

class PublicDynamic
{
    public function foo(): self
    {
    }
}
