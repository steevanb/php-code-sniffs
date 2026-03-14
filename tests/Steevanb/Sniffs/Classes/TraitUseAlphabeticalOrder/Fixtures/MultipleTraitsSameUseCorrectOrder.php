<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Classes\TraitUseAlphabeticalOrder\Fixtures;

trait FooTrait
{
}

trait BarTrait
{
}

class MultipleTraitsSameUseCorrectOrder
{
    use BarTrait, FooTrait;
}
