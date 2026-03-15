<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Syntax\NewWithoutParentheses;

use Steevanb\PhpCodeSniffs\Tests\AbstractSniffTestCase;

class NewWithoutParenthesesSniffTest extends AbstractSniffTestCase
{
    private const string ERROR_MESSAGE = 'Since PHP 8.4, wrapping parentheses around new are unnecessary for chaining';

    private const string ERROR_SOURCE = 'Steevanb.Syntax.NewWithoutParentheses.ParenthesesNotAllowed';

    protected static function getSniffName(): string
    {
        return 'Steevanb.Syntax.NewWithoutParentheses';
    }

    public function testWithParenthesesIsDisallowed(): void
    {
        static::assertError('WithParentheses.php', 14, self::ERROR_MESSAGE, self::ERROR_SOURCE);
    }

    public function testWithoutParenthesesIsAllowed(): void
    {
        static::assertNoErrors('WithoutParentheses.php');
    }

    public function testWithParenthesesStaticIsDisallowed(): void
    {
        static::assertError('WithParenthesesStatic.php', 12, self::ERROR_MESSAGE, self::ERROR_SOURCE);
    }

    public function testWithParenthesesNullsafeIsDisallowed(): void
    {
        static::assertError('WithParenthesesNullsafe.php', 15, self::ERROR_MESSAGE, self::ERROR_SOURCE);
    }

    public function testNewWithoutChainingIsAllowed(): void
    {
        static::assertNoErrors('NewWithoutChaining.php');
    }

    public function testWithoutParenthesesMultilineChainingIsAllowed(): void
    {
        static::assertNoErrors('WithoutParenthesesMultilineChaining.php');
    }

    public function testFixerWithParentheses(): void
    {
        static::assertFixerResult('WithParentheses.php', 'WithParenthesesFixed.php');
    }

    public function testFixerWithParenthesesStatic(): void
    {
        static::assertFixerResult('WithParenthesesStatic.php', 'WithParenthesesStaticFixed.php');
    }

    public function testFixerWithParenthesesNullsafe(): void
    {
        static::assertFixerResult('WithParenthesesNullsafe.php', 'WithParenthesesNullsafeFixed.php');
    }
}
