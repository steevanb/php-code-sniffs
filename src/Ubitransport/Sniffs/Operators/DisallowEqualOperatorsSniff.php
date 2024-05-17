<?php

declare(strict_types=1);

namespace ubitransport\PhpCodeSniffs\Ubitransport\Sniffs\Operators;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class DisallowEqualOperatorsSniff implements Sniff
{

    /**
     * @inheritDoc
     */
    public function register()
    {
        return [
            T_IS_EQUAL,
            T_IS_NOT_EQUAL
        ];
    }

    /**
     * @inheritDoc
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $operatorReplacement = '';
        $errorMessage = 'Operator %s prohibited; use %s instead';
        if ($tokens[$stackPtr]['code'] === T_IS_EQUAL) {
            $operatorReplacement = '===';
            $fixable = $phpcsFile->addFixableError(
                sprintf($errorMessage, '==', $operatorReplacement),
                $stackPtr,
                'Prohibited'
            );
        } else {
            $operatorReplacement = '!==';
            $fixable = $phpcsFile->addFixableError(
                sprintf($errorMessage, '!=', $operatorReplacement),
                $stackPtr,
                'Prohibited'
            );
        }

        if ($fixable) {
            $phpcsFile->fixer->beginChangeset();
            $phpcsFile->fixer->replaceToken($stackPtr, $operatorReplacement);
            $phpcsFile->fixer->endChangeset();
        }
    }
}
