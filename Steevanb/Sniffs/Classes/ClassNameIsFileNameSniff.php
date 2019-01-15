<?php

declare(strict_types=1);

/** Force class, trait or interface name to be the same as file name */
class Steevanb_Sniffs_Classes_ClassNameIsFileNameSniff implements PHP_CodeSniffer_Sniff
{
    public function register(): array
    {
        return [T_CLASS, T_INTERFACE, T_TRAIT];
    }

    /** @param int $stackPtr */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();
        $name = $tokens[$phpcsFile->findNext(T_STRING, $stackPtr)]['content'];
        if ($name !== pathinfo($phpcsFile->getFilename(), PATHINFO_FILENAME)) {
            $phpcsFile->addError('Class, interface or trait name must be same as file name', $stackPtr);
        }
    }
}
