<?php

declare(strict_types=1);

/** Disallow more than one empty line */
class Steevanb_Sniffs_PHP_DisallowMultipleEmptyLinesSniff extends Generic_Sniffs_PHP_DeprecatedFunctionsSniff
{
    public function register(): array
    {
        return [T_WHITESPACE];
    }

    /** @param int $stackPtr */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr): void
    {
        $token = $phpcsFile->getTokens()[$stackPtr];
        if (isset($phpcsFile->getTokens()[$stackPtr + 1]) && isset($phpcsFile->getTokens()[$stackPtr + 2])) {
            $nextToken = $phpcsFile->getTokens()[$stackPtr + 1];
            $nextToken2 = $phpcsFile->getTokens()[$stackPtr + 2];
            if (
                $token['content'] === "\n"
                && $nextToken['code'] === T_WHITESPACE
                && $nextToken['content'] === "\n"
                && $nextToken2['code'] === T_WHITESPACE
                && $nextToken2['content'] === "\n"
            ) {
                $phpcsFile->addErrorOnLine('Multiple empty lines are not allowed', $nextToken['line']);
            }
        }
    }
}
