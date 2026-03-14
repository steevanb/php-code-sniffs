<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Steevanb\Sniffs\ReturnType;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\Sniff
};

/** Force using static and not self as return type for non-static public/protected methods */
class StaticInsteadOfSelfSniff implements Sniff
{
    public function register(): array
    {
        return [T_SELF];
    }

    public function process(File $phpcsFile, int $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        // Check that self is followed by { (return type position).
        $nextStackPtr = $stackPtr + 1;
        while ($tokens[$nextStackPtr]['code'] === T_WHITESPACE) {
            $nextStackPtr++;
        }

        if ($tokens[$nextStackPtr]['code'] !== T_OPEN_CURLY_BRACKET) {
            return;
        }

        // Find the function declaration for this return type.
        $functionPtr = $phpcsFile->findPrevious(T_FUNCTION, $stackPtr - 1);
        if ($functionPtr === false) {
            return;
        }

        // Allow self on static methods.
        $previousPtr = $phpcsFile->findPrevious(T_WHITESPACE, $functionPtr - 1, null, true);
        if ($previousPtr !== false && $tokens[$previousPtr]['code'] === T_STATIC) {
            return;
        }

        // Allow self on private methods.
        $visibilityPtr = $phpcsFile->findPrevious(
            [T_PUBLIC, T_PROTECTED, T_PRIVATE],
            $functionPtr - 1
        );
        if ($visibilityPtr !== false && $tokens[$visibilityPtr]['code'] === T_PRIVATE) {
            return;
        }

        $fix = $phpcsFile->addFixableError(
            'Use return type static instead of self',
            $stackPtr,
            'ReturnTypeStatic'
        );

        if ($fix === true) {
            $phpcsFile->fixer->replaceToken($stackPtr, 'static');
        }
    }
}
