<?php

declare(strict_types=1);

/**
 * PSR2_Sniffs_Namespaces_UseDeclarationSniff fork
 * Remove only one use per line, for PHP 7
 */
class Steevanb_Sniffs_Namespaces_UseDeclarationSniff implements PHP_CodeSniffer_Sniff
{
    public function register(): array
    {
        return [T_USE];
    }

    /** @param int $stackPtr */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr): void
    {
        if ($this->shouldIgnoreUse($phpcsFile, $stackPtr) === true) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        // One space after the use keyword.
        if ($tokens[($stackPtr + 1)]['content'] !== ' ') {
            $error = 'There must be a single space after the USE keyword';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceAfterUse');
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken(($stackPtr + 1), ' ');
            }
        }

        // Make sure this USE comes after the first namespace declaration.
        $prev = $phpcsFile->findPrevious(T_NAMESPACE, ($stackPtr - 1));
        if ($prev !== false) {
            $first = $phpcsFile->findNext(T_NAMESPACE, 1);
            if ($prev !== $first) {
                $error = 'USE declarations must go after the first namespace declaration';
                $phpcsFile->addError($error, $stackPtr, 'UseAfterNamespace');
            }
        }

        // Only interested in the last USE statement from here onwards.
        $nextUse = $phpcsFile->findNext(T_USE, ($stackPtr + 1));
        while ($this->shouldIgnoreUse($phpcsFile, $nextUse) === true) {
            $nextUse = $phpcsFile->findNext(T_USE, ($nextUse + 1));
            if ($nextUse === false) {
                break;
            }
        }

        if ($nextUse !== false) {
            return;
        }

        $end  = $phpcsFile->findNext(T_SEMICOLON, ($stackPtr + 1));
        $next = $phpcsFile->findNext(T_WHITESPACE, ($end + 1), null, true);

        if ($tokens[$next]['code'] === T_CLOSE_TAG) {
            return;
        }

        $diff = ($tokens[$next]['line'] - $tokens[$end]['line'] - 1);
        if ($diff !== 1) {
            if ($diff < 0) {
                $diff = 0;
            }

            $error = 'There must be one blank line after the last USE statement; %s found;';
            $data  = [$diff];
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceAfterLastUse', $data);
            if ($fix === true) {
                if ($diff === 0) {
                    $phpcsFile->fixer->addNewline($end);
                } else {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = ($end + 1); $i < $next; $i++) {
                        if ($tokens[$i]['line'] === $tokens[$next]['line']) {
                            break;
                        }

                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->addNewline($end);
                    $phpcsFile->fixer->endChangeset();
                }
            }
        }
    }

    /** @param int $stackPtr */
    private function shouldIgnoreUse(PHP_CodeSniffer_File $phpcsFile, $stackPtr): bool
    {
        $tokens = $phpcsFile->getTokens();

        // Ignore USE keywords inside closures.
        $next = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
        if ($tokens[$next]['code'] === T_OPEN_PARENTHESIS) {
            return true;
        }

        // Ignore USE keywords for traits.
        if ($phpcsFile->hasCondition($stackPtr, [T_CLASS, T_TRAIT]) === true) {
            return true;
        }

        return false;
    }
}
