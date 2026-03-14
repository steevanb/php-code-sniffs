<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Namespaces\UseDeclaration;

use Steevanb\PhpCodeSniffs\Tests\AbstractSniffTestCase;

class UseDeclarationSniffTest extends AbstractSniffTestCase
{
    private const string ERROR_SPACE_AFTER_USE_MESSAGE = 'There must be a single space after the USE keyword';

    private const string ERROR_SPACE_AFTER_USE_SOURCE = 'Steevanb.Namespaces.UseDeclaration.SpaceAfterUse';

    private const string ERROR_USE_AFTER_NAMESPACE_MESSAGE =
        'USE declarations must go after the first namespace declaration';

    private const string ERROR_USE_AFTER_NAMESPACE_SOURCE = 'Steevanb.Namespaces.UseDeclaration.UseAfterNamespace';

    private const string ERROR_SPACE_AFTER_LAST_USE_SOURCE =
        'Steevanb.Namespaces.UseDeclaration.SpaceAfterLastUse';

    protected static function getSniffName(): string
    {
        return 'Steevanb.Namespaces.UseDeclaration';
    }

    public function testValidSingleUseIsAllowed(): void
    {
        static::assertNoErrors('ValidSingleUse.php');
    }

    public function testValidGroupedUseIsAllowed(): void
    {
        static::assertNoErrors('ValidGroupedUse.php');
    }

    public function testValidMultipleUsesIsAllowed(): void
    {
        static::assertNoErrors('ValidMultipleUses.php');
    }

    public function testClosureUseIsIgnored(): void
    {
        static::assertNoErrors('ValidClosureUse.php');
    }

    public function testTraitUseIsIgnored(): void
    {
        static::assertNoErrors('ValidTraitUse.php');
    }

    public function testMultipleSpacesAfterUseIsDisallowed(): void
    {
        static::assertError(
            'MultipleSpacesAfterUse.php',
            7,
            self::ERROR_SPACE_AFTER_USE_MESSAGE,
            self::ERROR_SPACE_AFTER_USE_SOURCE
        );
    }

    public function testNoBlankLineAfterUseIsDisallowed(): void
    {
        static::assertError(
            'NoBlankLineAfterUse.php',
            7,
            'There must be one blank line after the last USE statement; 0 found',
            self::ERROR_SPACE_AFTER_LAST_USE_SOURCE
        );
    }

    public function testTooManyBlankLinesAfterUseIsDisallowed(): void
    {
        static::assertError(
            'TooManyBlankLinesAfterUse.php',
            7,
            'There must be one blank line after the last USE statement; 3 found',
            self::ERROR_SPACE_AFTER_LAST_USE_SOURCE
        );
    }

    public function testUseInSecondNamespaceIsDisallowed(): void
    {
        static::assertError(
            'UseInSecondNamespace.php',
            13,
            self::ERROR_USE_AFTER_NAMESPACE_MESSAGE,
            self::ERROR_USE_AFTER_NAMESPACE_SOURCE
        );
    }

    public function testFixerMultipleSpacesAfterUse(): void
    {
        static::assertFixerResult('MultipleSpacesAfterUse.php', 'MultipleSpacesAfterUseFixed.php');
    }

    public function testFixerNoBlankLineAfterUse(): void
    {
        static::assertFixerResult('NoBlankLineAfterUse.php', 'NoBlankLineAfterUseFixed.php');
    }

    public function testFixerTooManyBlankLinesAfterUse(): void
    {
        static::assertFixerResult('TooManyBlankLinesAfterUse.php', 'TooManyBlankLinesAfterUseFixed.php');
    }
}
