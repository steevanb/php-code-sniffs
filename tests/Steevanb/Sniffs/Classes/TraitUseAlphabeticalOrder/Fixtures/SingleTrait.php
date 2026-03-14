<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Classes\TraitUseAlphabeticalOrder\Fixtures;

trait OnlyTrait
{
}

class SingleTrait
{
    use OnlyTrait;
}
