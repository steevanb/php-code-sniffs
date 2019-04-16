<?php

declare(strict_types=1);

namespace steevanb\PhpCodeSniffs\Steevanb\Sniffs\Metrics;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\Sniff
};

/**
 * Generic_Sniffs_Metrics_NestingLevelSniff fork
 * Allow some methods to not respect nesting level
 */
class NestingLevelSniff implements Sniff
{
    /** @var string[] */
    protected static $allowedNestingLevelMethods = [];

    public static function addAllowedNestingLevelMethods(string $fileName, string $method): void
    {
        static::$allowedNestingLevelMethods[] = $fileName . '::' . $method;
    }

    /** A nesting level higher than this value will throw a warning */
    public $nestingLevel = 5;

    /** A nesting level higher than this value will throw an error */
    public $absoluteNestingLevel = 10;

    public function register(): array
    {
        return [T_FUNCTION];
    }

    /** @param int $stackPtr */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        // Ignore abstract methods.
        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            return;
        }

        // Detect start and end of this function definition.
        $start = $tokens[$stackPtr]['scope_opener'];
        $end   = $tokens[$stackPtr]['scope_closer'];

        $nestingLevel = 0;

        // Find the maximum nesting level of any token in the function.
        for ($i = ($start + 1); $i < $end; $i++) {
            $level = $tokens[$i]['level'];
            if ($nestingLevel < $level) {
                $nestingLevel = $level;
            }
        }

        // We subtract the nesting level of the function itself.
        $nestingLevel = ($nestingLevel - $tokens[$stackPtr]['level'] - 1);

        if ($nestingLevel > $this->absoluteNestingLevel) {
            $error = 'Function\'s nesting level (%s) exceeds allowed maximum of %s';
            $data  = [
                $nestingLevel,
                $this->absoluteNestingLevel,
            ];
            $phpcsFile->addError($error, $stackPtr, 'MaxExceeded', $data);
        } elseif ($nestingLevel > $this->nestingLevel) {
            if (
                in_array(
                    basename($phpcsFile->getFilename())
                        . '::'
                        . $tokens[$phpcsFile->findNext(T_STRING, $stackPtr)]['content'],
                    static::$allowedNestingLevelMethods
                ) === false
            ) {
                $warning = 'Function\'s nesting level (%s) exceeds %s; consider refactoring the function';
                $data = [$nestingLevel, $this->nestingLevel];
                $phpcsFile->addWarning($warning, $stackPtr, 'TooHigh', $data);
            }
        }
    }
}
