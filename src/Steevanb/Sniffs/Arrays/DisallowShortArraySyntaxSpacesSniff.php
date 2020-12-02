<?php

declare(strict_types=1);

namespace steevanb\PhpCodeSniffs\Steevanb\Sniffs\Arrays;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\Sniff
};

/** Disallow spaces after [ and before ] */
class DisallowShortArraySyntaxSpacesSniff implements Sniff
{
    public function register(): array
    {
        return [T_OPEN_SHORT_ARRAY, T_CLOSE_SHORT_ARRAY];
    }

    /** @param int $stackPtr */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $token = $phpcsFile->getTokens()[$stackPtr];
        if ($token['code'] === 'PHPCS_T_OPEN_SHORT_ARRAY') {
            $this->processOpenShortArray($phpcsFile, $stackPtr);
        } else {
            $this->processCloseShortArray($phpcsFile, $stackPtr);
        }
    }

    /** @param int $stackPtr */
    protected function processOpenShortArray(File $phpcsFile, $stackPtr): self
    {
        $nextToken = $phpcsFile->getTokens()[$stackPtr + 1];
        if ($nextToken['code'] === T_WHITESPACE && substr($nextToken['content'], 0, 1) !== "\n") {
            $phpcsFile->addErrorOnLine(
                'Short array syntax should not begin with spaces',
                $nextToken['line'],
                'NoSpaceAtOpen'
            );
        }

        return $this;
    }

    /** @param int $stackPtr */
    protected function processCloseShortArray(File $phpcsFile, $stackPtr): self
    {
        $previousToken = $phpcsFile->getTokens()[$stackPtr - 1];
        $previousToken2 = $phpcsFile->getTokens()[$stackPtr - 2];
        if (
            ($previousToken['content'] ?? null) !== "\n"
            && $previousToken['code'] === T_WHITESPACE
            && $previousToken2['code'] !== T_WHITESPACE
            && $previousToken2['code'] !== T_CLOSE_SHORT_ARRAY
        ) {
            $phpcsFile->addErrorOnLine(
                'Short array syntax should not end with spaces',
                $previousToken['line'],
                'NoSpaceAtClose'
            );
        }

        return $this;
    }
}
