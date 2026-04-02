<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\CodeAnalysis\ForbidRedundantArgument\Fixtures;

use PHPUnit\Framework\Attributes\DataProvider;

class AttributeRedundant
{
    #[DataProvider('provideData')]
    public function testSomething(): void
    {
    }
}
