<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Classes\ClassMemberOrder;

use Steevanb\PhpCodeSniffs\Tests\AbstractSniffTestCase;

class ClassMemberOrderSniffTest extends AbstractSniffTestCase
{
    private const string ERROR_SOURCE = 'Steevanb.Classes.ClassMemberOrder.InvalidOrder';

    protected static function getSniffName(): string
    {
        return 'Steevanb.Classes.ClassMemberOrder';
    }

    public function testCorrectOrderIsAllowed(): void
    {
        static::assertNoErrors('CorrectOrder.php');
    }

    public function testPropertyBeforeUseIsDisallowed(): void
    {
        static::assertError(
            'PropertyBeforeUse.php',
            15,
            'trait use must be before public property',
            self::ERROR_SOURCE
        );
    }

    public function testMethodBeforePropertyIsDisallowed(): void
    {
        static::assertError(
            'MethodBeforeProperty.php',
            13,
            'public property must be before public method',
            self::ERROR_SOURCE
        );
    }

    public function testPrivateBeforePublicIsDisallowed(): void
    {
        static::assertError(
            'PrivateBeforePublic.php',
            13,
            'public method must be before private method',
            self::ERROR_SOURCE
        );
    }

    public function testConstantBeforeAbstractIsDisallowed(): void
    {
        static::assertError(
            'ConstantBeforeAbstract.php',
            11,
            'abstract public method must be before public constant',
            self::ERROR_SOURCE
        );
    }

    public function testConstructBeforePropertyIsDisallowed(): void
    {
        static::assertError(
            'ConstructBeforeProperty.php',
            13,
            'public property must be before __construct',
            self::ERROR_SOURCE
        );
    }

    public function testStaticMethodBeforeConstantIsDisallowed(): void
    {
        static::assertError(
            'StaticMethodBeforeConstant.php',
            13,
            'public constant must be before public static method',
            self::ERROR_SOURCE
        );
    }
}
