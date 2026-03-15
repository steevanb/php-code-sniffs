<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Properties\EmptyPropertyHookOnSameLine\Fixtures;

class GetOnlyOnMultipleLines
{
    public string $foo {
        get;
    }
}
