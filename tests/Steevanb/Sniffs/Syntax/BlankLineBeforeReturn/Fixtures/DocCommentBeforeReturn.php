<?php

declare(strict_types=1);

function docCommentBeforeReturn(): bool
{
    $a = true;
    /** @var bool $result */
    return false;
}
