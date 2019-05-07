<?php

declare(strict_types=1);

namespace steevanb\PhpCodeSniffs\Steevanb\Sniffs\Functions;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\Sniff
};

/** Disallow a function to have multiple return keyword */
class DisallowMultipleReturnSniff implements Sniff
{
    /** @var string[] */
    protected static $allowedFunctions = [];

    public static function addAllowedFunction(string $fileName, string $function)
    {
        static::$allowedFunctions[$fileName][] = $function;
    }

    public function register(): array
    {
        return [T_FUNCTION, T_RETURN];
    }

    /** @param int $stackPtr */
    public function process(File $phpcsFile, $stackPtr): void
    {
        static $countReturn = [];
        static $currentFunction;

        $token = $phpcsFile->getTokens()[$stackPtr];
        if ($token['code'] === T_FUNCTION) {
            $countReturn = 0;
            $currentFunction = $phpcsFile->getTokens()[$phpcsFile->findNext(T_STRING, $stackPtr)]['content'];
        } elseif (
            $token['code'] === T_RETURN
            && is_string($currentFunction)
            && in_array($currentFunction, static::$allowedFunctions[$phpcsFile->getFilename()] ?? []) === false
            && in_array('PHPCS_T_CLOSURE', $token['conditions']) === false
        ) {
            $countReturn++;
            if ($countReturn > 1) {
                $phpcsFile->addErrorOnLine(
                    'Multiple return in function "' . $currentFunction . '" are not allowed',
                    $token['line'],
                    'NotAllowed'
                );
            }
        }
    }
}
