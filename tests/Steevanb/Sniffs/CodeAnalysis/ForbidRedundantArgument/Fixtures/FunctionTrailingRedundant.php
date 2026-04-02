<?php

declare(strict_types=1);

function trailingDefaults(int $a = 1, string $b = 'hello', bool $c = true): void
{
}

trailingDefaults(42, 'hello', true);
