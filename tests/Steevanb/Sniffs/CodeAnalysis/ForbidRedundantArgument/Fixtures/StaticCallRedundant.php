<?php

declare(strict_types=1);

class Helper
{
    public static function format(string $value, string $prefix = '#'): string
    {
        return $prefix . $value;
    }

    public static function caller(): void
    {
        self::format('test', '#');
    }
}
