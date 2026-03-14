<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Functions\StaticClosure;

use Steevanb\PhpCodeSniffs\Tests\AbstractSniffTestCase;

class StaticClosureSniffTest extends AbstractSniffTestCase
{
    private const string ERROR_SOURCE = 'Steevanb.Functions.StaticClosure.MustBeStatic';

    protected static function getSniffName(): string
    {
        return 'Steevanb.Functions.StaticClosure';
    }

    public function testStaticClosureIsAllowed(): void
    {
        static::assertNoErrors('StaticClosure.php');
    }

    public function testStaticArrowFunctionIsAllowed(): void
    {
        static::assertNoErrors('StaticArrowFunction.php');
    }

    public function testClosureWithThisIsAllowed(): void
    {
        static::assertNoErrors('ClosureWithThis.php');
    }

    public function testArrowFunctionWithThisIsAllowed(): void
    {
        static::assertNoErrors('ArrowFunctionWithThis.php');
    }

    public function testNonStaticClosureWithoutThisIsDisallowed(): void
    {
        static::assertError(
            'NonStaticClosureWithoutThis.php',
            5,
            'Closure does not use "$this" and should be declared static',
            self::ERROR_SOURCE
        );
    }

    public function testNonStaticArrowFunctionWithoutThisIsDisallowed(): void
    {
        static::assertError(
            'NonStaticArrowFunctionWithoutThis.php',
            5,
            'Arrow function does not use "$this" and should be declared static',
            self::ERROR_SOURCE
        );
    }

    public function testNestedClosureWithThisInInnerOnly(): void
    {
        $errors = static::getErrors('NestedClosureWithThisInInner.php');

        static::assertCount(1, $errors);
        static::assertSame(11, $errors[0]['line']);
        static::assertSame(
            'Closure does not use "$this" and should be declared static',
            $errors[0]['message']
        );
        static::assertSame(self::ERROR_SOURCE, $errors[0]['source']);
    }

    public function testFixerClosure(): void
    {
        static::assertFixerResult('NonStaticClosureWithoutThis.php', 'NonStaticClosureWithoutThisFixed.php');
    }

    public function testFixerArrowFunction(): void
    {
        static::assertFixerResult(
            'NonStaticArrowFunctionWithoutThis.php',
            'NonStaticArrowFunctionWithoutThisFixed.php'
        );
    }

    public function testFixerNestedClosure(): void
    {
        static::assertFixerResult('NestedClosureWithThisInInner.php', 'NestedClosureWithThisInInnerFixed.php');
    }
}
