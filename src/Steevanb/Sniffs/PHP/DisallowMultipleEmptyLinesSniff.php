<?php

declare(strict_types=1);

namespace steevanb\PhpCodeSniffs\Steevanb\Sniffs\PHP;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\Sniff
};

/** Disallow more than one empty line */
class DisallowMultipleEmptyLinesSniff implements Sniff
{
    public function register(): array
    {
        return [T_WHITESPACE];
    }

    /** @param int $stackPtr */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $token = $phpcsFile->getTokens()[$stackPtr];
        if (isset($phpcsFile->getTokens()[$stackPtr + 1]) && isset($phpcsFile->getTokens()[$stackPtr + 2])) {
            $nextToken = $phpcsFile->getTokens()[$stackPtr + 1];
            $nextToken2 = $phpcsFile->getTokens()[$stackPtr + 2];
            if (
                $token['content'] === "\n"
                && $nextToken['code'] === T_WHITESPACE
                && $nextToken['content'] === "\n"
                && $nextToken2['code'] === T_WHITESPACE
                && $nextToken2['content'] === "\n"
            ) {
                $phpcsFile->addErrorOnLine(
                    'Multiple empty lines are not allowed',
                    $nextToken['line'],
                    'NotAllowed'
                );
            }
        }
    }
}
