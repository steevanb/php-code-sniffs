<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Php\DisallowSelf\Fixtures;

class SelfAccessPublicConstant
{
    public const string FOO = 'bar';

    public function bar(): void
    {
        echo self::FOO;
    }
}
