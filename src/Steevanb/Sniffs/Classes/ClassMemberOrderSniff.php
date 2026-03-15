<?php
/**
 * Enforces a specific order of class members:
 * 1. use (traits)
 * 2. Abstract properties (public, protected)
 * 3. Abstract methods (public, protected)
 * 4. Constants (public, protected, private)
 * 5. Static properties (public, protected, private)
 * 6. Static methods (public, protected, private)
 * 7. Properties (public, protected, private)
 * 8. __construct
 * 9. Magic methods (__*)
 * 10. Methods (public, protected, private)
 */

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Steevanb\Sniffs\Classes;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\Sniff
};

class ClassMemberOrderSniff implements Sniff
{
    private const int GROUP_USE = 10;
    private const int GROUP_ABSTRACT_PROPERTY_PUBLIC = 20;
    private const int GROUP_ABSTRACT_PROPERTY_PROTECTED = 21;
    private const int GROUP_ABSTRACT_METHOD_PUBLIC = 30;
    private const int GROUP_ABSTRACT_METHOD_PROTECTED = 31;
    private const int GROUP_CONSTANT_PUBLIC = 40;
    private const int GROUP_CONSTANT_PROTECTED = 41;
    private const int GROUP_CONSTANT_PRIVATE = 42;
    private const int GROUP_STATIC_PROPERTY_PUBLIC = 45;
    private const int GROUP_STATIC_PROPERTY_PROTECTED = 46;
    private const int GROUP_STATIC_PROPERTY_PRIVATE = 47;
    private const int GROUP_STATIC_METHOD_PUBLIC = 50;
    private const int GROUP_STATIC_METHOD_PROTECTED = 51;
    private const int GROUP_STATIC_METHOD_PRIVATE = 52;
    private const int GROUP_PROPERTY_PUBLIC = 60;
    private const int GROUP_PROPERTY_PROTECTED = 61;
    private const int GROUP_PROPERTY_PRIVATE = 62;
    private const int GROUP_CONSTRUCT = 70;
    private const int GROUP_MAGIC_METHOD = 80;
    private const int GROUP_METHOD_PUBLIC = 90;
    private const int GROUP_METHOD_PROTECTED = 91;
    private const int GROUP_METHOD_PRIVATE = 92;

    private const array GROUP_LABELS = [
        self::GROUP_USE => 'trait use',
        self::GROUP_ABSTRACT_PROPERTY_PUBLIC => 'abstract public property',
        self::GROUP_ABSTRACT_PROPERTY_PROTECTED => 'abstract protected property',
        self::GROUP_ABSTRACT_METHOD_PUBLIC => 'abstract public method',
        self::GROUP_ABSTRACT_METHOD_PROTECTED => 'abstract protected method',
        self::GROUP_CONSTANT_PUBLIC => 'public constant',
        self::GROUP_CONSTANT_PROTECTED => 'protected constant',
        self::GROUP_CONSTANT_PRIVATE => 'private constant',
        self::GROUP_STATIC_PROPERTY_PUBLIC => 'public static property',
        self::GROUP_STATIC_PROPERTY_PROTECTED => 'protected static property',
        self::GROUP_STATIC_PROPERTY_PRIVATE => 'private static property',
        self::GROUP_STATIC_METHOD_PUBLIC => 'public static method',
        self::GROUP_STATIC_METHOD_PROTECTED => 'protected static method',
        self::GROUP_STATIC_METHOD_PRIVATE => 'private static method',
        self::GROUP_PROPERTY_PUBLIC => 'public property',
        self::GROUP_PROPERTY_PROTECTED => 'protected property',
        self::GROUP_PROPERTY_PRIVATE => 'private property',
        self::GROUP_CONSTRUCT => '__construct',
        self::GROUP_MAGIC_METHOD => 'magic method',
        self::GROUP_METHOD_PUBLIC => 'public method',
        self::GROUP_METHOD_PROTECTED => 'protected method',
        self::GROUP_METHOD_PRIVATE => 'private method',
    ];

    public function register(): array
    {
        return [T_CLASS];
    }

    public function process(File $phpcsFile, int $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        if (
            array_key_exists('scope_opener', $tokens[$stackPtr]) === false
            || array_key_exists('scope_closer', $tokens[$stackPtr]) === false
        ) {
            return;
        }

        $classOpen = $tokens[$stackPtr]['scope_opener'];
        $classClose = $tokens[$stackPtr]['scope_closer'];
        $classLevel = $tokens[$stackPtr]['level'] + 1;

        $members = $this->collectMembers($phpcsFile, $classOpen, $classClose, $classLevel);

        $previousWeight = 0;
        $previousLabel = '';
        foreach ($members as $member) {
            if ($member['weight'] < $previousWeight) {
                $phpcsFile->addError(
                    '%s must be before %s',
                    $member['ptr'],
                    'InvalidOrder',
                    [self::GROUP_LABELS[$member['weight']], $previousLabel]
                );
            }

            if ($member['weight'] >= $previousWeight) {
                $previousWeight = $member['weight'];
                $previousLabel = self::GROUP_LABELS[$member['weight']];
            }
        }
    }

    /** @return list<array{ptr: int, weight: int}> */
    private function collectMembers(File $phpcsFile, int $classOpen, int $classClose, int $classLevel): array
    {
        $tokens = $phpcsFile->getTokens();
        $members = [];
        $ptr = $classOpen + 1;

        while ($ptr < $classClose) {
            if ($tokens[$ptr]['level'] !== $classLevel) {
                $ptr++;
                continue;
            }

            $code = $tokens[$ptr]['code'];

            if ($code === T_USE) {
                $members[] = ['ptr' => $ptr, 'weight' => self::GROUP_USE];
                $ptr = $phpcsFile->findNext(T_SEMICOLON, $ptr + 1, $classClose);
                if ($ptr === false) {
                    break;
                }
                $ptr++;
                continue;
            }

            if ($code === T_CONST) {
                $visibility = $this->findVisibilityBefore($phpcsFile, $ptr, $classOpen);
                $members[] = [
                    'ptr' => $ptr,
                    'weight' => match ($visibility) {
                    T_PROTECTED => self::GROUP_CONSTANT_PROTECTED,
                    T_PRIVATE => self::GROUP_CONSTANT_PRIVATE,
                    default => self::GROUP_CONSTANT_PUBLIC,
                    }
                ];
                $ptr = $phpcsFile->findNext(T_SEMICOLON, $ptr + 1, $classClose);
                if ($ptr === false) {
                    break;
                }
                $ptr++;
                continue;
            }

            if ($code === T_VARIABLE) {
                $isAbstract = $this->hasModifierBefore($phpcsFile, $ptr, $classOpen, T_ABSTRACT);
                $isStatic = $this->hasModifierBefore($phpcsFile, $ptr, $classOpen, T_STATIC);
                $visibility = $this->findVisibilityBefore($phpcsFile, $ptr, $classOpen);

                if ($isAbstract) {
                    $members[] = [
                        'ptr' => $ptr,
                        'weight' => match ($visibility) {
                        T_PROTECTED => self::GROUP_ABSTRACT_PROPERTY_PROTECTED,
                        default => self::GROUP_ABSTRACT_PROPERTY_PUBLIC,
                        }
                    ];
                } elseif ($isStatic) {
                    $members[] = [
                        'ptr' => $ptr,
                        'weight' => match ($visibility) {
                        T_PROTECTED => self::GROUP_STATIC_PROPERTY_PROTECTED,
                        T_PRIVATE => self::GROUP_STATIC_PROPERTY_PRIVATE,
                        default => self::GROUP_STATIC_PROPERTY_PUBLIC,
                        }
                    ];
                } else {
                    $members[] = [
                        'ptr' => $ptr,
                        'weight' => match ($visibility) {
                        T_PROTECTED => self::GROUP_PROPERTY_PROTECTED,
                        T_PRIVATE => self::GROUP_PROPERTY_PRIVATE,
                        default => self::GROUP_PROPERTY_PUBLIC,
                        }
                    ];
                }

                // Skip past property hooks or semicolon.
                if (array_key_exists('scope_closer', $tokens[$ptr])) {
                    $ptr = $tokens[$ptr]['scope_closer'] + 1;
                } else {
                    $next = $phpcsFile->findNext([T_SEMICOLON, T_OPEN_CURLY_BRACKET], $ptr + 1, $classClose);
                    if ($next === false) {
                        break;
                    }
                    if (
                        $tokens[$next]['code'] === T_OPEN_CURLY_BRACKET
                        && array_key_exists('bracket_closer', $tokens[$next])
                    ) {
                        $ptr = $tokens[$next]['bracket_closer'] + 1;
                    } else {
                        $ptr = $next + 1;
                    }
                }
                continue;
            }

            if ($code === T_FUNCTION) {
                $namePtr = $phpcsFile->findNext(T_WHITESPACE, $ptr + 1, null, true);
                $methodName = $namePtr !== false ? $tokens[$namePtr]['content'] : '';
                $isAbstract = $this->hasModifierBefore($phpcsFile, $ptr, $classOpen, T_ABSTRACT);
                $isStatic = $this->hasModifierBefore($phpcsFile, $ptr, $classOpen, T_STATIC);
                $visibility = $this->findVisibilityBefore($phpcsFile, $ptr, $classOpen);

                if ($isAbstract) {
                    $members[] = [
                        'ptr' => $ptr,
                        'weight' => match ($visibility) {
                        T_PROTECTED => self::GROUP_ABSTRACT_METHOD_PROTECTED,
                        default => self::GROUP_ABSTRACT_METHOD_PUBLIC,
                        }
                    ];
                } elseif ($methodName === '__construct') {
                    $members[] = ['ptr' => $ptr, 'weight' => self::GROUP_CONSTRUCT];
                } elseif (str_starts_with($methodName, '__')) {
                    $members[] = ['ptr' => $ptr, 'weight' => self::GROUP_MAGIC_METHOD];
                } elseif ($isStatic) {
                    $members[] = [
                        'ptr' => $ptr,
                        'weight' => match ($visibility) {
                        T_PROTECTED => self::GROUP_STATIC_METHOD_PROTECTED,
                        T_PRIVATE => self::GROUP_STATIC_METHOD_PRIVATE,
                        default => self::GROUP_STATIC_METHOD_PUBLIC,
                        }
                    ];
                } else {
                    $members[] = [
                        'ptr' => $ptr,
                        'weight' => match ($visibility) {
                        T_PROTECTED => self::GROUP_METHOD_PROTECTED,
                        T_PRIVATE => self::GROUP_METHOD_PRIVATE,
                        default => self::GROUP_METHOD_PUBLIC,
                        }
                    ];
                }

                // Skip past method body or semicolon (abstract).
                if (array_key_exists('scope_closer', $tokens[$ptr])) {
                    $ptr = $tokens[$ptr]['scope_closer'] + 1;
                } else {
                    $next = $phpcsFile->findNext(T_SEMICOLON, $ptr + 1, $classClose);
                    if ($next === false) {
                        break;
                    }
                    $ptr = $next + 1;
                }
                continue;
            }

            $ptr++;
        }

        return $members;
    }

    private function findVisibilityBefore(File $phpcsFile, int $ptr, int $classOpen): int
    {
        $tokens = $phpcsFile->getTokens();
        $search = $ptr - 1;

        while ($search > $classOpen) {
            $code = $tokens[$search]['code'];

            if (in_array($code, [T_PUBLIC, T_PROTECTED, T_PRIVATE], true)) {
                return $code;
            }

            if (
                $code !== T_WHITESPACE
                && $code !== T_ABSTRACT
                && $code !== T_STATIC
                && $code !== T_READONLY
                && $code !== T_STRING
                && $code !== T_NULLABLE
                && $code !== T_TYPE_UNION
                && $code !== T_TYPE_INTERSECTION
                && $code !== T_NULL
                && $code !== T_SELF
                && $code !== T_PARENT
                && $code !== T_NS_SEPARATOR
                && $code !== T_PUBLIC_SET
                && $code !== T_PROTECTED_SET
                && $code !== T_PRIVATE_SET
            ) {
                break;
            }

            $search--;
        }

        return T_PUBLIC;
    }

    private function hasModifierBefore(File $phpcsFile, int $ptr, int $classOpen, int $modifier): bool
    {
        $tokens = $phpcsFile->getTokens();
        $search = $ptr - 1;

        while ($search > $classOpen) {
            $code = $tokens[$search]['code'];

            if ($code === $modifier) {
                return true;
            }

            if (
                $code !== T_WHITESPACE
                && $code !== T_PUBLIC
                && $code !== T_PROTECTED
                && $code !== T_PRIVATE
                && $code !== T_ABSTRACT
                && $code !== T_STATIC
                && $code !== T_READONLY
                && $code !== T_STRING
                && $code !== T_NULLABLE
                && $code !== T_TYPE_UNION
                && $code !== T_TYPE_INTERSECTION
                && $code !== T_NULL
                && $code !== T_SELF
                && $code !== T_PARENT
                && $code !== T_NS_SEPARATOR
                && $code !== T_PUBLIC_SET
                && $code !== T_PROTECTED_SET
                && $code !== T_PRIVATE_SET
            ) {
                break;
            }

            $search--;
        }

        return false;
    }
}
