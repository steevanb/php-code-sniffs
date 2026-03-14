<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Steevanb\Sniffs\Php;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\Sniff
};

/** Disallow more than one empty line */
class DisallowMultipleEmptyLinesSniff implements Sniff
{
    public function register(): array
    {
        return [T_WHITESPACE];
    }

    public function process(File $phpcsFile, int $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();
        if (
            isset($tokens[$stackPtr + 1], $tokens[$stackPtr + 2])
            && $tokens[$stackPtr]['content'] === "\n"
            && $tokens[$stackPtr + 1]['code'] === T_WHITESPACE
            && $tokens[$stackPtr + 1]['content'] === "\n"
            && $tokens[$stackPtr + 2]['code'] === T_WHITESPACE
            && $tokens[$stackPtr + 2]['content'] === "\n"
        ) {
            $fix = $phpcsFile->addFixableError(
                'Multiple empty lines are not allowed',
                $stackPtr + 1,
                'NotAllowed'
            );

            if ($fix === true) {
                $phpcsFile->fixer->replaceToken($stackPtr + 1, '');
            }
        }
    }
}
