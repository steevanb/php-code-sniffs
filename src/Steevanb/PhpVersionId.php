<?php

declare(strict_types=1);

namespace steevanb\PhpCodeSniffs\Steevanb;

class PhpVersionId
{
    public static function get(): int
    {
        return
            array_key_exists('PHPCS_PHP_VERSION_ID', $_ENV)
                ? (int) $_ENV['PHPCS_PHP_VERSION_ID']
                : PHP_VERSION_ID;
    }
}
