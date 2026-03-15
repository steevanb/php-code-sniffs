<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Steevanb\Sniffs\Php;

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
    public $allowedDeprecatedFunctions = [];

    protected function addError(File $phpcsFile, int $stackPtr, string $function, ?string $pattern = null): void
    {
        $allowed = false;
        foreach (array_keys($this->forbiddenFunctions) as $functionName) {
            if (in_array($functionName, $this->allowedDeprecatedFunctions, true)) {
                $allowed = true;
                break;
            }
        }

        if ($allowed === false) {
            parent::addError($phpcsFile, $stackPtr, $function, $pattern);
        }
    }
}
