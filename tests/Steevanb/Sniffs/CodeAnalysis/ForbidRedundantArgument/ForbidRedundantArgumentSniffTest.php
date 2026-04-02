<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\CodeAnalysis\ForbidRedundantArgument;

use Steevanb\PhpCodeSniffs\Tests\AbstractSniffTestCase;

class ForbidRedundantArgumentSniffTest extends AbstractSniffTestCase
{
    private const string ERROR_SOURCE = 'Steevanb.CodeAnalysis.ForbidRedundantArgument.RedundantArgument';

    protected static function getSniffName(): string
    {
        return 'Steevanb.CodeAnalysis.ForbidRedundantArgument';
    }

    public function testNoRedundantArguments(): void
    {
        static::assertNoErrors('NoRedundantArguments.php');
    }

    public function testFunctionTrailingRedundant(): void
    {
        $errors = static::getErrors('FunctionTrailingRedundant.php');

        static::assertCount(2, $errors);
        static::assertSame(9, $errors[0]['line']);
        static::assertSame(
            'Argument 2 passes the default value for parameter $b and should be removed',
            $errors[0]['message']
        );
        static::assertSame(self::ERROR_SOURCE, $errors[0]['source']);
        static::assertSame(9, $errors[1]['line']);
        static::assertSame(
            'Argument 3 passes the default value for parameter $c and should be removed',
            $errors[1]['message']
        );
    }

    public function testFunctionAllRedundant(): void
    {
        $errors = static::getErrors('FunctionAllRedundant.php');

        static::assertCount(2, $errors);
        static::assertSame(9, $errors[0]['line']);
        static::assertSame(9, $errors[1]['line']);
    }

    public function testMethodCallRedundant(): void
    {
        $errors = static::getErrors('MethodCallRedundant.php');

        static::assertCount(1, $errors);
        static::assertSame(13, $errors[0]['line']);
        static::assertSame(
            'Argument 2 passes the default value for parameter $verbose and should be removed',
            $errors[0]['message']
        );
    }

    public function testConstructorRedundant(): void
    {
        $errors = static::getErrors('ConstructorRedundant.php');

        static::assertCount(2, $errors);
        static::assertSame(14, $errors[0]['line']);
        static::assertSame(14, $errors[1]['line']);
    }

    public function testStaticCallRedundant(): void
    {
        $errors = static::getErrors('StaticCallRedundant.php');

        static::assertCount(1, $errors);
        static::assertSame(14, $errors[0]['line']);
    }

    public function testNamedArgumentRedundant(): void
    {
        $errors = static::getErrors('NamedArgumentRedundant.php');

        static::assertCount(2, $errors);
        static::assertSame(9, $errors[0]['line']);
        static::assertSame(
            'Named argument "a" passes the default value and should be removed',
            $errors[0]['message']
        );
        static::assertSame(9, $errors[1]['line']);
        static::assertSame(
            'Named argument "c" passes the default value and should be removed',
            $errors[1]['message']
        );
    }

    public function testNullDefault(): void
    {
        $errors = static::getErrors('NullDefault.php');

        static::assertCount(1, $errors);
        static::assertSame(9, $errors[0]['line']);
    }

    public function testFixerFunctionTrailingRedundant(): void
    {
        static::assertFixerResult('FunctionTrailingRedundant.php', 'FunctionTrailingRedundantFixed.php');
    }

    public function testFixerFunctionAllRedundant(): void
    {
        static::assertFixerResult('FunctionAllRedundant.php', 'FunctionAllRedundantFixed.php');
    }

    public function testFixerMethodCallRedundant(): void
    {
        static::assertFixerResult('MethodCallRedundant.php', 'MethodCallRedundantFixed.php');
    }

    public function testFixerConstructorRedundant(): void
    {
        static::assertFixerResult('ConstructorRedundant.php', 'ConstructorRedundantFixed.php');
    }

    public function testFixerStaticCallRedundant(): void
    {
        static::assertFixerResult('StaticCallRedundant.php', 'StaticCallRedundantFixed.php');
    }

    public function testFixerNamedArgumentRedundant(): void
    {
        static::assertFixerResult('NamedArgumentRedundant.php', 'NamedArgumentRedundantFixed.php');
    }

    public function testFixerNullDefault(): void
    {
        static::assertFixerResult('NullDefault.php', 'NullDefaultFixed.php');
    }

    public function testBuiltInFunction(): void
    {
        $errors = static::getErrors('BuiltInFunction.php');

        static::assertCount(2, $errors);
        static::assertSame(7, $errors[0]['line']);
        static::assertSame(
            'Argument 3 passes the default value for parameter $length and should be removed',
            $errors[0]['message']
        );
        static::assertSame(7, $errors[1]['line']);
        static::assertSame(
            'Argument 4 passes the default value for parameter $preserve_keys and should be removed',
            $errors[1]['message']
        );
    }

    public function testFixerBuiltInFunction(): void
    {
        static::assertFixerResult('BuiltInFunction.php', 'BuiltInFunctionFixed.php');
    }

    public function testAttributeRedundant(): void
    {
        $errors = static::getErrors('AttributeRedundant.php');

        static::assertCount(1, $errors);
        static::assertSame(11, $errors[0]['line']);
        static::assertSame(
            'Argument 2 passes the default value for parameter $validateArgumentCount and should be removed',
            $errors[0]['message']
        );
    }

    public function testAttributeNoRedundant(): void
    {
        static::assertNoErrors('AttributeNoRedundant.php');
    }

    public function testFixerAttributeRedundant(): void
    {
        static::assertFixerResult('AttributeRedundant.php', 'AttributeRedundantFixed.php');
    }
}
