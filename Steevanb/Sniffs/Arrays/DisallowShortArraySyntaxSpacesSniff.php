<?php

declare(strict_types=1);

/** Disallow spaces after [ and before ] */
class Steevanb_Sniffs_Arrays_DisallowShortArraySyntaxSpacesSniff implements PHP_CodeSniffer_Sniff
{
    public function register(): array
    {
        return [T_OPEN_SHORT_ARRAY, T_CLOSE_SHORT_ARRAY];
    }

    /** @param int $stackPtr */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr): void
    {
        $token = $phpcsFile->getTokens()[$stackPtr];
        if ($token['code'] === 'PHPCS_T_OPEN_SHORT_ARRAY') {
            $this->processOpenShortArray($phpcsFile, $stackPtr);
        } else {
            $this->processCloseShortArray($phpcsFile, $stackPtr);
        }
    }

    /** @param int $stackPtr */
    protected function processOpenShortArray(PHP_CodeSniffer_File $phpcsFile, $stackPtr): self
    {
        $nextToken = $phpcsFile->getTokens()[$stackPtr + 1];
        if ($nextToken['code'] === T_WHITESPACE && substr($nextToken['content'], 0, 1) !== "\n") {
            $phpcsFile->addErrorOnLine('Short array syntax should not begin with spaces', $nextToken['line']);
        }

        return $this;
    }

    /** @param int $stackPtr */
    protected function processCloseShortArray(PHP_CodeSniffer_File $phpcsFile, $stackPtr): self
    {
        $previousToken = $phpcsFile->getTokens()[$stackPtr - 1];
        $previousToken2 = $phpcsFile->getTokens()[$stackPtr - 2];
        if ($previousToken['code'] === T_WHITESPACE && $previousToken2['code'] !== T_WHITESPACE) {
            $phpcsFile->addErrorOnLine('Short array syntax should not end with spaces', $previousToken['line']);
        }

        return $this;
    }
}
