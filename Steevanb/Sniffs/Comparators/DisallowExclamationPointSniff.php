<?php

/**
 * Reports errors if "!" is used to compare as false
 */
class Steevanb_Sniffs_Comparators_DisallowExclamationPointSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * @return array
     */
    public function register()
    {
        return [ T_BOOLEAN_NOT ];
    }

    /**
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $phpcsFile->addError(
            '"!" to compare as false is not allowed, use "== false" or "=== false" instead',
            $stackPtr
        );
    }
}
