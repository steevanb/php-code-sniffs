<?php

declare(strict_types=1);

namespace steevanb\PhpCodeSniffs\Steevanb\Sniffs\Comparators;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\Sniff
};

/** Disallow "!" to compare with false */
class DisallowExclamationPointSniff implements Sniff
{
    public function register(): array
    {
        return [T_BOOLEAN_NOT];
    }

    /** @param int $stackPtr */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $phpcsFile->addError(
            '"!" to compare as false is not allowed, use "=== false" instead',
            $stackPtr,
            'NotAllowed'
        );
    }
}
