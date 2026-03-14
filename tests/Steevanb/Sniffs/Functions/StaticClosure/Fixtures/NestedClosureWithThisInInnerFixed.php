<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Functions\StaticClosure\Fixtures;

class NestedClosureWithThisInInner
{
    public function method(): void
    {
        $fn = static function (): \Closure {
            return function (): string {
                return $this->name();
            };
        };
    }

    private function name(): string
    {
        return 'foo';
    }
}
