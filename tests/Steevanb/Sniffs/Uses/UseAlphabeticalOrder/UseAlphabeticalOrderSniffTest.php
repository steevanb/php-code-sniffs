<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Uses\UseAlphabeticalOrder;

use Steevanb\PhpCodeSniffs\Tests\AbstractSniffTestCase;

class UseAlphabeticalOrderSniffTest extends AbstractSniffTestCase
{
    private const string ERROR_SOURCE = 'Steevanb.Uses.UseAlphabeticalOrder.InvalidOrder';

    protected static function getSniffName(): string
    {
        return 'Steevanb.Uses.UseAlphabeticalOrder';
    }

    public function testCorrectOrderIsAllowed(): void
    {
        static::assertNoErrors('CorrectOrder.php');
    }

    public function testSingleUseIsAllowed(): void
    {
        static::assertNoErrors('SingleUse.php');
    }

    public function testNoUseIsAllowed(): void
    {
        static::assertNoErrors('NoUse.php');
    }

    public function testWrongOrderIsDisallowed(): void
    {
        static::assertError(
            'WrongOrder.php',
            8,
            'Use import "Alpha\Foo" must be before "Beta\Bar" (alphabetical order)',
            self::ERROR_SOURCE
        );
    }

    public function testGroupedCorrectOrderIsAllowed(): void
    {
        static::assertNoErrors('GroupedCorrectOrder.php');
    }

    public function testGroupedWrongOrderIsDisallowed(): void
    {
        static::assertError(
            'GroupedWrongOrder.php',
            8,
            'Use import "Alpha\Bar" must be before "Beta\Baz" (alphabetical order)',
            self::ERROR_SOURCE
        );
    }

    public function testGroupedWrongOrderInGroupIsDisallowed(): void
    {
        static::assertError(
            'GroupedWrongOrderInGroup.php',
            7,
            'Use import "Alpha\Bar" must be before "Alpha\Foo" (alphabetical order)',
            self::ERROR_SOURCE
        );
    }
}
