<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Steevanb\Sniffs\Formatting;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\Sniff
};

class ChainedCallsOnNewLineSniff implements Sniff
{
    public function register(): array
    {
        return [T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR];
    }

    public function process(File $phpcsFile, int $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        $prev = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
        if ($prev === false || $tokens[$prev]['line'] !== $tokens[$stackPtr]['line']) {
            return;
        }

        if ($this->isFirstOperatorInChain($phpcsFile, $stackPtr) === false) {
            return;
        }

        if ($this->chainSpansMultipleLines($phpcsFile, $stackPtr) === false) {
            return;
        }

        $fix = $phpcsFile->addFixableError(
            'First chained call must be on a new line when chain spans multiple lines',
            $stackPtr,
            'FirstCallSameLine'
        );

        if ($fix === true) {
            $this->fix($phpcsFile, $stackPtr);
        }
    }

    private function fix(File $phpcsFile, int $firstOperator): void
    {
        $tokens = $phpcsFile->getTokens();
        $indent = $this->getChainIndent($phpcsFile, $firstOperator);
        $firstLine = $tokens[$firstOperator]['line'];

        $phpcsFile->fixer->beginChangeset();

        $current = $firstOperator;
        while ($current !== null && $tokens[$current]['line'] === $firstLine) {
            $before = $current - 1;
            if ($tokens[$before]['code'] === T_WHITESPACE) {
                $phpcsFile->fixer->replaceToken($before, '');
            }

            $phpcsFile->fixer->addContentBefore($current, "\n" . $indent);

            $current = $this->findNextOperatorInChain($phpcsFile, $current);
        }

        $phpcsFile->fixer->endChangeset();
    }

    private function getChainIndent(File $phpcsFile, int $firstOperator): string
    {
        $tokens = $phpcsFile->getTokens();
        $firstLine = $tokens[$firstOperator]['line'];
        $current = $firstOperator;

        while (true) {
            $next = $this->findNextOperatorInChain($phpcsFile, $current);
            if ($next === null) {
                break;
            }

            if ($tokens[$next]['line'] !== $firstLine) {
                $prevToken = $next - 1;
                if ($tokens[$prevToken]['code'] === T_WHITESPACE) {
                    $content = $tokens[$prevToken]['content'];
                    $lastNewline = strrpos($content, "\n");
                    if ($lastNewline !== false) {
                        return substr($content, $lastNewline + 1);
                    }

                    // PHPCS may split whitespace into separate tokens (newline and indentation).
                    // If the token has no newline but is on the same line as the operator, it is the indentation.
                    if ($tokens[$prevToken]['line'] === $tokens[$next]['line']) {
                        return $content;
                    }
                }

                break;
            }

            $current = $next;
        }

        return '    ';
    }

    private function isFirstOperatorInChain(File $phpcsFile, int $stackPtr): bool
    {
        $tokens = $phpcsFile->getTokens();

        $prev = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
        if ($prev === false) {
            return true;
        }

        $prev = $this->skipBackwardsOverBrackets($phpcsFile, $prev);
        if ($prev === false) {
            return true;
        }

        $beforeName = $phpcsFile->findPrevious(T_WHITESPACE, $prev - 1, null, true);
        if ($beforeName === false) {
            return true;
        }

        return $tokens[$beforeName]['code'] !== T_OBJECT_OPERATOR
            && $tokens[$beforeName]['code'] !== T_NULLSAFE_OBJECT_OPERATOR;
    }

    private function skipBackwardsOverBrackets(File $phpcsFile, int $ptr): int|false
    {
        $tokens = $phpcsFile->getTokens();

        while (true) {
            if (
                $tokens[$ptr]['code'] === T_CLOSE_PARENTHESIS
                && array_key_exists('parenthesis_opener', $tokens[$ptr])
            ) {
                $ptr = $phpcsFile->findPrevious(
                    T_WHITESPACE,
                    $tokens[$ptr]['parenthesis_opener'] - 1,
                    null,
                    true
                );
                if ($ptr === false) {
                    return false;
                }

                continue;
            }

            if (
                $tokens[$ptr]['code'] === T_CLOSE_SQUARE_BRACKET
                && array_key_exists('bracket_opener', $tokens[$ptr])
            ) {
                $ptr = $phpcsFile->findPrevious(
                    T_WHITESPACE,
                    $tokens[$ptr]['bracket_opener'] - 1,
                    null,
                    true
                );
                if ($ptr === false) {
                    return false;
                }

                continue;
            }

            return $ptr;
        }
    }

    private function chainSpansMultipleLines(File $phpcsFile, int $firstOperator): bool
    {
        $tokens = $phpcsFile->getTokens();
        $firstLine = $tokens[$firstOperator]['line'];
        $current = $firstOperator;

        while (true) {
            $next = $this->findNextOperatorInChain($phpcsFile, $current);
            if ($next === null) {
                return false;
            }

            if ($tokens[$next]['line'] !== $firstLine) {
                return true;
            }

            $current = $next;
        }
    }

    private function findNextOperatorInChain(File $phpcsFile, int $stackPtr): ?int
    {
        $tokens = $phpcsFile->getTokens();
        $i = $stackPtr + 1;

        while ($i < $phpcsFile->numTokens) {
            $code = $tokens[$i]['code'];

            if ($code === T_OBJECT_OPERATOR || $code === T_NULLSAFE_OBJECT_OPERATOR) {
                return $i;
            }

            if ($code === T_OPEN_PARENTHESIS && array_key_exists('parenthesis_closer', $tokens[$i])) {
                $i = $tokens[$i]['parenthesis_closer'] + 1;
                continue;
            }

            if ($code === T_OPEN_SQUARE_BRACKET && array_key_exists('bracket_closer', $tokens[$i])) {
                $i = $tokens[$i]['bracket_closer'] + 1;
                continue;
            }

            if ($code === T_OPEN_CURLY_BRACKET && array_key_exists('bracket_closer', $tokens[$i])) {
                $i = $tokens[$i]['bracket_closer'] + 1;
                continue;
            }

            if (
                $code === T_STRING
                || $code === T_WHITESPACE
                || $code === T_VARIABLE
                || $code === T_COMMENT
            ) {
                $i++;
                continue;
            }

            return null;
        }

        return null;
    }
}
