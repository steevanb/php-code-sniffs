<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Properties\ConstantVisibility;

use Steevanb\PhpCodeSniffs\Tests\AbstractSniffTestCase;

class ConstantVisibilitySniffTest extends AbstractSniffTestCase
{
    private const string ERROR_MESSAGE = 'Visibility must be declared on all constants';

    private const string ERROR_SOURCE = 'Steevanb.Properties.ConstantVisibility.NotFound';

    protected static function getSniffName(): string
    {
        return 'Steevanb.Properties.ConstantVisibility';
    }

    public function testPublicConstantIsAllowed(): void
    {
        static::assertNoErrors('PublicConstant.php');
    }

    public function testProtectedConstantIsAllowed(): void
    {
        static::assertNoErrors('ProtectedConstant.php');
    }

    public function testPrivateConstantIsAllowed(): void
    {
        static::assertNoErrors('PrivateConstant.php');
    }

    public function testNoVisibilityIsDisallowed(): void
    {
        static::assertError('NoVisibility.php', 9, self::ERROR_MESSAGE, self::ERROR_SOURCE);
    }
}
