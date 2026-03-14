<?php

declare(strict_types=1);

namespace App;

class Foo
{
    public function my_method(): void
    {
    }
}

$foo = new Foo();
$foo->my_method();
