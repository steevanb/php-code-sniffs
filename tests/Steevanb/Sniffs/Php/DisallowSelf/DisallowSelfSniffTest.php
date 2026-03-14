<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Php\DisallowSelf;

use Steevanb\PhpCodeSniffs\Tests\AbstractSniffTestCase;

class DisallowSelfSniffTest extends AbstractSniffTestCase
{
    private const string ERROR_MESSAGE =
        'Use static:: instead of self:: for public/protected members (Late Static Binding)';

    private const string ERROR_SOURCE = 'Steevanb.Php.DisallowSelf.NotAllowed';

    protected static function getSniffName(): string
    {
        return 'Steevanb.Php.DisallowSelf';
    }

    public function testSelfCallPublicMethodIsDisallowed(): void
    {
        static::assertError('SelfCallPublicMethod.php', 15, self::ERROR_MESSAGE, self::ERROR_SOURCE);
    }

    public function testSelfCallProtectedMethodIsDisallowed(): void
    {
        static::assertError('SelfCallProtectedMethod.php', 15, self::ERROR_MESSAGE, self::ERROR_SOURCE);
    }

    public function testSelfCallPrivateMethodIsAllowed(): void
    {
        static::assertNoErrors('SelfCallPrivateMethod.php');
    }

    public function testSelfInFunctionParameterIsAllowed(): void
    {
        static::assertNoErrors('SelfInFunctionParameter.php');
    }

    public function testStaticCallPublicMethodIsAllowed(): void
    {
        static::assertNoErrors('StaticCallPublicMethod.php');
    }

    public function testSelfAccessPublicConstantIsDisallowed(): void
    {
        static::assertError('SelfAccessPublicConstant.php', 13, self::ERROR_MESSAGE, self::ERROR_SOURCE);
    }

    public function testSelfAccessProtectedConstantIsDisallowed(): void
    {
        static::assertError('SelfAccessProtectedConstant.php', 13, self::ERROR_MESSAGE, self::ERROR_SOURCE);
    }

    public function testSelfAccessPrivateConstantIsAllowed(): void
    {
        static::assertNoErrors('SelfAccessPrivateConstant.php');
    }

    public function testSelfAccessPropertyIsAllowed(): void
    {
        static::assertNoErrors('SelfAccessProperty.php');
    }

    public function testFixerSelfCallPublicMethod(): void
    {
        static::assertFixerResult('SelfCallPublicMethod.php', 'SelfCallPublicMethodFixed.php');
    }

    public function testFixerSelfCallProtectedMethod(): void
    {
        static::assertFixerResult('SelfCallProtectedMethod.php', 'SelfCallProtectedMethodFixed.php');
    }

    public function testFixerSelfAccessPublicConstant(): void
    {
        static::assertFixerResult('SelfAccessPublicConstant.php', 'SelfAccessPublicConstantFixed.php');
    }

    public function testFixerSelfAccessProtectedConstant(): void
    {
        static::assertFixerResult('SelfAccessProtectedConstant.php', 'SelfAccessProtectedConstantFixed.php');
    }
}
