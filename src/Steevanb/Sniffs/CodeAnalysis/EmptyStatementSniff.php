<?php

declare(strict_types=1);

namespace steevanb\PhpCodeSniffs\Steevanb\Sniffs\CodeAnalysis;

use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\EmptyStatementSniff as PhpCodeSnifferEmptyStatementSniff;

/**
 * Generic_Sniffs_CodeAnalysis_EmptyStatementSniff fork
 * Allow catch to be empty
 */
class EmptyStatementSniff extends PhpCodeSnifferEmptyStatementSniff
{
    public function register(): array
    {
        return [
            T_TRY,
            T_FINALLY,
            T_DO,
            T_WHILE,
            T_IF,
            T_ELSE,
            T_ELSEIF,
            T_FOR,
            T_FOREACH,
            T_SWITCH
        ];
    }
}
