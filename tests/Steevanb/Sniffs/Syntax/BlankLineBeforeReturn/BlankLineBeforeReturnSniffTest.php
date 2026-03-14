<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Syntax\BlankLineBeforeReturn;

use Steevanb\PhpCodeSniffs\Tests\AbstractSniffTestCase;

class BlankLineBeforeReturnSniffTest extends AbstractSniffTestCase
{
    private const string ERROR_MESSAGE = 'Add a blank line before return keyword';

    private const string ERROR_SOURCE = 'Steevanb.Syntax.BlankLineBeforeReturn.BlankLineBeforeReturnKeyword';

    protected static function getSniffName(): string
    {
        return 'Steevanb.Syntax.BlankLineBeforeReturn';
    }

    public function testWithBlankLineIsAllowed(): void
    {
        static::assertNoErrors('WithBlankLine.php');
    }

    public function testOnlyReturnInFunctionIsAllowed(): void
    {
        static::assertNoErrors('OnlyReturnInFunction.php');
    }

    public function testOnlyReturnInIfIsAllowed(): void
    {
        static::assertNoErrors('OnlyReturnInIf.php');
    }

    public function testOnlyReturnInForeachIsAllowed(): void
    {
        static::assertNoErrors('OnlyReturnInForeach.php');
    }

    public function testOnlyReturnInClosureIsAllowed(): void
    {
        static::assertNoErrors('OnlyReturnInClosure.php');
    }

    public function testCommentBeforeReturnIsAllowed(): void
    {
        static::assertNoErrors('CommentBeforeReturn.php');
    }

    public function testDocCommentBeforeReturnIsAllowed(): void
    {
        static::assertNoErrors('DocCommentBeforeReturn.php');
    }

    public function testBlankLineAndCommentBeforeReturnIsAllowed(): void
    {
        static::assertNoErrors('BlankLineAndCommentBeforeReturn.php');
    }

    public function testWithoutBlankLineIsDisallowed(): void
    {
        static::assertError('WithoutBlankLine.php', 8, self::ERROR_MESSAGE, self::ERROR_SOURCE);
    }

    public function testWithoutBlankLineInIfIsDisallowed(): void
    {
        static::assertError('WithoutBlankLineInIf.php', 10, self::ERROR_MESSAGE, self::ERROR_SOURCE);
    }

    public function testFixerWithoutBlankLine(): void
    {
        static::assertFixerResult('WithoutBlankLine.php', 'WithoutBlankLineFixed.php');
    }

    public function testFixerWithoutBlankLineInIf(): void
    {
        static::assertFixerResult('WithoutBlankLineInIf.php', 'WithoutBlankLineInIfFixed.php');
    }
}
