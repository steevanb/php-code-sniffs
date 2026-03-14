<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Steevanb\Sniffs\Syntax;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\Sniff
};

/** Concatenation character "." should be surrounded by spaces */
class ConcatenationSniff implements Sniff
{
    public function register(): array
    {
        return [T_STRING_CONCAT];
    }

    public function process(File $phpcsFile, int $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        $missingBefore = $tokens[$stackPtr - 1]['code'] !== T_WHITESPACE;
        $missingAfter = $tokens[$stackPtr + 1]['code'] !== T_WHITESPACE;

        if ($missingBefore === false && $missingAfter === false) {
            return;
        }

        $fix = $phpcsFile->addFixableError(
            'Concatenation character "." should be surrounded by spaces',
            $stackPtr,
            'SurroundedBySpaces'
        );

        if ($fix === true) {
            $phpcsFile->fixer->beginChangeset();
            if ($missingBefore) {
                $phpcsFile->fixer->addContentBefore($stackPtr, ' ');
            }
            if ($missingAfter) {
                $phpcsFile->fixer->addContent($stackPtr, ' ');
            }
            $phpcsFile->fixer->endChangeset();
        }
    }
}
