<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Syntax\Concatenation;

use Steevanb\PhpCodeSniffs\Tests\AbstractSniffTestCase;

class ConcatenationSniffTest extends AbstractSniffTestCase
{
    private const string ERROR_MESSAGE = 'Concatenation character "." should be surrounded by spaces';

    private const string ERROR_SOURCE = 'Steevanb.Syntax.Concatenation.SurroundedBySpaces';

    protected static function getSniffName(): string
    {
        return 'Steevanb.Syntax.Concatenation';
    }

    public function testWithSpacesIsAllowed(): void
    {
        static::assertNoErrors('WithSpaces.php');
    }

    public function testNoSpaceBeforeIsDisallowed(): void
    {
        static::assertError('NoSpaceBefore.php', 5, self::ERROR_MESSAGE, self::ERROR_SOURCE);
    }

    public function testNoSpaceAfterIsDisallowed(): void
    {
        static::assertError('NoSpaceAfter.php', 5, self::ERROR_MESSAGE, self::ERROR_SOURCE);
    }

    public function testNoSpacesIsDisallowed(): void
    {
        static::assertError('NoSpaces.php', 5, self::ERROR_MESSAGE, self::ERROR_SOURCE);
    }

    public function testFixerNoSpaceBefore(): void
    {
        static::assertFixerResult('NoSpaceBefore.php', 'NoSpaceBeforeFixed.php');
    }

    public function testFixerNoSpaceAfter(): void
    {
        static::assertFixerResult('NoSpaceAfter.php', 'NoSpaceAfterFixed.php');
    }

    public function testFixerNoSpaces(): void
    {
        static::assertFixerResult('NoSpaces.php', 'NoSpacesFixed.php');
    }
}
