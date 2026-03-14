<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Comparators\DisallowExclamationPoint;

use Steevanb\PhpCodeSniffs\Tests\AbstractSniffTestCase;

class DisallowExclamationPointSniffTest extends AbstractSniffTestCase
{
    private const string ERROR_MESSAGE = 'Exclamation mark to compare as false is not allowed, use === false instead';

    private const string ERROR_SOURCE = 'Steevanb.Comparators.DisallowExclamationPoint.NotAllowed';

    protected static function getSniffName(): string
    {
        return 'Steevanb.Comparators.DisallowExclamationPoint';
    }

    public function testWithExclamationIsDisallowed(): void
    {
        static::assertError('WithExclamation.php', 6, self::ERROR_MESSAGE, self::ERROR_SOURCE);
    }

    public function testWithExclamationParensIsDisallowed(): void
    {
        static::assertError('WithExclamationParens.php', 6, self::ERROR_MESSAGE, self::ERROR_SOURCE);
    }

    public function testWithFalseIsAllowed(): void
    {
        static::assertNoErrors('WithFalse.php');
    }
}
