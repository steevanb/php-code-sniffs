<?php

declare(strict_types=1);

namespace steevanb\PhpCodeSniffs\Steevanb\Sniffs\Properties;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Mostly copied from PHP_CodeSniffer\Standards\PSR12\Sniffs\Properties\ConstantVisibilitySniff
 * Change warning to error
 */
class ConstantVisibilitySniff implements Sniff
{
    public function register(): array
    {
        return [T_CONST];
    }

    /** @param int $stackPtr */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();
        $prev   = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        if (isset(Tokens::$scopeModifiers[$tokens[$prev]['code']]) === true) {
            return;
        }

        $error = 'Visibility must be declared on all constants';
        $phpcsFile->addError($error, $stackPtr, 'NotFound');
    }
}
