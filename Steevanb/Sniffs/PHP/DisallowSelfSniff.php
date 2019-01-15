<?php

declare(strict_types=1);

/** Disallow usage of self, whenever it's possible. Use static instead */
class Steevanb_Sniffs_PHP_DisallowSelfSniff extends Generic_Sniffs_PHP_DeprecatedFunctionsSniff
{
    protected $isInPropertyDeclaration = false;

    public function register(): array
    {
        return [T_PUBLIC, T_PROTECTED, T_PRIVATE, T_SELF];
    }

    /** @param int $stackPtr */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr): void
    {
        $token = $phpcsFile->getTokens()[$stackPtr];
        if (in_array($token['code'], [T_PUBLIC, T_PROTECTED, T_PRIVATE])) {
            $functionToken = $phpcsFile->getTokens()[$phpcsFile->findNext(T_FUNCTION, $stackPtr)];
            $variableToken = $phpcsFile->getTokens()[$phpcsFile->findNext(T_VARIABLE, $stackPtr)];

            $this->isInPropertyDeclaration =
                $variableToken['line'] === $token['line']
                && $functionToken['line'] !== $token['line'];
        } elseif (
            $token['code'] === T_SELF
            && $this->isInPropertyDeclaration === false
            && $this->isInFunctionParameters($phpcsFile, $stackPtr) === false
        ) {
            $phpcsFile->addErrorOnLine(
                'Because of Late Static Binding, self is not allowed, use static instead',
                $phpcsFile->getTokens()[$stackPtr]['line']
            );
        }
    }

    protected function isInFunctionParameters(PHP_CodeSniffer_File $phpcsFile, int $stackPtr): bool
    {
        $return = false;
        $openPtr = $phpcsFile->findPrevious([T_FUNCTION], $stackPtr);
        if (is_int($openPtr)) {
            $closePtr = $phpcsFile->findNext(['PHPCS_T_CLOSE_PARENTHESIS'], $openPtr);
            if ($closePtr === false || $closePtr > $stackPtr) {
                $return = true;
            }
        }

        return $return;
    }
}
