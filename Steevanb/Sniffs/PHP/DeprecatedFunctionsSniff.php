<?php

declare(strict_types=1);

namespace steevanb\PhpCodeSniffs\Steevanb\Sniffs\PHP;

use PHP_CodeSniffer\{
    Files\File,
    Standards\Generic\Sniffs\PHP\DeprecatedFunctionsSniff as PhpCodeSnifferDeprecatedFunctionsSniff
};

/**
 * Generic_Sniffs_PHP_DeprecatedFunctionsSniff fork
 * Allow some deprecated functions to be used
 */
class DeprecatedFunctionsSniff extends PhpCodeSnifferDeprecatedFunctionsSniff
{
    /** @var string[] */
    protected static $allowedDeprecatedFunctions = [];

    public static function addAllowDeprecatedFunction(string $name): void
    {
        static::$allowedDeprecatedFunctions[] = $name;
    }

    /**
     * @param File $phpcsFile
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
