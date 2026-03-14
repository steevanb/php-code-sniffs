<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Steevanb\Sniffs\Syntax;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\Sniff
};

/** Force a blank line before return keyword, except when return is the only statement in a block */
class BlankLineBeforeReturnSniff implements Sniff
{
    public function register(): array
    {
        return [T_RETURN];
    }

    public function process(File $phpcsFile, int $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        $previousPtr = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
        if ($previousPtr === false) {
            return;
        }

        if ($tokens[$previousPtr]['code'] === T_OPEN_CURLY_BRACKET) {
            return;
        }

        if ($tokens[$stackPtr]['line'] - $tokens[$previousPtr]['line'] >= 2) {
            return;
        }

        if (
            $tokens[$previousPtr]['code'] === T_COMMENT
            || $tokens[$previousPtr]['code'] === T_DOC_COMMENT_CLOSE_TAG
        ) {
            return;
        }

        $fix = $phpcsFile->addFixableError(
            'Add a blank line before return keyword',
            $stackPtr,
            'BlankLineBeforeReturnKeyword'
        );

        if ($fix === true) {
            $eol = $phpcsFile->eolChar;
            $wsPtr = $stackPtr - 1;
            if ($tokens[$wsPtr]['code'] === T_WHITESPACE) {
                $phpcsFile->fixer->replaceToken($wsPtr, $eol . $tokens[$wsPtr]['content']);
            } else {
                $phpcsFile->fixer->addContent($wsPtr, $eol);
            }
        }
    }
}
