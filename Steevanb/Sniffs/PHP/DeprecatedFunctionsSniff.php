<?php

declare(strict_types=1);

/**
 * Generic_Sniffs_PHP_DeprecatedFunctionsSniff fork
 * Allow some deprecated functions to be used
 */
class Steevanb_Sniffs_PHP_DeprecatedFunctionsSniff extends Generic_Sniffs_PHP_DeprecatedFunctionsSniff
{
    /** @var string[] */
    protected static $allowedDeprecatedFunctions = [];

    public static function addAllowDeprecatedFunction(string $name): void
    {
        static::$allowedDeprecatedFunctions[] = $name;
    }

    /**
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param int $stackPtr
     * @param string $function
     * @param ?string $pattern
     */
    protected function addError($phpcsFile, $stackPtr, $function, $pattern = null): void
    {
        $allowed = false;
        foreach (array_keys($this->forbiddenFunctions) as $functionName) {
            if (in_array($functionName, static::$allowedDeprecatedFunctions)) {
                $allowed = true;
                break;
            }
        }

        if ($allowed === false) {
            parent::addError($phpcsFile, $stackPtr, $function, $pattern);
        }
    }
}
