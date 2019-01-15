<?php

declare(strict_types=1);

/** Concatenation character "." should be surrounded by spaces */
class Steevanb_Sniffs_Syntax_ConcatenationSniff implements PHP_CodeSniffer_Sniff
{
    /** @return int[] */
    public function register()
    {
        return [T_STRING_CONCAT];
    }

    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        if (
            $phpcsFile->getTokens()[$stackPtr - 1]['type'] !== 'T_WHITESPACE'
            || $phpcsFile->getTokens()[$stackPtr + 1]['type'] !== 'T_WHITESPACE'
        ) {
            $phpcsFile->addError('Concatenation character "." should be surrounded by spaces', $stackPtr);
        }
    }
}
