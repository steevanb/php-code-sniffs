<?php

declare(strict_types=1);

namespace App;

class Foo
{
    public string $myProp = '';
}

$foo = new Foo();
$value = $foo->my_prop;
