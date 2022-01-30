<?php

declare(strict_types=1);

namespace steevanb\PhpCodeSniffs\Steevanb;

class PhpVersionId
{
    public static function get(): int
    {
        if (array_key_exists('PHPCS_PHP_VERSION_ID', $_ENV)) {
            $return = (int) $_ENV['PHPCS_PHP_VERSION_ID'];
        } elseif (array_key_exists('PHPCS_PHP_VERSION_ID', $_SERVER)) {
            $return = (int) $_SERVER['PHPCS_PHP_VERSION_ID'];
        } else {
            $return = PHP_VERSION_ID;
        }

        return $return;
    }
}
