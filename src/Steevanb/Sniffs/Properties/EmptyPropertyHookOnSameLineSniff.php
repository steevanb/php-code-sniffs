<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Steevanb\Sniffs\Properties;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\Sniff
};

class EmptyPropertyHookOnSameLineSniff implements Sniff
{
    public function register(): array
    {
        return [T_VARIABLE];
    }

    public function process(File $phpcsFile, int $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        // Find the next non-whitespace token after the variable.
        $next = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
        if ($next === false || $tokens[$next]['code'] !== T_OPEN_CURLY_BRACKET) {
            return;
        }

        $openBrace = $next;
        if (array_key_exists('bracket_closer', $tokens[$openBrace]) === false) {
            return;
        }

        $closeBrace = $tokens[$openBrace]['bracket_closer'];

        // Check that everything inside the braces is only: whitespace, "get", "set", semicolons.
        for ($i = ($openBrace + 1); $i < $closeBrace; $i++) {
            $code = $tokens[$i]['code'];
            if ($code === T_WHITESPACE || $code === T_SEMICOLON) {
                continue;
            }

            if ($code === T_STRING && ($tokens[$i]['content'] === 'get' || $tokens[$i]['content'] === 'set')) {
                continue;
            }

            // Hooks contain code, not our concern.
            return;
        }

        // Empty hooks block: check that everything is on the same line as the variable.
        if ($tokens[$stackPtr]['line'] === $tokens[$closeBrace]['line']) {
            return;
        }

        $fix = $phpcsFile->addFixableError(
            'Empty property hooks must be on the same line as the property declaration',
            $openBrace,
            'NotOnSameLine'
        );

        if ($fix === true) {
            $phpcsFile->fixer->beginChangeset();

            // Rebuild the hook block on one line: " { get; set; }" or " { get; }" or " { set; }".
            $hookContent = '';
            for ($i = ($openBrace + 1); $i < $closeBrace; $i++) {
                $code = $tokens[$i]['code'];
                if ($code === T_STRING && ($tokens[$i]['content'] === 'get' || $tokens[$i]['content'] === 'set')) {
                    if ($hookContent !== '') {
                        $hookContent .= ' ';
                    }

                    $hookContent .= $tokens[$i]['content'] . ';';
                }
            }

            // Replace from the token after the variable up to (and including) the close brace.
            for ($i = ($stackPtr + 1); $i <= $closeBrace; $i++) {
                $phpcsFile->fixer->replaceToken($i, '');
            }

            $phpcsFile->fixer->addContent($stackPtr, ' { ' . $hookContent . ' }');
            $phpcsFile->fixer->endChangeset();
        }
    }
}
