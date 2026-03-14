<?php

declare(strict_types=1);

/** @param int[] $items */
function onlyReturnInForeach(array $items): int
{
    foreach ($items as $item) {
        return $item;
    }

    return 0;
}
