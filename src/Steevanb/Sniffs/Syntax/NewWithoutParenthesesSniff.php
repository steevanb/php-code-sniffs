<?php
/**
 * Since PHP 8.4, new expressions can be chained without wrapping parentheses.
 * (new Foo())->bar() should be new Foo()->bar()
 */

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Steevanb\Sniffs\Syntax;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\Sniff
};

class NewWithoutParenthesesSniff implements Sniff
{
    public function register(): array
    {
        return [T_NEW];
    }

    public function process(File $phpcsFile, int $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        // Check if previous non-whitespace token is (.
        $prevPtr = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
        if ($prevPtr === false || $tokens[$prevPtr]['code'] !== T_OPEN_PARENTHESIS) {
            return;
        }

        $openParen = $prevPtr;

        // If the open parenthesis belongs to a function/method call, it's not a wrapping parenthesis.
        $beforeParen = $phpcsFile->findPrevious(T_WHITESPACE, $openParen - 1, null, true);
        if (
            $beforeParen !== false
            && in_array(
                $tokens[$beforeParen]['code'],
                [T_STRING, T_VARIABLE, T_CLOSE_PARENTHESIS, T_CLOSE_SQUARE_BRACKET],
                true
            ) === true
        ) {
            return;
        }

        // Find the matching close parenthesis.
        if (array_key_exists('parenthesis_closer', $tokens[$openParen]) === false) {
            return;
        }

        $closeParen = $tokens[$openParen]['parenthesis_closer'];

        // Check if after the close paren there's -> or :: or ?-> (chaining).
        $afterClose = $phpcsFile->findNext(T_WHITESPACE, $closeParen + 1, null, true);
        if (
            $afterClose === false
            || in_array(
                $tokens[$afterClose]['code'],
                [T_OBJECT_OPERATOR, T_DOUBLE_COLON, T_NULLSAFE_OBJECT_OPERATOR],
                true
            ) === false
        ) {
            return;
        }

        $fix = $phpcsFile->addFixableError(
            'Since PHP 8.4, wrapping parentheses around new are unnecessary for chaining',
            $openParen,
            'ParenthesesNotAllowed'
        );

        if ($fix === true) {
            $phpcsFile->fixer->beginChangeset();
            $phpcsFile->fixer->replaceToken($openParen, '');
            $phpcsFile->fixer->replaceToken($closeParen, '');
            $phpcsFile->fixer->endChangeset();
        }
    }
}
