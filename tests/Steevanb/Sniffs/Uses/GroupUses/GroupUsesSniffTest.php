<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Uses\GroupUses;

use Steevanb\PhpCodeSniffs\Tests\AbstractSniffTestCase;

class GroupUsesSniffTest extends AbstractSniffTestCase
{
    private const string ERROR_SOURCE_MUST_GROUP = 'Steevanb.Uses.GroupUses.MustGroup';

    private const string ERROR_SOURCE_ONE_USE_PER_LINE = 'Steevanb.Uses.GroupUses.OneUsePerLine';

    private const string ERROR_SOURCE_CLOSE_BRACE = 'Steevanb.Uses.GroupUses.CloseBraceOwnLine';

    private const string ERROR_SOURCE_WRONG_GROUP_PREFIX = 'Steevanb.Uses.GroupUses.WrongGroupPrefix';

    private const string ERROR_SOURCE_DUPLICATE_GROUP = 'Steevanb.Uses.GroupUses.DuplicateGroup';

    protected static function getSniffName(): string
    {
        return 'Steevanb.Uses.GroupUses';
    }

    protected static function getRuleset(): ?string
    {
        return 'ruleset.xml';
    }

    public function testValidGroupedUseIsAllowed(): void
    {
        static::assertNoErrors('ValidGroupedUse.php');
    }

    public function testValidSingleUseIsAllowed(): void
    {
        static::assertNoErrors('ValidSingleUse.php');
    }

    public function testValidDifferentPrefixesIsAllowed(): void
    {
        static::assertNoErrors('ValidDifferentPrefixes.php');
    }

    public function testValidTraitUseIsAllowed(): void
    {
        static::assertNoErrors('ValidTraitUse.php');
    }

    public function testValidClosureUseIsAllowed(): void
    {
        static::assertNoErrors('ValidClosureUse.php');
    }

    public function testValidNonConfiguredPrefixIsAllowed(): void
    {
        static::assertNoErrors('ValidNonConfiguredPrefix.php');
    }

    public function testUngroupedSamePrefixIsDisallowed(): void
    {
        $errors = static::getErrors('UngroupedSamePrefix.php');

        static::assertCount(2, $errors);
        static::assertSame(5, $errors[0]['line']);
        static::assertSame('Use "App\Foo\Bar" must be grouped under "App\Foo"', $errors[0]['message']);
        static::assertSame(self::ERROR_SOURCE_MUST_GROUP, $errors[0]['source']);
        static::assertSame(6, $errors[1]['line']);
        static::assertSame('Use "App\Foo\Baz" must be grouped under "App\Foo"', $errors[1]['message']);
        static::assertSame(self::ERROR_SOURCE_MUST_GROUP, $errors[1]['source']);
    }

    public function testGroupedOnSameLineIsDisallowed(): void
    {
        $errors = static::getErrors('GroupedOnSameLine.php');

        static::assertGreaterThanOrEqual(1, count($errors));
        static::assertSame(self::ERROR_SOURCE_ONE_USE_PER_LINE, $errors[0]['source']);
    }

    public function testCloseBraceNotOwnLineIsDisallowed(): void
    {
        static::assertError(
            'CloseBraceNotOwnLine.php',
            7,
            'Use group close brace must be on its own line',
            self::ERROR_SOURCE_CLOSE_BRACE
        );
    }

    public function testValidGroupedUseWithSubNamespaceIsAllowed(): void
    {
        static::assertNoErrors('ValidGroupedUseWithSubNamespace.php');
    }

    public function testGroupedUnderWrongPrefixIsDisallowed(): void
    {
        static::assertError(
            'GroupedUnderWrongPrefix.php',
            5,
            'Use group "App\Foo\Bar" must be grouped under "App\Foo" (App\Foo\Bar\Baz, App\Foo\Bar\Qux)',
            self::ERROR_SOURCE_WRONG_GROUP_PREFIX
        );
    }

    public function testGroupedUnderNonConfiguredPrefixIsDisallowed(): void
    {
        static::assertError(
            'GroupedUnderNonConfiguredPrefix.php',
            5,
            'Use group "App\Other" must be grouped under "App" (App\Other\Bar, App\Other\Baz)',
            self::ERROR_SOURCE_WRONG_GROUP_PREFIX
        );
    }

    public function testGroupedAndUngroupedSamePrefixIsDisallowed(): void
    {
        static::assertError(
            'GroupedAndUngroupedSamePrefix.php',
            8,
            'Use "App\Foo\Baz" must be grouped under "App\Foo"',
            self::ERROR_SOURCE_MUST_GROUP
        );
    }

    public function testDuplicateGroupIsDisallowed(): void
    {
        static::assertError(
            'DuplicateGroup.php',
            8,
            'Use group "App\Foo" must be merged with the previous one',
            self::ERROR_SOURCE_DUPLICATE_GROUP
        );
    }
}
