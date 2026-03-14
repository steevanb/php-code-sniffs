<?php

declare(strict_types=1);

namespace App;

class Foo
{
    public string $myProperty = '';
}

$foo = new Foo();
$value = $foo->myProperty;
