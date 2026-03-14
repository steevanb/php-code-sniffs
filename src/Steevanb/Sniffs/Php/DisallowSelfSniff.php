<?php
/**
 * Disallow self:: for public/protected method calls and constant access. Use static:: instead.
 * self:: is allowed for private methods, private constants, and in function parameter type hints.
 *
 * @see https://www.php.net/manual/en/language.oop5.late-static-bindings.php
 */

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Steevanb\Sniffs\Php;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\Sniff
};

class DisallowSelfSniff implements Sniff
{
    public function register(): array
    {
        return [T_SELF];
    }

    public function process(File $phpcsFile, int $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        // self in function parameter type hint: allowed.
        if ($this->isInFunctionParameters($phpcsFile, $stackPtr)) {
            return;
        }

        // Only target self:: (double colon after self).
        $nextPtr = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);
        if ($nextPtr === false || $tokens[$nextPtr]['code'] !== T_DOUBLE_COLON) {
            return;
        }

        // What follows :: ?
        $memberPtr = $phpcsFile->findNext(T_WHITESPACE, $nextPtr + 1, null, true);
        if ($memberPtr === false) {
            return;
        }

        // self::$property — ignored.
        if ($tokens[$memberPtr]['code'] === T_VARIABLE) {
            return;
        }

        // self::CONSTANT or self::method()
        if ($tokens[$memberPtr]['code'] !== T_STRING) {
            return;
        }

        $afterMemberPtr = $phpcsFile->findNext(T_WHITESPACE, $memberPtr + 1, null, true);
        $isMethodCall = $afterMemberPtr !== false && $tokens[$afterMemberPtr]['code'] === T_OPEN_PARENTHESIS;
        $memberName = $tokens[$memberPtr]['content'];

        if ($isMethodCall) {
            $visibility = $this->findMethodVisibility($phpcsFile, $stackPtr, $memberName);
        } else {
            $visibility = $this->findConstantVisibility($phpcsFile, $stackPtr, $memberName);
        }

        // Private: self:: is allowed.
        if ($visibility === T_PRIVATE) {
            return;
        }

        $fix = $phpcsFile->addFixableError(
            'Use static:: instead of self:: for public/protected members (Late Static Binding)',
            $stackPtr,
            'NotAllowed'
        );

        if ($fix === true) {
            $phpcsFile->fixer->replaceToken($stackPtr, 'static');
        }
    }

    private function isInFunctionParameters(File $phpcsFile, int $stackPtr): bool
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['nested_parenthesis']) === false) {
            return false;
        }

        foreach ($tokens[$stackPtr]['nested_parenthesis'] as $openParen => $closeParen) {
            $previousPtr = $phpcsFile->findPrevious(T_WHITESPACE, $openParen - 1, null, true);
            if ($previousPtr !== false && $tokens[$previousPtr]['code'] === T_STRING) {
                $beforeName = $phpcsFile->findPrevious(T_WHITESPACE, $previousPtr - 1, null, true);
                if ($beforeName !== false && $tokens[$beforeName]['code'] === T_FUNCTION) {
                    return true;
                }
            }

            if ($previousPtr !== false && $tokens[$previousPtr]['code'] === T_FUNCTION) {
                return true;
            }
        }

        return false;
    }

    /** @return int|null Token code (T_PUBLIC, T_PROTECTED, T_PRIVATE) or null if not found. */
    private function findMethodVisibility(File $phpcsFile, int $stackPtr, string $methodName): ?int
    {
        $tokens = $phpcsFile->getTokens();

        $classPtr = $phpcsFile->getCondition($stackPtr, T_CLASS);
        if ($classPtr === false) {
            return null;
        }

        $classOpen = $tokens[$classPtr]['scope_opener'];
        $classClose = $tokens[$classPtr]['scope_closer'];

        $ptr = $classOpen;
        while ($ptr < $classClose) {
            $ptr = $phpcsFile->findNext(T_FUNCTION, $ptr + 1, $classClose);
            if ($ptr === false) {
                break;
            }

            $namePtr = $phpcsFile->findNext(T_WHITESPACE, $ptr + 1, null, true);
            if ($namePtr === false || $tokens[$namePtr]['content'] !== $methodName) {
                continue;
            }

            return $this->findVisibilityBefore($phpcsFile, $ptr, $classOpen);
        }

        return null;
    }

    /** @return int|null Token code (T_PUBLIC, T_PROTECTED, T_PRIVATE) or null if not found. */
    private function findConstantVisibility(File $phpcsFile, int $stackPtr, string $constantName): ?int
    {
        $tokens = $phpcsFile->getTokens();

        $classPtr = $phpcsFile->getCondition($stackPtr, T_CLASS);
        if ($classPtr === false) {
            return null;
        }

        $classOpen = $tokens[$classPtr]['scope_opener'];
        $classClose = $tokens[$classPtr]['scope_closer'];

        $ptr = $classOpen;
        while ($ptr < $classClose) {
            $ptr = $phpcsFile->findNext(T_CONST, $ptr + 1, $classClose);
            if ($ptr === false) {
                break;
            }

            // Find the constant name after const (skip type if present).
            $namePtr = $ptr + 1;
            while ($namePtr < $classClose) {
                $namePtr = $phpcsFile->findNext(T_WHITESPACE, $namePtr, null, true);
                if ($namePtr === false) {
                    break;
                }

                if ($tokens[$namePtr]['code'] === T_STRING) {
                    // Check if this is the constant name (followed by =) or a type.
                    $afterName = $phpcsFile->findNext(T_WHITESPACE, $namePtr + 1, null, true);
                    if ($afterName !== false && $tokens[$afterName]['code'] === T_EQUAL) {
                        break;
                    }
                }

                $namePtr++;
            }

            if ($namePtr === false || $tokens[$namePtr]['content'] !== $constantName) {
                continue;
            }

            return $this->findVisibilityBefore($phpcsFile, $ptr, $classOpen);
        }

        return null;
    }

    private function findVisibilityBefore(File $phpcsFile, int $ptr, int $classOpen): int
    {
        $tokens = $phpcsFile->getTokens();

        $visibilityPtr = $phpcsFile->findPrevious(
            [T_PUBLIC, T_PROTECTED, T_PRIVATE],
            $ptr - 1,
            $classOpen
        );

        if ($visibilityPtr === false) {
            return T_PUBLIC;
        }

        // Make sure the visibility belongs to this declaration.
        $between = $phpcsFile->findNext(
            [T_FUNCTION, T_VARIABLE, T_CONST, T_SEMICOLON],
            $visibilityPtr + 1,
            $ptr
        );
        if ($between !== false && $between !== $ptr) {
            return T_PUBLIC;
        }

        return $tokens[$visibilityPtr]['code'];
    }
}
