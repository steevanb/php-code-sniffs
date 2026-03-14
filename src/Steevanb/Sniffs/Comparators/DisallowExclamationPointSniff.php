<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Steevanb\Sniffs\Comparators;

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

    public function process(File $phpcsFile, int $stackPtr): void
    {
        $phpcsFile->addError(
            'Exclamation mark to compare as false is not allowed, use === false instead',
            $stackPtr,
            'NotAllowed'
        );
    }
}
