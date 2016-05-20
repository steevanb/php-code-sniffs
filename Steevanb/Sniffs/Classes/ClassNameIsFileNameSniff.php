<?php

/**
 * Reports errors if class, trait or interface name is not same as file name
 */
class Steevanb_Sniffs_Classes_ClassNameIsFileNameSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * @return array
     */
    public function register()
    {
        return [ T_CLASS, T_INTERFACE, T_TRAIT ];
    }

    /**
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $name = $tokens[$phpcsFile->findNext(T_STRING, $stackPtr)]['content'];
        if ($name !== pathinfo($phpcsFile->getFilename(), PATHINFO_FILENAME)) {
            $phpcsFile->addError('Class, interface or trait name must be same as file name', $stackPtr);
        }
    }
}
