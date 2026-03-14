<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Formatting\DisallowMultipleStatements\Fixtures;

class HooksWithCodeOneLine
{
    private bool $enabled = false;

    public bool $streamEnabled { get => $this->enabled; set => $this->enabled = $value; }
}
