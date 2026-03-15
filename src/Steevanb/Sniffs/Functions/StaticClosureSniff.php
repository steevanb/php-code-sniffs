<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Steevanb\Sniffs\Functions;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\Sniff
};

class StaticClosureSniff implements Sniff
{
    public function register(): array
    {
        return [T_CLOSURE, T_FN];
    }

    public function process(File $phpcsFile, int $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        if ($this->isStatic($phpcsFile, $stackPtr)) {
            return;
        }

        if ($this->usesThis($phpcsFile, $stackPtr)) {
            return;
        }

        $type = $tokens[$stackPtr]['code'] === T_FN ? 'Arrow function' : 'Closure';
        $fix = $phpcsFile->addFixableError(
            '%s does not use "$this" and should be declared static',
            $stackPtr,
            'MustBeStatic',
            [$type]
        );

        if ($fix === true) {
            $phpcsFile->fixer->addContentBefore($stackPtr, 'static ');
        }
    }

    private function isStatic(File $phpcsFile, int $stackPtr): bool
    {
        $prev = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);

        return $prev !== false && $phpcsFile->getTokens()[$prev]['code'] === T_STATIC;
    }

    private function usesThis(File $phpcsFile, int $stackPtr): bool
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_FN) {
            return $this->usesThisInArrowFunction($phpcsFile, $stackPtr);
        }

        if (
            array_key_exists('scope_opener', $tokens[$stackPtr]) === false
            || array_key_exists('scope_closer', $tokens[$stackPtr]) === false
        ) {
            return false;
        }

        $start = $tokens[$stackPtr]['scope_opener'];
        $end = $tokens[$stackPtr]['scope_closer'];

        for ($i = $start + 1; $i < $end; $i++) {
            if ($tokens[$i]['code'] === T_VARIABLE && $tokens[$i]['content'] === '$this') {
                return true;
            }

            if ($tokens[$i]['code'] === T_CLOSURE || $tokens[$i]['code'] === T_FN) {
                if ($tokens[$i]['code'] === T_CLOSURE && array_key_exists('scope_closer', $tokens[$i])) {
                    $i = $tokens[$i]['scope_closer'];
                } elseif ($tokens[$i]['code'] === T_FN) {
                    $i = $this->findArrowFunctionEnd($phpcsFile, $i);
                }
            }
        }

        return false;
    }

    private function usesThisInArrowFunction(File $phpcsFile, int $stackPtr): bool
    {
        $tokens = $phpcsFile->getTokens();
        $end = $this->findArrowFunctionEnd($phpcsFile, $stackPtr);

        $arrow = $phpcsFile->findNext(T_FN_ARROW, $stackPtr + 1, $end);
        if ($arrow === false) {
            return false;
        }

        for ($i = $arrow + 1; $i <= $end; $i++) {
            if ($tokens[$i]['code'] === T_VARIABLE && $tokens[$i]['content'] === '$this') {
                return true;
            }

            if ($tokens[$i]['code'] === T_CLOSURE || $tokens[$i]['code'] === T_FN) {
                if ($tokens[$i]['code'] === T_CLOSURE && array_key_exists('scope_closer', $tokens[$i])) {
                    $i = $tokens[$i]['scope_closer'];
                } elseif ($tokens[$i]['code'] === T_FN) {
                    $i = $this->findArrowFunctionEnd($phpcsFile, $i);
                }
            }
        }

        return false;
    }

    private function findArrowFunctionEnd(File $phpcsFile, int $stackPtr): int
    {
        $tokens = $phpcsFile->getTokens();

        if (array_key_exists('scope_closer', $tokens[$stackPtr])) {
            return $tokens[$stackPtr]['scope_closer'];
        }

        $semicolon = $phpcsFile->findNext([T_SEMICOLON, T_CLOSE_PARENTHESIS, T_COMMA], $stackPtr + 1);

        return $semicolon !== false ? $semicolon - 1 : $phpcsFile->numTokens - 1;
    }
}
