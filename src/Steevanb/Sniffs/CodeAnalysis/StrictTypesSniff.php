<?php

declare(strict_types=1);

namespace steevanb\PhpCodeSniffs\Steevanb\Sniffs\CodeAnalysis;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\Sniff
};

/**
 * Force declare(strict_types=1)
 * Mostly copied from RequireStrictTypesSniff, force value to be 1
 */
class StrictTypesSniff implements Sniff
{
    /** @var bool[] */
    protected $strictTypes = [];

    public function register(): array
    {
        return [T_OPEN_TAG];
    }

    public function process(File $phpcsFile, $stackPtr): int
    {
        $tokens = $phpcsFile->getTokens();
        $declare = $phpcsFile->findNext(T_DECLARE, $stackPtr);
        $found = false;
        $strictTypesEnabled = false;

        if ($declare !== false) {
            $nextString = $phpcsFile->findNext(T_STRING, $declare);

            if ($nextString !== false) {
                if (strtolower($tokens[$nextString]['content']) === 'strict_types') {
                    $found = true;
                    if (
                        $tokens[$nextString + 1]['type'] === T_EQUAL
                        || $tokens[$nextString + 2]['type'] === T_LNUMBER
                        || $tokens[$nextString + 2]['content'] === '1'
                    ) {
                        $strictTypesEnabled = true;
                    }
                }
            }
        }

        if ($found === false) {
            $phpcsFile->addError(
                'File should have "declare(strict_types=1);" after <?php',
                $stackPtr,
                'StrictTypesRequired'
            );
        } elseif ($strictTypesEnabled === false) {
            $phpcsFile->addError(
                'strict_types value should be 1, another value found',
                $stackPtr,
                'InvalidStrictTypesValue'
            );
        }

        // Skip the rest of the file so we don't pick up additional
        // open tags, typically embedded in HTML.
        return $phpcsFile->numTokens;
    }
}
