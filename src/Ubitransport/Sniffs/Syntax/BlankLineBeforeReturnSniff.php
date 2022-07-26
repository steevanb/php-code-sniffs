<?php

declare(strict_types=1);

namespace ubitransport\PhpCodeSniffs\Ubitransport\Sniffs\Syntax;

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

        while (in_array($previousTokenType, ['T_WHITESPACE', 'T_COMMENT', 'T_DOC_COMMENT_CLOSE_TAG'], true)) {
            if ($previousTokenType === 'T_DOC_COMMENT_CLOSE_TAG') {
                $commentCloseTagLine = $phpcsFile->getTokens()[$previousStackPtr]['line'];
                $commentOpenerPtr = $phpcsFile->getTokens()[$previousStackPtr]['comment_opener'];

                $blankLinesCount += 1 + ($commentCloseTagLine - $phpcsFile->getTokens()[$commentOpenerPtr]['line']);

                $previousStackPtr = $commentOpenerPtr - 1;
                $previousTokenType = $phpcsFile->getTokens()[$previousStackPtr]['type'];
            } else {
                $previousStackPtr--;
                if (in_array($previousTokenType, ['T_COMMENT', 'T_DOC_COMMENT'], true)) {
                    $blankLinesCount++;
                }

                $previousTokenType = $phpcsFile->getTokens()[$previousStackPtr]['type'];
            }
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
