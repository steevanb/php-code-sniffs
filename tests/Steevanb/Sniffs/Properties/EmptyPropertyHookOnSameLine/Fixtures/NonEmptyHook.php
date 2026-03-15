<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Properties\EmptyPropertyHookOnSameLine\Fixtures;

class NonEmptyHook
{
    public string $foo {
        get {
            return $this->foo;
        }
        set {
            $this->foo = $value;
        }
    }
}
