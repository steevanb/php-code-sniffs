<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Steevanb\Sniffs\PhpDoc;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\Sniff
};

/** Force PHPDoc to be on one line if PHPDoc contains only one line */
class NumberOfRowsRelativeToContentSniff implements Sniff
{
    public function register(): array
    {
        return [T_DOC_COMMENT_OPEN_TAG];
    }

    public function process(File $phpcsFile, int $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();
        $openTag = $tokens[$stackPtr];

        $closeTagPtr = $openTag['comment_closer'] ?? null;
        if (is_int($closeTagPtr) === false) {
            return;
        }

        $closeTag = $tokens[$closeTagPtr];

        if (
            $openTag['content'] !== '/**'
            || $closeTag['content'] !== '*/'
            || $closeTag['line'] - $openTag['line'] !== 2
        ) {
            return;
        }

        $fix = $phpcsFile->addFixableError(
            'Single-line PHPDoc should not span multiple lines',
            $stackPtr,
            'PHPDocOnOneLine'
        );

        if ($fix === true) {
            $content = $this->extractContent($phpcsFile, $stackPtr, $closeTagPtr);

            $phpcsFile->fixer->beginChangeset();

            $phpcsFile->fixer->replaceToken($stackPtr, '/** ' . $content . ' */');
            for ($i = $stackPtr + 1; $i <= $closeTagPtr; $i++) {
                $phpcsFile->fixer->replaceToken($i, '');
            }

            $phpcsFile->fixer->endChangeset();
        }
    }

    private function extractContent(File $phpcsFile, int $openPtr, int $closePtr): string
    {
        $tokens = $phpcsFile->getTokens();
        $contentLine = $tokens[$openPtr]['line'] + 1;
        $content = '';

        for ($i = $openPtr + 1; $i < $closePtr; $i++) {
            if ($tokens[$i]['line'] !== $contentLine) {
                continue;
            }

            if ($tokens[$i]['code'] === T_DOC_COMMENT_STAR) {
                continue;
            }

            $content .= $tokens[$i]['content'];
        }

        return trim($content);
    }
}
