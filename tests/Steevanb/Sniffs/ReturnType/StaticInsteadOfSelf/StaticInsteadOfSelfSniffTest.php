<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\ReturnType\StaticInsteadOfSelf;

use Steevanb\PhpCodeSniffs\Tests\AbstractSniffTestCase;

class StaticInsteadOfSelfSniffTest extends AbstractSniffTestCase
{
    private const string ERROR_MESSAGE = 'Use return type static instead of self';

    private const string ERROR_SOURCE = 'Steevanb.ReturnType.StaticInsteadOfSelf.ReturnTypeStatic';

    protected static function getSniffName(): string
    {
        return 'Steevanb.ReturnType.StaticInsteadOfSelf';
    }

    public function testPublicDynamicSelfIsDisallowed(): void
    {
        static::assertError('PublicDynamic.php', 9, self::ERROR_MESSAGE, self::ERROR_SOURCE);
    }

    public function testProtectedDynamicSelfIsDisallowed(): void
    {
        static::assertError('ProtectedDynamic.php', 9, self::ERROR_MESSAGE, self::ERROR_SOURCE);
    }

    public function testPrivateDynamicSelfIsAllowed(): void
    {
        static::assertNoErrors('PrivateDynamic.php');
    }

    public function testPublicStaticSelfIsAllowed(): void
    {
        static::assertNoErrors('PublicStatic.php');
    }

    public function testProtectedStaticSelfIsAllowed(): void
    {
        static::assertNoErrors('ProtectedStatic.php');
    }

    public function testPublicDynamicStaticReturnIsAllowed(): void
    {
        static::assertNoErrors('PublicDynamicStatic.php');
    }

    public function testFixerPublicDynamic(): void
    {
        static::assertFixerResult('PublicDynamic.php', 'PublicDynamicFixed.php');
    }

    public function testFixerProtectedDynamic(): void
    {
        static::assertFixerResult('ProtectedDynamic.php', 'ProtectedDynamicFixed.php');
    }
}
