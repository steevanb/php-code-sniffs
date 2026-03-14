<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Classes\TraitUseAlphabeticalOrder;

use Steevanb\PhpCodeSniffs\Tests\AbstractSniffTestCase;

class TraitUseAlphabeticalOrderSniffTest extends AbstractSniffTestCase
{
    private const string ERROR_SOURCE = 'Steevanb.Classes.TraitUseAlphabeticalOrder.InvalidOrder';

    protected static function getSniffName(): string
    {
        return 'Steevanb.Classes.TraitUseAlphabeticalOrder';
    }

    public function testCorrectOrderIsAllowed(): void
    {
        static::assertNoErrors('CorrectOrder.php');
    }

    public function testSingleTraitIsAllowed(): void
    {
        static::assertNoErrors('SingleTrait.php');
    }

    public function testNoTraitIsAllowed(): void
    {
        static::assertNoErrors('NoTrait.php');
    }

    public function testWrongOrderIsDisallowed(): void
    {
        static::assertError(
            'WrongOrder.php',
            18,
            'Trait use "AlphaTrait" must be before "BetaTrait" (alphabetical order)',
            self::ERROR_SOURCE
        );
    }

    public function testMultipleTraitsOnSameUseCorrectOrderIsAllowed(): void
    {
        static::assertNoErrors('MultipleTraitsSameUseCorrectOrder.php');
    }

    public function testMultipleTraitsOnSameUseWrongOrderIsDisallowed(): void
    {
        static::assertError(
            'MultipleTraitsSameUseWrongOrder.php',
            17,
            'Trait use "AlphaTrait" must be before "BetaTrait" (alphabetical order)',
            self::ERROR_SOURCE
        );
    }

    public function testFixerWrongOrder(): void
    {
        static::assertFixerResult('WrongOrder.php', 'WrongOrderFixed.php');
    }

    public function testFixerMultipleTraitsOnSameUseWrongOrder(): void
    {
        static::assertFixerResult('MultipleTraitsSameUseWrongOrder.php', 'MultipleTraitsSameUseWrongOrderFixed.php');
    }
}
