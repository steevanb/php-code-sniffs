<?php

/**
 * Obligation de declare(strict_types=1)
 */
class Steevanb_Sniffs_CodeAnalysis_StrictTypesSniff extends Generic_Sniffs_CodeAnalysis_EmptyStatementSniff
{
    protected $strictTypes = [];

    public function register()
    {
        return [T_DECLARE, T_NAMESPACE];
    }

    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        if ($phpcsFile->getTokens()[$stackPtr]['code'] === T_DECLARE) {
            $this->strictTypes[$phpcsFile->getFilename()] = true;
        } elseif (
            $phpcsFile->getTokens()[$stackPtr]['code'] === T_NAMESPACE
            && array_key_exists($phpcsFile->getFilename(), $this->strictTypes) === false
        ) {
            $phpcsFile->addError('File should have "declare(strict_types=1);" before namespace', $stackPtr);
        }
    }
}
