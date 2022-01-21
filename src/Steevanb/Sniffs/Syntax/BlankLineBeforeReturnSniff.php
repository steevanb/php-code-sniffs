<?php

declare(strict_types=1);

namespace steevanb\PhpCodeSniffs\Steevanb\Sniffs\Syntax;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\Sniff
};

/** Force to have a blank line before return keyword */
class BlankLineBeforeReturnSniff implements Sniff
{
    /** @return int[] */
    public function register(): array
    {
        return [T_RETURN];
    }

    public function process(File $phpcsFile, $stackPtr): void
    {
        $token = $phpcsFile->getTokens()[$stackPtr];

        $blankLinesCount = 1;
        $previousStackPtr = $stackPtr - 1;
        $previousTokenType = $phpcsFile->getTokens()[$previousStackPtr]['type'];
        while (in_array($previousTokenType, ['T_WHITESPACE', 'T_COMMENT'], true)) {
            $previousStackPtr--;
            if ($previousTokenType === 'T_COMMENT') {
                $blankLinesCount++;
            }

            $previousTokenType = $phpcsFile->getTokens()[$previousStackPtr]['type'];
        }

        $previousToken = $phpcsFile->getTokens()[$previousStackPtr];
        if (
            $previousToken['type'] !== 'T_OPEN_CURLY_BRACKET'
            && $token['line'] - $previousToken['line'] <= $blankLinesCount
        ) {
            $phpcsFile->addError(
                'Add a blank line before return keyword',
                $stackPtr,
                'BlankLineBeforeReturnKeyword'
            );
        }
    }
}
