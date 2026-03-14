<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Steevanb\Sniffs\Properties;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\Sniff,
    Util\Tokens
};

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

    public function process(File $phpcsFile, int $stackPtr): void
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
