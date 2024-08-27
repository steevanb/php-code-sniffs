<?php

declare(strict_types=1);

namespace steevanb\PhpCodeSniffs\Steevanb\Sniffs\PhpDoc;

use PHP_CodeSniffer\{
    Sniffs\Sniff,
    Files\File
};

/** Force PHPDoc to be on one line if PHPDoc contains only one line */
class NumberOfRowsRelativeToContentSniff implements Sniff
{
    public function register(): array
    {
        return [T_DOC_COMMENT_OPEN_TAG];
    }

    /** @param int $stackPtr */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $openTag = $phpcsFile->getTokens()[$stackPtr];

        $closeTagPtr = $openTag['comment_closer'] ?? null;
        if (is_int($closeTagPtr)) {
            $closeTag = $phpcsFile->getTokens()[$closeTagPtr];

            if (
                $openTag['content'] === '/**'
                && $closeTag['content'] === '*/'
                && $closeTag['line'] - $openTag['line'] === 2
            ) {
                $phpcsFile->addError(
                    'PHPDoc with only one information should be on one line',
                    $stackPtr,
                    'PHPDocOnOneLine'
                );
            }
        }
    }
}
