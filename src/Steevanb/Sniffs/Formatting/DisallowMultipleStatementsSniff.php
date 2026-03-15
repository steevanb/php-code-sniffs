<?php
/**
 * Fork from PHPCSStandards/PHP_CodeSniffer/src/Standards/Generic/Sniffs/Formatting/DisallowMultipleStatementsSniff.php
 * Allow empty property hooks on a single line: { get; set; }
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2023 Squiz Pty Ltd (ABN 77 084 670 600)
 * @copyright 2023 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Steevanb\Sniffs\Formatting;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\Sniff
};

class DisallowMultipleStatementsSniff implements Sniff
{
    public function register(): array
    {
        return [T_SEMICOLON];
    }

    public function process(File $phpcsFile, int $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();
        $fixable = true;
        $prev = $stackPtr;

        do {
            $prev = $phpcsFile->findPrevious(
                [T_SEMICOLON, T_OPEN_TAG, T_OPEN_TAG_WITH_ECHO, T_PHPCS_IGNORE],
                ($prev - 1)
            );
            if (
                $prev === false
                || $tokens[$prev]['code'] === T_OPEN_TAG
                || $tokens[$prev]['code'] === T_OPEN_TAG_WITH_ECHO
            ) {
                $phpcsFile->recordMetric($stackPtr, 'Multiple statements on same line', 'no');

                return;
            }

            if ($tokens[$prev]['code'] === T_PHPCS_IGNORE) {
                $fixable = false;
            }
        } while ($tokens[$prev]['code'] === T_PHPCS_IGNORE);

        // Ignore multiple statements in a FOR condition.
        foreach ([$stackPtr, $prev] as $checkToken) {
            if (array_key_exists('nested_parenthesis', $tokens[$checkToken]) === true) {
                foreach ($tokens[$checkToken]['nested_parenthesis'] as $bracket) {
                    if (array_key_exists('parenthesis_owner', $tokens[$bracket]) === false) {
                        continue;
                    }

                    $owner = $tokens[$bracket]['parenthesis_owner'];
                    if ($tokens[$owner]['code'] === T_FOR) {
                        return;
                    }
                }
            }
        }

        if ($tokens[$prev]['line'] === $tokens[$stackPtr]['line']) {
            if ($this->isEmptyPropertyHookBlock($phpcsFile, $stackPtr)) {
                $phpcsFile->recordMetric($stackPtr, 'Multiple statements on same line', 'no');

                return;
            }

            $phpcsFile->recordMetric($stackPtr, 'Multiple statements on same line', 'yes');

            $error = 'Each PHP statement must be on a line by itself';
            $code = 'SameLine';
            if ($fixable === false) {
                $phpcsFile->addError($error, $stackPtr, $code);

                return;
            }

            $fix = $phpcsFile->addFixableError($error, $stackPtr, $code);
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->addNewline($prev);
                if ($tokens[($prev + 1)]['code'] === T_WHITESPACE) {
                    $phpcsFile->fixer->replaceToken(($prev + 1), '');
                }

                $phpcsFile->fixer->endChangeset();
            }
        } else {
            $phpcsFile->recordMetric($stackPtr, 'Multiple statements on same line', 'no');
        }
    }

    /** Check if the current semicolon is inside a property hook block where all hooks are empty (get; set;). */
    private function isEmptyPropertyHookBlock(File $phpcsFile, int $stackPtr): bool
    {
        $tokens = $phpcsFile->getTokens();

        // Find the enclosing curly brace block.
        $openBrace = null;
        for ($i = ($stackPtr - 1); $i >= 0; $i--) {
            if ($tokens[$i]['code'] === T_OPEN_CURLY_BRACKET) {
                $openBrace = $i;
                break;
            }

            // If we hit a close curly bracket, skip its block.
            if ($tokens[$i]['code'] === T_CLOSE_CURLY_BRACKET) {
                return false;
            }
        }

        if ($openBrace === null || array_key_exists('bracket_closer', $tokens[$openBrace]) === false) {
            return false;
        }

        $closeBrace = $tokens[$openBrace]['bracket_closer'];

        // Check that the token before the open brace is a variable (property declaration).
        $beforeBrace = $phpcsFile->findPrevious(T_WHITESPACE, ($openBrace - 1), null, true);
        if ($beforeBrace === false || $tokens[$beforeBrace]['code'] !== T_VARIABLE) {
            return false;
        }

        // Check that everything inside the braces is only: whitespace, "get", "set", semicolons.
        for ($i = ($openBrace + 1); $i < $closeBrace; $i++) {
            $code = $tokens[$i]['code'];
            if ($code === T_WHITESPACE || $code === T_SEMICOLON) {
                continue;
            }

            if ($code === T_STRING && ($tokens[$i]['content'] === 'get' || $tokens[$i]['content'] === 'set')) {
                continue;
            }

            // Anything else means the hooks have code, not allowed on one line.
            return false;
        }

        return true;
    }
}
