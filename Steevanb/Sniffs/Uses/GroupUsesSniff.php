<?php

declare(strict_types=1);

/**
 * Group use on 1st, 2nd or 3rd level
 * Example:
 * use App\{
 *     Entity\Foo
 *     Repository\FooRepository
 * };
 * use Symfony\Component\HttpFoundation\{
 *     Request,
 *     Response
 * };
 * Call addThreeLevelsPrefix() to force this namespace to be regrouped at 3rd level
 */
class Steevanb_Sniffs_Uses_GroupUsesSniff implements PHP_CodeSniffer_Sniff
{
    protected static $thirdLevelPrefixs = [];

    public static function addThirdLevelPrefix(string $prefix): void
    {
        static::$thirdLevelPrefixs[] = $prefix;
    }

    public static function addSymfonyPrefixs(): void
    {
        static::addThirdLevelPrefix('Symfony\\Component');
        static::addThirdLevelPrefix('Symfony\\Bundle');
        static::addThirdLevelPrefix('Sensio\\Bundle');
        static::addThirdLevelPrefix('Doctrine\\Common');
    }

    /** @var string[] */
    protected $uses = [];

    public function register(): array
    {
        return [T_USE, T_OPEN_USE_GROUP];
    }

    /** @param int $stackPtr */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr): void
    {
        if ($phpcsFile->getTokens()[$stackPtr]['type'] === 'T_USE') {
            $useGroupPrefix = $this->getUseGroupPrefix($phpcsFile, $stackPtr);
            if ($useGroupPrefix !== null) {
                $this->validateUseGroupPrefixName($phpcsFile, $stackPtr, $useGroupPrefix);
            } else {
                $currentUse = $this->getCurrentUse($phpcsFile, $stackPtr);
                if ($currentUse !== null) {
                    $this->validateUse($phpcsFile, $stackPtr, $currentUse);
                }
            }
        } else {
            $comaPtr = $phpcsFile->findNext(
                [T_COMMA],
                $stackPtr,
                $phpcsFile->findNext([T_CLOSE_USE_GROUP], $stackPtr + 1)
            );
            $errorLines = [];
            while (is_int($comaPtr)) {
                $nextToken = $phpcsFile->getTokens()[$comaPtr + 1];
                if (
                    $nextToken['type'] === 'T_WHITESPACE'
                    && strpos($nextToken['content'], "\n") === false
                    && in_array($nextToken['line'], $errorLines) === false
                ) {
                    $phpcsFile->addError(
                        'Only one use per line allowed.',
                        $comaPtr + 1
                    );
                    $errorLines[] = $nextToken['line'];
                }

                $comaPtr = $phpcsFile->findNext(
                    [T_COMMA],
                    $comaPtr + 1,
                    $phpcsFile->findNext([T_CLOSE_USE_GROUP], $comaPtr)
                );
            }
        }
    }

    protected function getCurrentUse(PHP_CodeSniffer_File $phpcsFile, int $stackPtr): ?string
    {
        $startUse = $phpcsFile->findNext(T_STRING, $stackPtr);
        $tokenEndLine = $phpcsFile->findNext(T_SEMICOLON, $startUse + 1, null, false, ';');

        $return = null;
        for ($index = $startUse; $index < $tokenEndLine; $index++) {
            $currentToken = $phpcsFile->getTokens()[$index];
            if ($currentToken['code'] === T_OPEN_USE_GROUP) {
                $return = null;
                break;
            }
            $return .= $currentToken['content'];
        }

        return $return;
    }

    protected function getUseGroupPrefix(PHP_CodeSniffer_File $phpcsFile, int $stackPtr): ?string
    {
        $return = null;
        $nextStackPtr = $stackPtr;
        $urrentUseString = null;
        do {
            $nextStackPtr++;
            $currentToken = $phpcsFile->getTokens()[$nextStackPtr];
            if (is_array($currentToken) && $currentToken['code'] === T_OPEN_USE_GROUP) {
                $return = $urrentUseString;
                break;
            }
            $urrentUseString .= $currentToken['content'];
        } while ($currentToken['code'] !== T_SEMICOLON);

        return $return === null ? null : trim(rtrim($return, '\\'));
    }

    protected function validateUseGroupPrefixName(PHP_CodeSniffer_File $phpcsFile, int $stackPtr, string $prefix): self
    {
        $is3parts = false;
        foreach (static::$thirdLevelPrefixs as $usePrefix3part) {
            if (substr($usePrefix3part, 0, strlen($prefix)) === $prefix) {
                $phpcsFile->addError(
                    'Use group "'
                        . $prefix
                        . '" is invalid, you must group at 3rd level for '
                        . implode(', ', static::$thirdLevelPrefixs),
                    $stackPtr
                );
            } elseif (substr($prefix, 0, strlen($usePrefix3part)) === $usePrefix3part) {
                $is3parts = true;
                $countBackSlash = substr_count($prefix, '\\');
                if ($countBackSlash === 1 || $countBackSlash > 2) {
                    $allowedPrefix = substr($prefix, 0, strpos($prefix, '\\', strlen($usePrefix3part)) + 1);
                    $phpcsFile->addError(
                        '"' . $prefix . '" use group is invalid, use "' . $allowedPrefix . '" instead.',
                        $stackPtr
                    );
                    break;
                }
            }
        }
        if ($is3parts === false && substr_count($prefix, '\\') > 1) {
            $allowedPrefix = substr($prefix, 0, strpos($prefix, '\\', strpos($prefix, '\\') + 1) + 1);
            $phpcsFile->addError(
                '"' . $prefix . '" use group is invalid, use "' . rtrim($allowedPrefix, '\\') . '" instead',
                $stackPtr
            );
        }

        return $this;
    }

    protected function validateUse(PHP_CodeSniffer_File $phpcsFile, int $stackPtr, string $useToValidate): self
    {
        foreach ($this->uses[$phpcsFile->getFilename()] ?? [] as $use) {
            $prefix = null;
            foreach (static::$thirdLevelPrefixs as $usePrefix3part) {
                if (substr($use, 0, strlen($usePrefix3part)) === $usePrefix3part) {
                    $prefix = substr($use, 0, strpos($use, '\\', strlen($usePrefix3part)) + 1);
                    break;
                }
            }
            if ($prefix === null) {
                $useParts = explode('\\', $use);
                if (count($useParts) >= 3) {
                    $prefix = implode('\\', array_slice($useParts, 0, 2)) . '\\';
                } else {
                    $prefix = null;
                }
            }

            if ($prefix !== null && substr($useToValidate, 0, strlen($prefix)) === $prefix) {
                $phpcsFile->addError(
                    'You must group the use "' . $useToValidate . '" in "' . rtrim($prefix, '\\') . '".',
                    $stackPtr
                );
                break;
            }

        }

        if (array_key_exists($phpcsFile->getFilename(), $this->uses) === false) {
            $this->uses[$phpcsFile->getFilename()] = [];
        }
        $this->uses[$phpcsFile->getFilename()][] = $useToValidate;

        return $this;
    }
}
