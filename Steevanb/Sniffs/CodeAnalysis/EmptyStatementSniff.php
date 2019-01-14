<?php

/**
 * Autorisation de catch vide
 */
class Steevanb_Sniffs_CodeAnalysis_EmptyStatementSniff extends Generic_Sniffs_CodeAnalysis_EmptyStatementSniff
{
    public function register()
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
