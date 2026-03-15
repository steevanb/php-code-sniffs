<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Properties\EmptyPropertyHookOnSameLine;

use Steevanb\PhpCodeSniffs\Tests\AbstractSniffTestCase;

class EmptyPropertyHookOnSameLineSniffTest extends AbstractSniffTestCase
{
    private const string ERROR_MESSAGE = 'Empty property hooks must be on the same line as the property declaration';

    private const string ERROR_SOURCE = 'Steevanb.Properties.EmptyPropertyHookOnSameLine.NotOnSameLine';

    protected static function getSniffName(): string
    {
        return 'Steevanb.Properties.EmptyPropertyHookOnSameLine';
    }

    public function testOnSameLineIsAllowed(): void
    {
        static::assertNoErrors('OnSameLine.php');
    }

    public function testOnMultipleLinesIsDisallowed(): void
    {
        static::assertError('OnMultipleLines.php', 9, self::ERROR_MESSAGE, self::ERROR_SOURCE);
    }

    public function testFixerOnMultipleLines(): void
    {
        static::assertFixerResult('OnMultipleLines.php', 'OnMultipleLinesFixed.php');
    }

    public function testNonEmptyHookIsAllowed(): void
    {
        static::assertNoErrors('NonEmptyHook.php');
    }

    public function testGetOnlyOnSameLineIsAllowed(): void
    {
        static::assertNoErrors('GetOnlyOnSameLine.php');
    }

    public function testGetOnlyOnMultipleLinesIsDisallowed(): void
    {
        static::assertError('GetOnlyOnMultipleLines.php', 9, self::ERROR_MESSAGE, self::ERROR_SOURCE);
    }

    public function testFixerGetOnlyOnMultipleLines(): void
    {
        static::assertFixerResult('GetOnlyOnMultipleLines.php', 'GetOnlyOnMultipleLinesFixed.php');
    }
}
