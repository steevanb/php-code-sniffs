<?php

declare(strict_types=1);

namespace App;

trait FooTrait
{
}

trait BarTrait
{
}

class Baz
{
    use FooTrait;
    use BarTrait;
}
