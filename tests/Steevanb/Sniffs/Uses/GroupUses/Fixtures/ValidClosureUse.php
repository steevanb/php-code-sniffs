<?php

declare(strict_types=1);

$a = 1;
$b = 2;
$fn = function () use ($a, $b) {
    return $a + $b;
};
