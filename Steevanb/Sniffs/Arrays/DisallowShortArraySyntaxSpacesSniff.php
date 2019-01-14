<?php

class Steevanb_Sniffs_Arrays_DisallowShortArraySyntaxSpacesSniff implements PHP_CodeSniffer_Sniff
{
    /** @return int[] */
    public function register()
    {
        return [T_OPEN_SHORT_ARRAY, T_CLOSE_SHORT_ARRAY];
    }

    /**
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $token = $phpcsFile->getTokens()[$stackPtr];
        if ($token['code'] === 'PHPCS_T_OPEN_SHORT_ARRAY') {
            $this->processOpenShortArray($phpcsFile, $stackPtr);
        } else {
            $this->processCloseShortArray($phpcsFile, $stackPtr);
        }
    }

    /**
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param int $stackPtr
     */
    protected function processOpenShortArray(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $nextToken = $phpcsFile->getTokens()[$stackPtr + 1];
        if ($nextToken['code'] === T_WHITESPACE && substr($nextToken['content'], 0, 1) !== "\n") {
            $phpcsFile->addErrorOnLine('Short array syntax should not begin with spaces', $nextToken['line']);
        }
    }

    /**
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param int $stackPtr
     */
    protected function processCloseShortArray(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $previousToken = $phpcsFile->getTokens()[$stackPtr - 1];
        $previousToken2 = $phpcsFile->getTokens()[$stackPtr - 2];
        if ($previousToken['code'] === T_WHITESPACE && $previousToken2['code'] !== T_WHITESPACE) {
            $phpcsFile->addErrorOnLine('Short array syntax should not end with spaces', $previousToken['line']);
        }
    }
}
