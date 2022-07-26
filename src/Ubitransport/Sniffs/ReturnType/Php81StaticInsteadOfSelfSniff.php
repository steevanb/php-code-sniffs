<?php

declare(strict_types=1);

namespace ubitransport\PhpCodeSniffs\Ubitransport\Sniffs\ReturnType;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use ubitransport\PhpCodeSniffs\Ubitransport\PhpVersionId;

/** Force using static and not self as return type (since PHP 8.1) */
class Php81StaticInsteadOfSelfSniff implements Sniff
{
    public function register(): array
    {
        return PhpVersionId::get() >= 80100 ? [T_SELF] : [];
    }

    /** @param int $stackPtr */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $openTag = $phpcsFile->getTokens()[$stackPtr];
        if ($phpcsFile->getFilename() === '/app/src/Ubitransport/Sniffs/ReturnType/Php81StaticInsteadOfSelfSniff.php') {
            var_dump(
                $openTag,
                $phpcsFile->getTokens()[$stackPtr + 1],
                $phpcsFile->getTokens()[$stackPtr + 2],
                $phpcsFile->getTokens()[$stackPtr + 3]
            );
        }

        $nextStackPtr = $stackPtr + 1;
        while ($phpcsFile->getTokens()[$nextStackPtr]['type'] === 'T_WHITESPACE') {
            $nextStackPtr++;
        }

        $nextTag = $phpcsFile->getTokens()[$nextStackPtr];

        if ($nextTag['type'] === 'T_OPEN_CURLY_BRACKET') {
            $phpcsFile->addError(
                'Use return type static instead of self',
                $stackPtr,
                'ReturnTypeStatic'
            );
        }
    }
}
