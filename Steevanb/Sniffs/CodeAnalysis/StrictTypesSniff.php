<?php

declare(strict_types=1);

namespace steevanb\PhpCodeSniffs\Steevanb\Sniffs\CodeAnalysis;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\Sniff
};

/** Force declare(strict_types=1) */
class StrictTypesSniff implements Sniff
{
    /** @var bool[] */
    protected $strictTypes = [];

    public function register(): array
    {
        return [T_DECLARE, T_NAMESPACE];
    }

    public function process(File $phpcsFile, $stackPtr): void
    {
        if ($phpcsFile->getTokens()[$stackPtr]['code'] === T_DECLARE) {
            $this->strictTypes[$phpcsFile->getFilename()] = true;
        } elseif (
            $phpcsFile->getTokens()[$stackPtr]['code'] === T_NAMESPACE
            && array_key_exists($phpcsFile->getFilename(), $this->strictTypes) === false
        ) {
            $phpcsFile->addError(
                'File should have "declare(strict_types=1);" before namespace',
                $stackPtr,
                'StrictTypesRequired'
            );
        }
    }
}
