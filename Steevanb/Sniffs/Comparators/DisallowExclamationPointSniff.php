<?php

declare(strict_types=1);

/** Disallow "!" to compare with false */
class Steevanb_Sniffs_Comparators_DisallowExclamationPointSniff implements PHP_CodeSniffer_Sniff
{
    public function register(): array
    {
        return [T_BOOLEAN_NOT];
    }

    /** @param int $stackPtr */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr): void
    {
        $phpcsFile->addError(
            '"!" to compare as false is not allowed, use "=== false" instead',
            $stackPtr
        );
    }
}
