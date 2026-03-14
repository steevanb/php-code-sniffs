<?php

declare(strict_types=1);

function commentBeforeReturn(): bool
{
    $a = true;
    // Anything else means the hooks have code, not allowed on one line.
    return false;
}
