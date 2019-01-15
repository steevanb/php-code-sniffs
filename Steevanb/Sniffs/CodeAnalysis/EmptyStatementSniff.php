<?php

declare(strict_types=1);

/**
 * Generic_Sniffs_CodeAnalysis_EmptyStatementSniff fork
 * Allow catch to be empty
 */
class Steevanb_Sniffs_CodeAnalysis_EmptyStatementSniff extends Generic_Sniffs_CodeAnalysis_EmptyStatementSniff
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
