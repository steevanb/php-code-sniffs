<?php

declare(strict_types=1);

namespace steevanb\PhpCodeSniffs\Steevanb\Sniffs\NamingConventions;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\AbstractScopeSniff,
    Util\Common
};

/**
 * Generic_Sniffs_NamingConventions_CamelCapsFunctionNameSniff fork
 * Allow external libraries to not respect this standard
 */
class CamelCapsFunctionNameSniff extends AbstractScopeSniff
{
    /** A list of all PHP magic methods */
    protected $magicMethods = [
        'construct' => true,
        'destruct' => true,
        'call' => true,
        'callstatic' => true,
        'get' => true,
        'set' => true,
        'isset' => true,
        'unset' => true,
        'sleep' => true,
        'wakeup' => true,
        'tostring' => true,
        'set_state' => true,
        'clone' => true,
        'invoke' => true,
        'debuginfo' => true
    ];

    /**
     * A list of all PHP non-magic methods starting with a double underscore.
     * These come from PHP modules such as SOAPClient.
     */
    protected $methodsDoubleUnderscore = [
        'soapcall' => true,
        'getlastrequest' => true,
        'getlastresponse' => true,
        'getlastrequestheaders' => true,
        'getlastresponseheaders' => true,
        'getfunctions' => true,
        'gettypes' => true,
        'dorequest' => true,
        'setcookie' => true,
        'setlocation' => true,
        'setsoapheaders' => true
    ];

    protected $allowedNotCamelCase = [
        'getSQLDeclaration',
        'convertToPHPValue',
        'requiresSQLCommentHint'
    ];

    /** A list of all PHP magic functions */
    protected $magicFunctions = ['autoload' => true];

    /** If TRUE, the string must not have two capital letters next to each other. */
    public $strict = true;

    public function __construct()
    {
        parent::__construct([T_CLASS, T_ANON_CLASS, T_INTERFACE, T_TRAIT], [T_FUNCTION], true);
    }

    /**
     * @param int $stackPtr
     * @param int $currScope
     */
    protected function processTokenWithinScope(File $phpcsFile, $stackPtr, $currScope): void
    {
        $methodName = $phpcsFile->getDeclarationName($stackPtr);
        if ($methodName === null) {
            // Ignore closures.
            return;
        }

        $className = $phpcsFile->getDeclarationName($currScope);
        $errorData = [$className . '::' . $methodName];

        // Is this a magic method. i.e., is prefixed with "__" ?
        if (preg_match('|^__[^_]|', $methodName) !== 0) {
            $magicPart = strtolower(substr($methodName, 2));
            if (
                isset($this->magicMethods[$magicPart]) === false
                && isset($this->methodsDoubleUnderscore[$magicPart]) === false
            ) {
                $error =
                    'Method name "%s" is invalid;'
                    . ' only PHP magic methods should be prefixed with a double underscore';
                $phpcsFile->addError($error, $stackPtr, 'MethodDoubleUnderscore', $errorData);
            }

            return;
        }

        // PHP4 constructors are allowed to break our rules.
        if ($methodName === $className) {
            return;
        }

        // PHP4 destructors are allowed to break our rules.
        if ($methodName === '_' . $className) {
            return;
        }

        // Ignore first underscore in methods prefixed with "_".
        $methodName = ltrim($methodName, '_');

        $methodProps = $phpcsFile->getMethodProperties($stackPtr);
        if (Common::isCamelCaps($methodName, false, true, $this->strict) === false) {
            if ($methodProps['scope_specified'] === true) {
                if (in_array($methodName, $this->allowedNotCamelCase) === false) {
                    $error = '%s method name "%s" is not in camel caps format';
                    $data = [
                        ucfirst($methodProps['scope']),
                        $errorData[0],
                    ];
                    $phpcsFile->addError($error, $stackPtr, 'ScopeNotCamelCaps', $data);
                }
            } else {
                $error = 'Method name "%s" is not in camel caps format';
                $phpcsFile->addError($error, $stackPtr, 'NotCamelCaps', $errorData);
            }

            $phpcsFile->recordMetric($stackPtr, 'CamelCase method name', 'no');

            return;
        } else {
            $phpcsFile->recordMetric($stackPtr, 'CamelCase method name', 'yes');
        }
    }

    /** @param int $stackPtr */
    protected function processTokenOutsideScope(File $phpcsFile, $stackPtr): void
    {
        $functionName = $phpcsFile->getDeclarationName($stackPtr);
        if ($functionName === null) {
            // Ignore closures.
            return;
        }

        $errorData = [$functionName];

        // Is this a magic function. i.e., it is prefixed with "__".
        if (preg_match('|^__[^_]|', $functionName) !== 0) {
            $magicPart = strtolower(substr($functionName, 2));
            if (isset($this->magicFunctions[$magicPart]) === false) {
                 $error =
                     'Function name "%s" is invalid; '
                     . 'only PHP magic methods should be prefixed with a double underscore';
                 $phpcsFile->addError($error, $stackPtr, 'FunctionDoubleUnderscore', $errorData);
            }

            return;
        }

        // Ignore first underscore in functions prefixed with "_".
        $functionName = ltrim($functionName, '_');

        if (Common::isCamelCaps($functionName, false, true, $this->strict) === false) {
            $error = 'Function name "%s" is not in camel caps format';
            $phpcsFile->addError($error, $stackPtr, 'NotCamelCaps', $errorData);
            $phpcsFile->recordMetric($stackPtr, 'CamelCase function name', 'no');
        } else {
            $phpcsFile->recordMetric($stackPtr, 'CamelCase method name', 'yes');
        }
    }
}
