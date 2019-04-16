<?php

declare(strict_types=1);

namespace steevanb\PhpCodeSniffs\Steevanb\Sniffs\NamingConventions;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\AbstractVariableSniff,
    Util\Common
};

/**
 * Zend_Sniffs_NamingConventions_ValidVariableNameSniff fork
 * Allow some variables to not respect camelCase
 */
class ValidVariableNameSniff extends AbstractVariableSniff
{
    /** @var string[] */
    protected static $allowedVariableNames = [];

    public static function addAllowedVariableName(string $name)
    {
        static::$allowedVariableNames[] = $name;
    }

    /** @param int $stackPtr */
    protected function processVariable(File $phpcsFile, $stackPtr): void
    {
        $tokens  = $phpcsFile->getTokens();
        $varName = ltrim($tokens[$stackPtr]['content'], '$');

        $phpReservedVars = [
            '_SERVER',
            '_GET',
            '_POST',
            '_REQUEST',
            '_SESSION',
            '_ENV',
            '_COOKIE',
            '_FILES',
            'GLOBALS',
            'http_response_header',
            'HTTP_RAW_POST_DATA',
            'php_errormsg'
        ];

        // If it's a php reserved var, then its ok.
        if (in_array($varName, $phpReservedVars) === true) {
            return;
        }

        $objOperator = $phpcsFile->findNext([T_WHITESPACE], ($stackPtr + 1), null, true);
        if ($tokens[$objOperator]['code'] === T_OBJECT_OPERATOR) {
            // Check to see if we are using a variable from an object.
            $var = $phpcsFile->findNext([T_WHITESPACE], ($objOperator + 1), null, true);
            if ($tokens[$var]['code'] === T_STRING) {
                // Either a var name or a function call, so check for bracket.
                $bracket = $phpcsFile->findNext([T_WHITESPACE], ($var + 1), null, true);

                if ($tokens[$bracket]['code'] !== T_OPEN_PARENTHESIS) {
                    $objVarName = $tokens[$var]['content'];

                    // There is no way for us to know if the var is public or private,
                    // so we have to ignore a leading underscore if there is one and just
                    // check the main part of the variable name.
                    $originalVarName = $objVarName;
                    if (substr($objVarName, 0, 1) === '_') {
                        $objVarName = substr($objVarName, 1);
                    }

                    if (Common::isCamelCaps($objVarName, false, true, false) === false) {
                        $error = 'Variable "%s" is not in valid camel caps format';
                        $data = [$originalVarName];
                        $phpcsFile->addError($error, $var, 'NotCamelCaps', $data);
                    }
                }
            }
        }

        // There is no way for us to know if the var is public or private,
        // so we have to ignore a leading underscore if there is one and just
        // check the main part of the variable name.
        $originalVarName = $varName;
        if (substr($varName, 0, 1) === '_') {
            $objOperator = $phpcsFile->findPrevious([T_WHITESPACE], ($stackPtr - 1), null, true);
            if ($tokens[$objOperator]['code'] === T_DOUBLE_COLON) {
                // The variable lives within a class, and is referenced like
                // this: MyClass::$_variable, so we don't know its scope.
                $inClass = true;
            } else {
                $inClass = $phpcsFile->hasCondition($stackPtr, [T_CLASS, T_INTERFACE, T_TRAIT]);
            }

            if ($inClass === true) {
                $varName = substr($varName, 1);
            }
        }

        if (
            Common::isCamelCaps($varName, false, true, false) === false
            && in_array($varName, static::$allowedVariableNames) === false
        ) {
            $error = 'Variable "%s" is not in valid camel caps format';
            $data = [$originalVarName];
            $phpcsFile->addError($error, $stackPtr, 'NotCamelCaps', $data);
        }
    }

    /** @param int $stackPtr */
    protected function processMemberVar(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();
        $varName = ltrim($tokens[$stackPtr]['content'], '$');
        $memberProps = $phpcsFile->getMemberProperties($stackPtr);
        $public = ($memberProps['scope'] === 'public');

        if ($public === true) {
            if (substr($varName, 0, 1) === '_') {
                $error = 'Public member variable "%s" must not contain a leading underscore';
                $data = [$varName];
                $phpcsFile->addError($error, $stackPtr, 'PublicHasUnderscore', $data);

                return;
            }
        } elseif (substr($varName, 0, 1) === '_') {
            if (Common::isCamelCaps($varName, false, $public, false) === false) {
                $error = 'Member variable "%s" is not in valid camel caps format';
                $data = [$varName];
                $phpcsFile->addError($error, $stackPtr, 'MemberVarNotCamelCaps', $data);
            }
        }
    }

    /** @param int $stackPtr */
    protected function processVariableInString(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        $phpReservedVars = [
            '_SERVER',
            '_GET',
            '_POST',
            '_REQUEST',
            '_SESSION',
            '_ENV',
            '_COOKIE',
            '_FILES',
            'GLOBALS',
            'http_response_header',
            'HTTP_RAW_POST_DATA',
            'php_errormsg'
        ];

        if (
            preg_match_all(
                '|[^\\\]\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)|',
                $tokens[$stackPtr]['content'],
                $matches
            )
            !== 0
        ) {
            foreach ($matches[1] as $varName) {
                // If it's a php reserved var, then its ok.
                if (in_array($varName, $phpReservedVars) === true) {
                    continue;
                }

                if (Common::isCamelCaps($varName, false, true, false) === false) {
                    $error = 'Variable "%s" is not in valid camel caps format';
                    $data = [$varName];
                    $phpcsFile->addError($error, $stackPtr, 'StringVarNotCamelCaps', $data);
                }
            }
        }
    }
}
