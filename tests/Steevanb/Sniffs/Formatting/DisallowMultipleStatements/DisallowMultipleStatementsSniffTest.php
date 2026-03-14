<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Formatting\DisallowMultipleStatements;

use Steevanb\PhpCodeSniffs\Tests\AbstractSniffTestCase;

class DisallowMultipleStatementsSniffTest extends AbstractSniffTestCase
{
    private const string ERROR_MESSAGE = 'Each PHP statement must be on a line by itself';

    private const string ERROR_SOURCE = 'Steevanb.Formatting.DisallowMultipleStatements.SameLine';

    protected static function getSniffName(): string
    {
        return 'Steevanb.Formatting.DisallowMultipleStatements';
    }

    public function testEmptyHooksOneLineIsAllowed(): void
    {
        static::assertNoErrors('EmptyHooksOneLine.php');
    }

    public function testEmptyHooksMultiLineIsAllowed(): void
    {
        static::assertNoErrors('EmptyHooksMultiLine.php');
    }

    public function testHooksWithCodeOneLineIsDisallowed(): void
    {
        static::assertError('HooksWithCodeOneLine.php', 11, self::ERROR_MESSAGE, self::ERROR_SOURCE);
    }

    public function testMultipleStatementsOneLineIsDisallowed(): void
    {
        static::assertError('MultipleStatementsOneLine.php', 5, self::ERROR_MESSAGE, self::ERROR_SOURCE);
    }

    public function testSingleStatementPerLineIsAllowed(): void
    {
        static::assertNoErrors('SingleStatementPerLine.php');
    }

    public function testForLoopIsAllowed(): void
    {
        static::assertNoErrors('ForLoop.php');
    }

    public function testFixerHooksWithCodeOneLine(): void
    {
        static::assertFixerResult('HooksWithCodeOneLine.php', 'HooksWithCodeOneLineFixed.php');
    }

    public function testFixerMultipleStatementsOneLine(): void
    {
        static::assertFixerResult('MultipleStatementsOneLine.php', 'MultipleStatementsOneLineFixed.php');
    }
}
