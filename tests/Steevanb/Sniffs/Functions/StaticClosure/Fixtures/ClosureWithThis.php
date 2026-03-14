<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Functions\StaticClosure\Fixtures;

class ClosureWithThis
{
    public function method(): void
    {
        $fn = function (): string {
            return $this->name();
        };
    }

    private function name(): string
    {
        return 'foo';
    }
}
