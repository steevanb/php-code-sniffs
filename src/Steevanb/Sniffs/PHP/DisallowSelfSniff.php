<?php
/**
 * Disallow usage of self, whenever it's possible. Use static instead
 * @see https://www.php.net/manual/en/language.oop5.late-static-bindings.php
 */

declare(strict_types=1);

namespace steevanb\PhpCodeSniffs\Steevanb\Sniffs\PHP;

use PHP_CodeSniffer\{
    Files\File,
    Standards\Generic\Sniffs\PHP\DeprecatedFunctionsSniff
};

class DisallowSelfSniff extends DeprecatedFunctionsSniff
{
    protected $isInPropertyDeclaration = false;

    public function register(): array
    {
        return [T_PUBLIC, T_PROTECTED, T_PRIVATE, T_SELF];
    }

    /** @param int $stackPtr */
    public function process(File $phpcsFile, $stackPtr): void
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
            && $token['level'] > 1
        ) {
            $phpcsFile->addErrorOnLine(
                'Because of Late Static Binding self is not allowed, use static instead',
                $phpcsFile->getTokens()[$stackPtr]['line'],
                'NotAllowed'
            );
        }
    }

    protected function isInFunctionParameters(File $phpcsFile, int $stackPtr): bool
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
