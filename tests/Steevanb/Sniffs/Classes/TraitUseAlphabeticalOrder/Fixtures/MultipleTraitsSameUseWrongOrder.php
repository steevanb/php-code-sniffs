<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Classes\TraitUseAlphabeticalOrder\Fixtures;

trait AlphaTrait
{
}

trait BetaTrait
{
}

class MultipleTraitsSameUseWrongOrder
{
    use BetaTrait, AlphaTrait;
}
