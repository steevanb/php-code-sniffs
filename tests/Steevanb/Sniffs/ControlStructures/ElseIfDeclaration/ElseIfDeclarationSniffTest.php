<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\ControlStructures\ElseIfDeclaration;

use Steevanb\PhpCodeSniffs\Tests\AbstractSniffTestCase;

class ElseIfDeclarationSniffTest extends AbstractSniffTestCase
{
    private const string ERROR_MESSAGE = 'Usage of ELSE IF is discouraged; use ELSEIF instead';

    private const string ERROR_SOURCE = 'Steevanb.ControlStructures.ElseIfDeclaration.NotAllowed';

    protected static function getSniffName(): string
    {
        return 'Steevanb.ControlStructures.ElseIfDeclaration';
    }

    public function testElseIfIsDisallowed(): void
    {
        static::assertError('WithElseIf.php', 7, self::ERROR_MESSAGE, self::ERROR_SOURCE);
    }

    public function testElseifIsAllowed(): void
    {
        static::assertNoErrors('WithElseif.php');
    }

    public function testElseIsAllowed(): void
    {
        static::assertNoErrors('WithElse.php');
    }

    public function testFixerElseIf(): void
    {
        static::assertFixerResult('WithElseIf.php', 'WithElseIfFixed.php');
    }
}
