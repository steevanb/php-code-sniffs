<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Classes\TraitUseAlphabeticalOrder\Fixtures;

trait AlphaTrait
{
}

trait BetaTrait
{
}

class CorrectOrder
{
    use AlphaTrait;
    use BetaTrait;
}
