<?php

declare(strict_types=1);

function withoutBlankLineInIf(): int
{
    $a = 1;
    if ($a === 1) {
        $a = 2;
        return $a;
    }

    return 0;
}
