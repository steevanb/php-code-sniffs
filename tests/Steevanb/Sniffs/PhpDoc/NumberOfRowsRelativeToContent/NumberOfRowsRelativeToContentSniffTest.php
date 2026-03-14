<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\PhpDoc\NumberOfRowsRelativeToContent;

use Steevanb\PhpCodeSniffs\Tests\AbstractSniffTestCase;

class NumberOfRowsRelativeToContentSniffTest extends AbstractSniffTestCase
{
    private const string ERROR_MESSAGE = 'Single-line PHPDoc should not span multiple lines';

    private const string ERROR_SOURCE = 'Steevanb.PhpDoc.NumberOfRowsRelativeToContent.PHPDocOnOneLine';

    protected static function getSniffName(): string
    {
        return 'Steevanb.PhpDoc.NumberOfRowsRelativeToContent';
    }

    public function testOneLineIsAllowed(): void
    {
        static::assertNoErrors('OneLine.php');
    }

    public function testMultiLineMultipleContentIsAllowed(): void
    {
        static::assertNoErrors('MultiLineMultipleContent.php');
    }

    public function testThreeLinesOneContentIsDisallowed(): void
    {
        static::assertError('ThreeLinesOneContent.php', 9, self::ERROR_MESSAGE, self::ERROR_SOURCE);
    }

    public function testFixerThreeLinesOneContent(): void
    {
        static::assertFixerResult('ThreeLinesOneContent.php', 'ThreeLinesOneContentFixed.php');
    }
}
