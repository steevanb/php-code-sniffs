<?php

declare(strict_types=1);

function onlyReturnInIf(): int
{
    $a = 1;
    if ($a === 1) {
        return $a;
    }

    return 0;
}
