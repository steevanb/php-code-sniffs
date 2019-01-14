<?php

/**
 * Reports errors if "!" is used to compare with false
 */
class Steevanb_Sniffs_Comparators_DisallowExclamationPointSniff implements PHP_CodeSniffer_Sniff
{
    /** @return int[] */
    public function register()
    {
        return [T_BOOLEAN_NOT];
    }

    /** @param int $stackPtr */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $phpcsFile->addError(
            '"!" to compare as false is not allowed, use "=== false" instead',
            $stackPtr
        );
    }
}
