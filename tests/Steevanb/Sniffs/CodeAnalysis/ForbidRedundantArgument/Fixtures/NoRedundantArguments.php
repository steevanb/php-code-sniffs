<?php

declare(strict_types=1);

function noRedundant(int $a = 1, string $b = 'hello'): void
{
}

noRedundant(42, 'world');
noRedundant(42);
noRedundant();
