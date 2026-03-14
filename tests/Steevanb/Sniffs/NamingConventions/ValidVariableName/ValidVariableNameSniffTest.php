<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\NamingConventions\ValidVariableName;

use Steevanb\PhpCodeSniffs\Tests\AbstractSniffTestCase;

class ValidVariableNameSniffTest extends AbstractSniffTestCase
{
    private const string ERROR_NOT_CAMEL_CAPS_MESSAGE = 'Variable "%s" is not in valid camel caps format';

    private const string ERROR_NOT_CAMEL_CAPS_SOURCE =
        'Steevanb.NamingConventions.ValidVariableName.NotCamelCaps';

    private const string ERROR_PUBLIC_HAS_UNDERSCORE_SOURCE =
        'Steevanb.NamingConventions.ValidVariableName.PublicHasUnderscore';

    private const string ERROR_MEMBER_VAR_NOT_CAMEL_CAPS_SOURCE =
        'Steevanb.NamingConventions.ValidVariableName.MemberVarNotCamelCaps';

    private const string ERROR_STRING_VAR_NOT_CAMEL_CAPS_SOURCE =
        'Steevanb.NamingConventions.ValidVariableName.StringVarNotCamelCaps';

    protected static function getSniffName(): string
    {
        return 'Steevanb.NamingConventions.ValidVariableName';
    }

    public function testValidCamelCaseIsAllowed(): void
    {
        static::assertNoErrors('ValidCamelCase.php');
    }

    public function testPhpReservedVariableIsAllowed(): void
    {
        static::assertNoErrors('ValidPhpReservedVariable.php');
    }

    public function testValidObjectPropertyIsAllowed(): void
    {
        static::assertNoErrors('ValidObjectProperty.php');
    }

    public function testObjectMethodCallIsNotChecked(): void
    {
        static::assertNoErrors('ValidObjectMethodCall.php');
    }

    public function testValidMemberVariableIsAllowed(): void
    {
        static::assertNoErrors('ValidMemberVariable.php');
    }

    public function testValidVariableInStringIsAllowed(): void
    {
        static::assertNoErrors('ValidVariableInString.php');
    }

    public function testPhpReservedVariableInStringIsAllowed(): void
    {
        static::assertNoErrors('ValidPhpReservedVariableInString.php');
    }

    public function testSnakeCaseVariableIsDisallowed(): void
    {
        static::assertError(
            'SnakeCaseVariable.php',
            5,
            sprintf(self::ERROR_NOT_CAMEL_CAPS_MESSAGE, 'my_variable'),
            self::ERROR_NOT_CAMEL_CAPS_SOURCE
        );
    }

    public function testSnakeCaseObjectPropertyIsDisallowed(): void
    {
        static::assertError(
            'SnakeCaseObjectProperty.php',
            13,
            sprintf(self::ERROR_NOT_CAMEL_CAPS_MESSAGE, 'my_prop'),
            self::ERROR_NOT_CAMEL_CAPS_SOURCE
        );
    }

    public function testPublicMemberWithUnderscoreIsDisallowed(): void
    {
        static::assertError(
            'PublicMemberWithUnderscore.php',
            9,
            'Public member variable "_myProperty" must not contain a leading underscore',
            self::ERROR_PUBLIC_HAS_UNDERSCORE_SOURCE
        );
    }

    public function testPrivateMemberNotCamelCapsIsDisallowed(): void
    {
        static::assertError(
            'PrivateMemberNotCamelCaps.php',
            9,
            sprintf('Member variable "%s" is not in valid camel caps format', '_my_property'),
            self::ERROR_MEMBER_VAR_NOT_CAMEL_CAPS_SOURCE
        );
    }

    public function testSnakeCaseVariableInStringIsDisallowed(): void
    {
        static::assertError(
            'SnakeCaseVariableInString.php',
            6,
            sprintf(self::ERROR_NOT_CAMEL_CAPS_MESSAGE, 'my_var'),
            self::ERROR_STRING_VAR_NOT_CAMEL_CAPS_SOURCE
        );
    }
}
