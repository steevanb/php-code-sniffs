<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Steevanb\Sniffs\Classes;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\Sniff
};

class TraitUseAlphabeticalOrderSniff implements Sniff
{
    public function register(): array
    {
        return [T_CLASS];
    }

    public function process(File $phpcsFile, int $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['scope_opener'], $tokens[$stackPtr]['scope_closer']) === false) {
            return;
        }

        $classOpen = $tokens[$stackPtr]['scope_opener'];
        $classClose = $tokens[$stackPtr]['scope_closer'];
        $entries = $this->collectTraitUses($phpcsFile, $classOpen, $classClose);

        $previousName = '';
        $previousIndex = 0;
        foreach ($entries as $index => $entry) {
            if ($previousName !== '' && strcasecmp($entry['name'], $previousName) < 0) {
                $fix = $phpcsFile->addFixableError(
                    'Trait use "%s" must be before "%s" (alphabetical order)',
                    $entry['nameStart'],
                    'InvalidOrder',
                    [$entry['name'], $previousName]
                );

                if ($fix === true) {
                    $this->swapTraitNames($phpcsFile, $entries[$previousIndex], $entry);
                }

                return;
            }

            if (strcasecmp($entry['name'], $previousName) >= 0) {
                $previousName = $entry['name'];
                $previousIndex = $index;
            }
        }
    }

    /** @return list<array{nameStart: int, nameEnd: int, name: string}> */
    private function collectTraitUses(File $phpcsFile, int $classOpen, int $classClose): array
    {
        $tokens = $phpcsFile->getTokens();
        $classLevel = $tokens[$classOpen]['level'] + 1;
        $entries = [];
        $ptr = $classOpen + 1;

        while ($ptr < $classClose) {
            if ($tokens[$ptr]['level'] !== $classLevel || $tokens[$ptr]['code'] !== T_USE) {
                $ptr++;
                continue;
            }

            $semicolonOrBrace = $phpcsFile->findNext([T_SEMICOLON, T_OPEN_CURLY_BRACKET], $ptr + 1, $classClose);
            if ($semicolonOrBrace === false) {
                break;
            }

            $nameStart = null;
            $nameEnd = null;
            $name = '';
            $namePtr = $ptr + 1;

            while ($namePtr < $semicolonOrBrace) {
                $code = $tokens[$namePtr]['code'];
                if ($code === T_COMMA) {
                    if ($nameStart !== null) {
                        $entries[] = ['nameStart' => $nameStart, 'nameEnd' => $nameEnd, 'name' => $name];
                    }
                    $nameStart = null;
                    $nameEnd = null;
                    $name = '';
                } elseif ($code === T_STRING || $code === T_NS_SEPARATOR) {
                    if ($nameStart === null) {
                        $nameStart = $namePtr;
                    }
                    $nameEnd = $namePtr;
                    $name .= $tokens[$namePtr]['content'];
                }
                $namePtr++;
            }

            if ($nameStart !== null) {
                $entries[] = ['nameStart' => $nameStart, 'nameEnd' => $nameEnd, 'name' => $name];
            }

            if ($tokens[$semicolonOrBrace]['code'] === T_OPEN_CURLY_BRACKET) {
                if (isset($tokens[$semicolonOrBrace]['bracket_closer'])) {
                    $ptr = $tokens[$semicolonOrBrace]['bracket_closer'] + 1;
                } else {
                    break;
                }
            } else {
                $ptr = $semicolonOrBrace + 1;
            }
        }

        return $entries;
    }

    /**
     * @param array{nameStart: int, nameEnd: int, name: string} $a
     * @param array{nameStart: int, nameEnd: int, name: string} $b
     */
    private function swapTraitNames(File $phpcsFile, array $a, array $b): void
    {
        $phpcsFile->fixer->beginChangeset();

        for ($i = $a['nameStart']; $i <= $a['nameEnd']; $i++) {
            $phpcsFile->fixer->replaceToken($i, $i === $a['nameStart'] ? $b['name'] : '');
        }

        for ($i = $b['nameStart']; $i <= $b['nameEnd']; $i++) {
            $phpcsFile->fixer->replaceToken($i, $i === $b['nameStart'] ? $a['name'] : '');
        }

        $phpcsFile->fixer->endChangeset();
    }
}
