<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Arrays\DisallowShortArraySyntaxSpaces;

use Steevanb\PhpCodeSniffs\Tests\AbstractSniffTestCase;

class DisallowShortArraySyntaxSpacesSniffTest extends AbstractSniffTestCase
{
    private const string ERROR_MESSAGE_OPEN = 'Short array syntax should not begin with spaces';

    private const string ERROR_SOURCE_OPEN = 'Steevanb.Arrays.DisallowShortArraySyntaxSpaces.NoSpaceAtOpen';

    private const string ERROR_MESSAGE_CLOSE = 'Short array syntax should not end with spaces';

    private const string ERROR_SOURCE_CLOSE = 'Steevanb.Arrays.DisallowShortArraySyntaxSpaces.NoSpaceAtClose';

    protected static function getSniffName(): string
    {
        return 'Steevanb.Arrays.DisallowShortArraySyntaxSpaces';
    }

    public function testNoSpacesIsAllowed(): void
    {
        static::assertNoErrors('NoSpaces.php');
    }

    public function testSpaceAfterOpenIsDisallowed(): void
    {
        static::assertError('SpaceAfterOpen.php', 5, self::ERROR_MESSAGE_OPEN, self::ERROR_SOURCE_OPEN);
    }

    public function testSpaceBeforeCloseIsDisallowed(): void
    {
        static::assertError('SpaceBeforeClose.php', 5, self::ERROR_MESSAGE_CLOSE, self::ERROR_SOURCE_CLOSE);
    }

    public function testSpaceBothIsDisallowed(): void
    {
        $errors = static::getErrors('SpaceBoth.php');

        static::assertCount(2, $errors);
        static::assertSame(5, $errors[0]['line']);
        static::assertSame(self::ERROR_MESSAGE_OPEN, $errors[0]['message']);
        static::assertSame(self::ERROR_SOURCE_OPEN, $errors[0]['source']);
        static::assertSame(5, $errors[1]['line']);
        static::assertSame(self::ERROR_MESSAGE_CLOSE, $errors[1]['message']);
        static::assertSame(self::ERROR_SOURCE_CLOSE, $errors[1]['source']);
    }

    public function testMultiLineNoSpacesIsAllowed(): void
    {
        static::assertNoErrors('MultiLineNoSpaces.php');
    }
}
