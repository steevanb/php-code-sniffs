<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests\Steevanb\Sniffs\Syntax\NewWithoutParentheses\Fixtures;

class Foo
{
    public function add(Bar $bar): static
    {
        return $this;
    }

    public function run(): void
    {
    }
}

class Bar
{
    public function __construct(private string $name = '')
    {
    }

    public function setName(string $name): static
    {
        return $this;
    }
}

new Foo()
    ->add(
        new Bar('first')
            ->setName('one')
    )
    ->add(
        new Bar('second')
            ->setName('two')
    )
    ->run();
