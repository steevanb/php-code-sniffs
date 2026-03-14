<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Php\DisallowMultipleEmptyLines;

use Steevanb\PhpCodeSniffs\Tests\AbstractSniffTestCase;

class DisallowMultipleEmptyLinesSniffTest extends AbstractSniffTestCase
{
    private const string ERROR_MESSAGE = 'Multiple empty lines are not allowed';

    private const string ERROR_SOURCE = 'Steevanb.Php.DisallowMultipleEmptyLines.NotAllowed';

    protected static function getSniffName(): string
    {
        return 'Steevanb.Php.DisallowMultipleEmptyLines';
    }

    public function testNoEmptyLineIsAllowed(): void
    {
        static::assertNoErrors('NoEmptyLine.php');
    }

    public function testSingleEmptyLineIsAllowed(): void
    {
        static::assertNoErrors('SingleEmptyLine.php');
    }

    public function testTwoEmptyLinesIsDisallowed(): void
    {
        static::assertError('TwoEmptyLines.php', 4, self::ERROR_MESSAGE, self::ERROR_SOURCE);
    }

    public function testThreeEmptyLinesIsDisallowed(): void
    {
        $errors = static::getErrors('ThreeEmptyLines.php');

        static::assertCount(2, $errors);
        static::assertSame(4, $errors[0]['line']);
        static::assertSame(self::ERROR_MESSAGE, $errors[0]['message']);
        static::assertSame(self::ERROR_SOURCE, $errors[0]['source']);
        static::assertSame(5, $errors[1]['line']);
        static::assertSame(self::ERROR_MESSAGE, $errors[1]['message']);
        static::assertSame(self::ERROR_SOURCE, $errors[1]['source']);
    }

    public function testFixerTwoEmptyLines(): void
    {
        static::assertFixerResult('TwoEmptyLines.php', 'TwoEmptyLinesFixed.php');
    }

    public function testFixerThreeEmptyLines(): void
    {
        static::assertFixerResult('ThreeEmptyLines.php', 'ThreeEmptyLinesFixed.php');
    }
}
