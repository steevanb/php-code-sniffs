<?php
/**
 * Fork from squizlabs/php_codesniffer/src/Standards/PSR2/Sniffs/ControlStructures/ElseIfDeclarationSniff.php
 * Change warning into error
 * Verifies that there are no else if statements (elseif should be used instead).
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

declare(strict_types=1);

namespace steevanb\PhpCodeSniffs\Steevanb\Sniffs\ControlStructures;

use PHP_CodeSniffer\{
    Sniffs\Sniff,
    Files\File
};

class ElseIfDeclarationSniff implements Sniff
{
    public function register(): array
    {
        return [
            T_ELSE,
            T_ELSEIF,
        ];
    }

    /** @param int $stackPtr  The position of the current token in the stack passed in $tokens. */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_ELSEIF) {
            $phpcsFile->recordMetric($stackPtr, 'Use of ELSE IF or ELSEIF', 'elseif');
            return;
        }

        $next = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
        if ($tokens[$next]['code'] === T_IF) {
            $phpcsFile->recordMetric($stackPtr, 'Use of ELSE IF or ELSEIF', 'else if');
            $error = 'Usage of ELSE IF is discouraged; use ELSEIF instead';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NotAllowed');

            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->replaceToken($stackPtr, 'elseif');
                for ($i = ($stackPtr + 1); $i <= $next; $i++) {
                    $phpcsFile->fixer->replaceToken($i, '');
                }

                $phpcsFile->fixer->endChangeset();
            }
        }
    }
}
