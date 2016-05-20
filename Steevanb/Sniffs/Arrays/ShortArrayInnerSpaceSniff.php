<?php

/**
 * Reports errors if short array syntax doesn't have 1 white space inside
 * Example without error:
 *      $foo = [ $bar ]
 *      $foo = [
 *          $bar
 *      ]
 * Example who throws errors:
 *      $foo = [$bar]
 */
class Steevanb_Sniffs_Arrays_ShortArrayInnerSpaceSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * @return array
     */
    public function register()
    {
        return [ T_OPEN_SHORT_ARRAY, T_CLOSE_SHORT_ARRAY ];
    }

    /**
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_OPEN_SHORT_ARRAY) {
            if (
                $tokens[$stackPtr + 1]['code'] !== T_WHITESPACE
                || (
                    $tokens[$stackPtr + 1]['content'] !== ' '
                    && substr($tokens[$stackPtr + 1]['content'], 0, 1) !== "\n"
                    && substr($tokens[$stackPtr + 1]['content'], 0, 2) !== "\r\n"
                )
            ) {
                $phpcsFile->addError('A open sort array syntax must be followed by 1 whitespace', $stackPtr);
            }
        } else {
            if (
                $tokens[$stackPtr - 1]['code'] !== T_WHITESPACE
                || (
                    $tokens[$stackPtr - 1]['content'] !== "\n"
                    && $tokens[$stackPtr - 1]['content'] !== "\r\n"
                    && $tokens[$stackPtr - 1]['content'] !== " "
                )
            ) {
                $phpcsFile->addError('A close sort array syntax must be preceded by 1 whitespace', $stackPtr);
            }
        }
    }
}
