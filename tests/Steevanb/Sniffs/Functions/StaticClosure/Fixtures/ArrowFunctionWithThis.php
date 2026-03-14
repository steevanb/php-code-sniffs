<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Functions\StaticClosure\Fixtures;

class ArrowFunctionWithThis
{
    public function method(): \Closure
    {
        return fn(): string => $this->name();
    }

    private function name(): string
    {
        return 'foo';
    }
}
