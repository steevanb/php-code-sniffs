<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Php\DisallowSelf\Fixtures;

class SelfAccessProperty
{
    public static string $foo = 'bar';

    public function bar(): void
    {
        echo self::$foo;
    }
}
