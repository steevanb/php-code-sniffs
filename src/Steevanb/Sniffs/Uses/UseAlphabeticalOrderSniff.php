<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Steevanb\Sniffs\Uses;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\Sniff
};

class UseAlphabeticalOrderSniff implements Sniff
{
    public function register(): array
    {
        return [T_OPEN_TAG];
    }

    public function process(File $phpcsFile, int $stackPtr): void
    {
        $names = $this->collectUseNames($phpcsFile);

        $previousName = '';
        foreach ($names as $entry) {
            if ($previousName !== '' && strcasecmp($entry['name'], $previousName) < 0) {
                $phpcsFile->addError(
                    'Use import "%s" must be before "%s" (alphabetical order)',
                    $entry['ptr'],
                    'InvalidOrder',
                    [$entry['name'], $previousName]
                );

                return;
            }

            $previousName = $entry['name'];
        }
    }

    /** @return list<array{ptr: int, name: string}> */
    private function collectUseNames(File $phpcsFile): array
    {
        $tokens = $phpcsFile->getTokens();
        $names = [];
        $ptr = 0;

        while ($ptr < $phpcsFile->numTokens) {
            $ptr = $phpcsFile->findNext(T_USE, $ptr);
            if ($ptr === false) {
                break;
            }

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

                $prefix = rtrim($this->getTokenContent($phpcsFile, $ptr + 1, $groupOpen), '\\');
                $members = $this->getGroupMembers($phpcsFile, $groupOpen, $groupClose);

                foreach ($members as $member) {
                    $names[] = ['ptr' => $ptr, 'name' => $prefix . '\\' . $member];
                }

                $ptr = $semicolon + 1;
            } else {
                $name = $this->getTokenContent($phpcsFile, $ptr + 1, $semicolon);

                if ($name !== '') {
                    $names[] = ['ptr' => $ptr, 'name' => $name];
                }

                $ptr = $semicolon + 1;
            }
        }

        return $names;
    }

    private function isNamespaceUse(File $phpcsFile, int $stackPtr): bool
    {
        $tokens = $phpcsFile->getTokens();

        if ($phpcsFile->hasCondition($stackPtr, [T_CLASS, T_TRAIT, T_INTERFACE, T_ENUM, T_ANON_CLASS])) {
            return false;
        }

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
            } elseif ($code !== T_WHITESPACE) {
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
