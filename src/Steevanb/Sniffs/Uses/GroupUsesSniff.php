<?php

declare(strict_types=1);

namespace steevanb\PhpCodeSniffs\Steevanb\Sniffs\Uses;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\Sniff
};

/**
 * Group use on 1st, 2nd (default), 3rd level or 4th level
 * Example:
 * use App\{
 *     Entity\Foo
 *     Repository\FooRepository
 * };
 * use Symfony\Component\HttpFoundation\{
 *     Request,
 *     Response
 * };
 *
 * Call addFirstLevelPrefix() to force this namespace to be regrouped at 1st level
 * Call addThirdLevelPrefix() to force this namespace to be regrouped at 3rd level
 */
class GroupUsesSniff implements Sniff
{
    /** @var string[] */
    public $firstLevelPrefixes = [];

    /** @var string[] */
    public $thirdLevelPrefixes = [];

    /** @var string[] */
    public $fourthLevelPrefixes = [];

    /** @var string[] */
    protected $uses = [];

    /** @return string[] */
    public function register(): array
    {
        return [T_USE, T_OPEN_USE_GROUP, T_CLOSE_USE_GROUP];
    }

    /** @param int $stackPtr */
    public function process(File $phpcsFile, $stackPtr): void
    {
        if ($phpcsFile->getTokens()[$stackPtr]['type'] === 'T_USE') {
            $this->processUse($phpcsFile, $stackPtr);
        } elseif ($phpcsFile->getTokens()[$stackPtr]['type'] === 'T_OPEN_USE_GROUP') {
            $this->processOpenUseGroup($phpcsFile, $stackPtr);
        } else {
            $this->processCloseUseGroup($phpcsFile, $stackPtr);
        }
    }

    protected function processUse(File $phpcsFile, int $stackPtr): self
    {
        $useGroupPrefix = $this->getUseGroupPrefix($phpcsFile, $stackPtr);
        if (is_string($useGroupPrefix)) {
            $this->validateUseGroupPrefixName($phpcsFile, $stackPtr, $useGroupPrefix);
        } else {
            $currentUse = $this->getCurrentUse($phpcsFile, $stackPtr);
            if (is_string($currentUse)) {
                $this->validateUse($phpcsFile, $stackPtr, $currentUse);
            }
        }

        return $this;
    }

    protected function processOpenUseGroup(File $phpcsFile, int $stackPtr): self
    {
        $nextToken = $phpcsFile->getTokens()[$stackPtr + 1];
        if ($nextToken['type'] !== 'T_WHITESPACE' || $nextToken['content'] !== "\n") {
            $phpcsFile->addError(
                'Open use group brace should have a new line after',
                $stackPtr + 1,
                'LineAfterOpenBrace'
            );
        }

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
                    $comaPtr + 1,
                    'OneUsePerLine'
                );
                $errorLines[] = $nextToken['line'];
            }

            $comaPtr = $phpcsFile->findNext(
                [T_COMMA],
                $comaPtr + 1,
                $phpcsFile->findNext([T_CLOSE_USE_GROUP], $comaPtr)
            );
        }

        return $this;
    }

    protected function processCloseUseGroup(File $phpcsFile, int $stackPtr): self
    {
        $previousToken = $phpcsFile->getTokens()[$stackPtr - 1];
        if ($previousToken['type'] !== 'T_WHITESPACE' || $previousToken['content'] !== "\n") {
            $phpcsFile->addError(
                'Use group close brace should be on it\'s own line whithout spaces before',
                $stackPtr - 1,
                'CloseBraceOwnLine'
            );
        }

        return $this;
    }

    protected function getCurrentUse(File $phpcsFile, int $stackPtr): ?string
    {
        $startUse = $phpcsFile->findNext(T_STRING, $stackPtr);
        if (is_int($startUse) === false) {
            return null;
        }
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

    protected function getUseGroupPrefix(File $phpcsFile, int $stackPtr): ?string
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

        return ($return === null) ? null : trim(rtrim($return, '\\'));
    }

    protected function validateUseGroupPrefixName(File $phpcsFile, int $stackPtr, string $prefix): self
    {
        $is3parts = false;
        foreach ($this->thirdLevelPrefixes as $usePrefix3parts) {
            if (substr($usePrefix3parts, 0, strlen($prefix)) === $prefix) {
                $this->addGroupAtLevelError($phpcsFile, $stackPtr, '3rd', $prefix, $this->thirdLevelPrefixes);
            } elseif (substr($prefix, 0, strlen($usePrefix3parts)) === $usePrefix3parts) {
                $is3parts = true;
                $countBackSlash = substr_count($prefix, '\\');
                if ($countBackSlash === 1 || $countBackSlash > 2) {
                    $this->addBadRegroupmentError($phpcsFile, $stackPtr, $prefix, strlen($usePrefix3parts));
                    break;
                }
            }
        }

        if ($is3parts === false) {
            $is4parts = false;
            foreach ($this->fourthLevelPrefixes as $usePrefix4parts) {
                if (substr($usePrefix4parts, 0, strlen($prefix)) === $prefix) {
                    $this->addGroupAtLevelError($phpcsFile, $stackPtr, '4th', $prefix, $this->fourthLevelPrefixes);
                } elseif (substr($prefix, 0, strlen($usePrefix4parts)) === $usePrefix4parts) {
                    $is4parts = true;
                    $countBackSlash = substr_count($prefix, '\\');
                    if ($countBackSlash < 2 || $countBackSlash > 3) {
                        $this->addBadRegroupmentError($phpcsFile, $stackPtr, $prefix, strlen($usePrefix4parts));
                        break;
                    }
                }
            }
        }

        if ($is3parts === false && $is4parts === false && substr_count($prefix, '\\') > 1) {
            $this->addBadRegroupmentError($phpcsFile, $stackPtr, $prefix, strpos($prefix, '\\') + 1);
        }

        return $this;
    }

    protected function addBadRegroupmentError(File $phpcsFile, int $stackPtr, string $prefix, int $offset): self
    {
        $allowedPrefix = substr($prefix, 0, strpos($prefix, '\\', $offset));
        $phpcsFile->addError(
            '"' . $prefix . '" use group is invalid, use "' . $allowedPrefix . '" instead.',
            $stackPtr,
            'BadRegroupment'
        );

        return $this;
    }

    protected function addGroupAtLevelError(
        File $phpcsFile,
        int $stackPtr,
        string $level,
        string $prefix,
        array $prefixes
    ): self {
        $phpcsFile->addError(
            'Use group "'
                . $prefix
                . '" is invalid, you must group at '
                . $level
                . ' level for '
                . implode(', ', $prefixes),
            $stackPtr,
            'GroupAt3rdLevel'
        );

        return $this;
    }

    protected function validateUse(File $phpcsFile, int $stackPtr, string $useToValidate): self
    {
        foreach ($this->uses[$phpcsFile->getFilename()] ?? [] as $use) {
            $prefix = null;

            $usePrefixes = array_merge(
                $this->firstLevelPrefixes,
                $this->thirdLevelPrefixes,
                $this->fourthLevelPrefixes
            );
            foreach ($usePrefixes as $usePefix) {
                if (substr($use, 0, strlen($usePefix)) === $usePefix) {
                    $prefix = substr($use, 0, strpos($use, '\\', strlen($usePefix)) + 1);
                    break;
                }
            }

            if ($prefix === null) {
                $useParts = explode('\\', $use);
                if (count($useParts) >= 3) {
                    $prefix = implode('\\', array_slice($useParts, 0, 2)) . '\\';
                }
            }

            if (is_string($prefix) && substr($useToValidate, 0, strlen($prefix)) === $prefix) {
                $phpcsFile->addError(
                    'You must group the use "' . $useToValidate . '" under "' . rtrim($prefix, '\\') . '".',
                    $stackPtr,
                    'MustRegroup'
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
