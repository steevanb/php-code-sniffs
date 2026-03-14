<?php

declare(strict_types=1);

namespace App;

use App\Foo;

$fn = function () use ($foo) {
    return $foo;
};
