<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Formatting\ChainedCallsOnNewLine;

use Steevanb\PhpCodeSniffs\Tests\AbstractSniffTestCase;

class ChainedCallsOnNewLineSniffTest extends AbstractSniffTestCase
{
    private const string ERROR_SOURCE = 'Steevanb.Formatting.ChainedCallsOnNewLine.FirstCallSameLine';

    protected static function getSniffName(): string
    {
        return 'Steevanb.Formatting.ChainedCallsOnNewLine';
    }

    public function testAllOnOneLineIsAllowed(): void
    {
        static::assertNoErrors('AllOnOneLine.php');
    }

    public function testEachOnNewLineIsAllowed(): void
    {
        static::assertNoErrors('EachOnNewLine.php');
    }

    public function testSingleCallIsAllowed(): void
    {
        static::assertNoErrors('SingleCall.php');
    }

    public function testNullsafeOnNewLineIsAllowed(): void
    {
        static::assertNoErrors('NullsafeOnNewLine.php');
    }

    public function testMethodWithArrayAccessIsAllowed(): void
    {
        static::assertNoErrors('MethodWithArrayAccess.php');
    }

    public function testFirstCallOnSameLineIsDisallowed(): void
    {
        static::assertError(
            'FirstCallOnSameLine.php',
            5,
            'First chained call must be on a new line when chain spans multiple lines',
            self::ERROR_SOURCE
        );
    }

    public function testFirstPropertyOnSameLineIsDisallowed(): void
    {
        static::assertError(
            'FirstPropertyOnSameLine.php',
            5,
            'First chained call must be on a new line when chain spans multiple lines',
            self::ERROR_SOURCE
        );
    }

    public function testNullsafeFirstCallOnSameLineIsDisallowed(): void
    {
        static::assertError(
            'NullsafeFirstCallOnSameLine.php',
            5,
            'First chained call must be on a new line when chain spans multiple lines',
            self::ERROR_SOURCE
        );
    }

    public function testMultipleCallsOnFirstLineIsDisallowed(): void
    {
        static::assertError(
            'MultipleCallsOnFirstLine.php',
            5,
            'First chained call must be on a new line when chain spans multiple lines',
            self::ERROR_SOURCE
        );
    }

    public function testFixerFirstCallOnSameLine(): void
    {
        static::assertFixerResult('FirstCallOnSameLine.php', 'FirstCallOnSameLineFixed.php');
    }

    public function testFixerFirstPropertyOnSameLine(): void
    {
        static::assertFixerResult('FirstPropertyOnSameLine.php', 'FirstPropertyOnSameLineFixed.php');
    }

    public function testFixerNullsafeFirstCallOnSameLine(): void
    {
        static::assertFixerResult('NullsafeFirstCallOnSameLine.php', 'NullsafeFirstCallOnSameLineFixed.php');
    }

    public function testFixerMultipleCallsOnFirstLine(): void
    {
        static::assertFixerResult('MultipleCallsOnFirstLine.php', 'MultipleCallsOnFirstLineFixed.php');
    }
}
