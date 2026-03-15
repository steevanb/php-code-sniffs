<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Steevanb\Sniffs\Uses;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\Sniff
};

/**
 * Enforce grouped use statements for namespaces sharing a configured prefix.
 *
 * Example configuration:
 * <rule ref="Steevanb.Uses.GroupUses">
 *     <properties>
 *         <property name="groupPrefixes" type="array">
 *             <element value="App\Foo"/>
 *             <element value="Symfony\Component\HttpFoundation"/>
 *         </property>
 *     </properties>
 * </rule>
 *
 * With groupPrefixes = ["App\Foo"], the following is invalid:
 *     use App\Foo\Bar;
 *     use App\Foo\Baz;
 *
 * And must be written as:
 *     use App\Foo\{
 *         Bar,
 *         Baz
 *     };
 */
class GroupUsesSniff implements Sniff
{
    /** @var string[] */
    public $groupPrefixes = [];

    public function register(): array
    {
        return [T_OPEN_TAG];
    }

    public function process(File $phpcsFile, int $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();
        $uses = $this->collectUseStatements($phpcsFile);

        $this->validateUngroupedUses($phpcsFile, $uses);
        $this->validateGroupedUseFormat($phpcsFile, $uses);

        // Only process once per file.
        return;
    }

    /**
     * @return list<array{
     *     ptr: int,
     *     name: string,
     *     isGrouped: bool,
     *     groupPrefix: string|null,
     *     groupMembers: list<string>
     * }>
     */
    private function collectUseStatements(File $phpcsFile): array
    {
        $tokens = $phpcsFile->getTokens();
        $uses = [];
        $ptr = 0;

        while ($ptr < $phpcsFile->numTokens) {
            $ptr = $phpcsFile->findNext(T_USE, $ptr);
            if ($ptr === false) {
                break;
            }

            // Skip trait use and closure use.
            if ($this->isNamespaceUse($phpcsFile, $ptr) === false) {
                $ptr++;
                continue;
            }

            $semicolon = $phpcsFile->findNext(T_SEMICOLON, $ptr + 1);
            if ($semicolon === false) {
                break;
            }

            $groupOpen = $phpcsFile->findNext(T_OPEN_USE_GROUP, $ptr + 1, $semicolon);

            if ($groupOpen !== false) {
                $groupClose = $phpcsFile->findNext(T_CLOSE_USE_GROUP, $groupOpen + 1);
                if ($groupClose === false) {
                    break;
                }

                $prefix = $this->getTokenContent($phpcsFile, $ptr + 1, $groupOpen);
                $members = $this->getGroupMembers($phpcsFile, $groupOpen, $groupClose);

                $uses[] = [
                    'ptr' => $ptr,
                    'name' => $prefix,
                    'isGrouped' => true,
                    'groupPrefix' => rtrim($prefix, '\\'),
                    'groupMembers' => $members,
                ];

                $ptr = $semicolon + 1;
            } else {
                $name = $this->getTokenContent($phpcsFile, $ptr + 1, $semicolon);

                $uses[] = [
                    'ptr' => $ptr,
                    'name' => $name,
                    'isGrouped' => false,
                    'groupPrefix' => null,
                    'groupMembers' => [],
                ];

                $ptr = $semicolon + 1;
            }
        }

        return $uses;
    }

    /** @param list<array{ptr: int, name: string, isGrouped: bool, groupPrefix: string|null, groupMembers: list<string>}> $uses */
    private function validateUngroupedUses(File $phpcsFile, array $uses): void
    {
        $ungrouped = [];
        foreach ($uses as $use) {
            if ($use['isGrouped'] === false) {
                $ungrouped[] = $use;
            }
        }

        if (count($ungrouped) < 2) {
            return;
        }

        foreach ($this->groupPrefixes as $prefix) {
            $prefixWithSeparator = rtrim($prefix, '\\') . '\\';
            $matching = [];
            foreach ($ungrouped as $use) {
                if (str_starts_with($use['name'], $prefixWithSeparator)) {
                    $matching[] = $use;
                }
            }

            if (count($matching) >= 2) {
                foreach ($matching as $use) {
                    $phpcsFile->addError(
                        'Use "%s" must be grouped under "%s"',
                        $use['ptr'],
                        'MustGroup',
                        [$use['name'], $prefix]
                    );
                }
            }
        }
    }

    /** @param list<array{ptr: int, name: string, isGrouped: bool, groupPrefix: string|null, groupMembers: list<string>}> $uses */
    private function validateGroupedUseFormat(File $phpcsFile, array $uses): void
    {
        $tokens = $phpcsFile->getTokens();

        foreach ($uses as $use) {
            if ($use['isGrouped'] === false) {
                continue;
            }

            $groupOpen = $phpcsFile->findNext(T_OPEN_USE_GROUP, $use['ptr'] + 1);
            if ($groupOpen === false) {
                continue;
            }

            $groupClose = $phpcsFile->findNext(T_CLOSE_USE_GROUP, $groupOpen + 1);
            if ($groupClose === false) {
                continue;
            }

            // Open brace must be followed by a newline.
            $afterOpen = $groupOpen + 1;
            if (
                array_key_exists($afterOpen, $tokens)
                && (
                    $tokens[$afterOpen]['code'] !== T_WHITESPACE
                    || str_contains($tokens[$afterOpen]['content'], "\n") === false
                )
            ) {
                $phpcsFile->addError(
                    'Each grouped use must be on its own line',
                    $groupOpen,
                    'OneUsePerLine'
                );
            }

            // Close brace must be on its own line.
            $beforeClose = $groupClose - 1;
            if (
                array_key_exists($beforeClose, $tokens)
                && ($tokens[$beforeClose]['code'] !== T_WHITESPACE || $tokens[$beforeClose]['content'] !== "\n")
            ) {
                $phpcsFile->addError(
                    'Use group close brace must be on its own line',
                    $groupClose,
                    'CloseBraceOwnLine'
                );
            }

            // Each comma must be followed by a newline (one use per line).
            $commaPtr = $phpcsFile->findNext(T_COMMA, $groupOpen + 1, $groupClose);
            while ($commaPtr !== false) {
                $afterComma = $commaPtr + 1;
                if (
                    array_key_exists($afterComma, $tokens)
                    && $tokens[$afterComma]['code'] === T_WHITESPACE
                    && str_contains($tokens[$afterComma]['content'], "\n") === false
                ) {
                    $phpcsFile->addError(
                        'Each grouped use must be on its own line',
                        $commaPtr,
                        'OneUsePerLine'
                    );
                }

                $commaPtr = $phpcsFile->findNext(T_COMMA, $commaPtr + 1, $groupClose);
            }
        }
    }

    private function isNamespaceUse(File $phpcsFile, int $stackPtr): bool
    {
        $tokens = $phpcsFile->getTokens();

        // Inside a class/trait/interface = trait use.
        if ($phpcsFile->hasCondition($stackPtr, [T_CLASS, T_TRAIT, T_INTERFACE, T_ENUM, T_ANON_CLASS])) {
            return false;
        }

        // Closure use.
        $prev = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
        if ($prev !== false && $tokens[$prev]['code'] === T_CLOSE_PARENTHESIS) {
            return false;
        }

        return true;
    }

    private function getTokenContent(File $phpcsFile, int $start, int $end): string
    {
        $tokens = $phpcsFile->getTokens();
        $content = '';

        for ($i = $start; $i < $end; $i++) {
            if ($tokens[$i]['code'] !== T_WHITESPACE) {
                $content .= $tokens[$i]['content'];
            }
        }

        return $content;
    }

    /** @return list<string> */
    private function getGroupMembers(File $phpcsFile, int $openPtr, int $closePtr): array
    {
        $tokens = $phpcsFile->getTokens();
        $members = [];
        $current = '';

        for ($i = $openPtr + 1; $i < $closePtr; $i++) {
            $code = $tokens[$i]['code'];
            if ($code === T_COMMA) {
                $trimmed = trim($current);
                if ($trimmed !== '') {
                    $members[] = $trimmed;
                }

                $current = '';
            } elseif ($code === T_STRING || $code === T_NS_SEPARATOR) {
                $current .= $tokens[$i]['content'];
            }
        }

        $trimmed = trim($current);
        if ($trimmed !== '') {
            $members[] = $trimmed;
        }

        return $members;
    }
}
