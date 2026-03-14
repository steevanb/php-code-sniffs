<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Classes\ClassMemberOrder\Fixtures;

trait FooTrait
{
}

abstract class CorrectOrder
{
    use FooTrait;

    abstract public string $abstractPublicProp { get; }

    abstract protected string $abstractProtectedProp { get; }

    abstract public function abstractPublicMethod(): void;

    abstract protected function abstractProtectedMethod(): void;

    public const string PUBLIC_CONST = 'a';

    protected const string PROTECTED_CONST = 'b';

    private const string PRIVATE_CONST = 'c';

    public static function staticPublicMethod(): void
    {
    }

    protected static function staticProtectedMethod(): void
    {
    }

    private static function staticPrivateMethod(): void
    {
    }

    public string $publicProp = 'a';

    protected string $protectedProp = 'b';

    private string $privateProp = 'c';

    public function __construct()
    {
    }

    public function __toString(): string
    {
        return '';
    }

    public function publicMethod(): void
    {
    }

    protected function protectedMethod(): void
    {
    }

    private function privateMethod(): void
    {
    }
}
