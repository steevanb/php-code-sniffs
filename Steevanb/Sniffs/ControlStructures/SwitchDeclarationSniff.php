<?php

/**
 * PSR2_Sniffs_ControlStructures_SwitchDeclarationSniff fork
 * Remove forced new line after case and default
 */
class Steevanb_Sniffs_ControlStructures_SwitchDeclarationSniff implements PHP_CodeSniffer_Sniff
{
    public $indent = 4;

    public function register(): array
    {
        return [T_SWITCH];
    }

    /** @param int $stackPtr */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        // We can't process SWITCH statements unless we know where they start and end.
        if (isset($tokens[$stackPtr]['scope_opener']) === false
            || isset($tokens[$stackPtr]['scope_closer']) === false
        ) {
            return;
        }

        $switch = $tokens[$stackPtr];
        $nextCase = $stackPtr;
        $caseAlignment = ($switch['column'] + $this->indent);
        $caseCount = 0;

        while (($nextCase = $this->findNextCase($phpcsFile, ($nextCase + 1), $switch['scope_closer'])) !== false) {
            if ($tokens[$nextCase]['code'] === T_DEFAULT) {
                $type = 'default';
            } else {
                $type = 'case';
                $caseCount++;
            }

            if ($tokens[$nextCase]['content'] !== strtolower($tokens[$nextCase]['content'])) {
                $expected = strtolower($tokens[$nextCase]['content']);
                $error = strtoupper($type) . ' keyword must be lowercase; expected "%s" but found "%s"';
                $data = [$expected, $tokens[$nextCase]['content']];

                $fix = $phpcsFile->addFixableError($error, $nextCase, $type . 'NotLower', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken($nextCase, $expected);
                }
            }

            if (
                $type === 'case'
                && ($tokens[($nextCase + 1)]['code'] !== T_WHITESPACE
                || $tokens[($nextCase + 1)]['content'] !== ' ')
            ) {
                $error = 'CASE keyword must be followed by a single space';
                $fix   = $phpcsFile->addFixableError($error, $nextCase, 'SpacingAfterCase');
                if ($fix === true) {
                    if ($tokens[($nextCase + 1)]['code'] !== T_WHITESPACE) {
                        $phpcsFile->fixer->addContent($nextCase, ' ');
                    } else {
                        $phpcsFile->fixer->replaceToken(($nextCase + 1), ' ');
                    }
                }
            }

            $opener     = $tokens[$nextCase]['scope_opener'];
            $nextCloser = $tokens[$nextCase]['scope_closer'];
            if ($tokens[$opener]['code'] === T_COLON) {
                if ($tokens[($opener - 1)]['code'] === T_WHITESPACE) {
                    $error = 'There must be no space before the colon in a ' . strtoupper($type) . ' statement';
                    $fix   = $phpcsFile->addFixableError($error, $nextCase, 'SpaceBeforeColon' . strtoupper($type));
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($opener - 1), '');
                    }
                }

                if ($tokens[$nextCloser]['scope_condition'] === $nextCase) {
                    // Only need to check some things once, even if the
                    // closer is shared between multiple case statements, or even
                    // the default case.
                    $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($nextCloser - 1), $nextCase, true);
                    if ($tokens[$prev]['line'] !== $tokens[$nextCloser]['line']) {
                        $diff = ($caseAlignment + $this->indent - $tokens[$nextCloser]['column']);
                        if ($diff !== 0) {
                            $error = 'Terminating statement must be indented to the same level as the CASE body';
                            $fix   = $phpcsFile->addFixableError($error, $nextCloser, 'BreakIndent');
                            if ($fix === true) {
                                if ($diff > 0) {
                                    $phpcsFile->fixer->addContentBefore($nextCloser, str_repeat(' ', $diff));
                                } else {
                                    $phpcsFile->fixer->substrToken(($nextCloser - 1), 0, $diff);
                                }
                            }
                        }
                    }
                }
            } else {
                $error = strtoupper($type) . ' statements must be defined using a colon';
                $phpcsFile->addError($error, $nextCase, 'WrongOpener' . $type);
            }

            // We only want cases from here on in.
            if ($type !== 'case') {
                continue;
            }

            $nextCode = $phpcsFile->findNext(
                T_WHITESPACE,
                ($tokens[$nextCase]['scope_opener'] + 1),
                $nextCloser,
                true
            );

            if ($tokens[$nextCode]['code'] !== T_CASE && $tokens[$nextCode]['code'] !== T_DEFAULT) {
                // This case statement has content. If the next case or default comes
                // before the closer, it means we dont have a terminating statement
                // and instead need a comment.
                $nextCode = $this->findNextCase($phpcsFile, ($tokens[$nextCase]['scope_opener'] + 1), $nextCloser);
                if ($nextCode !== false) {
                    $prevCode = $phpcsFile->findPrevious(T_WHITESPACE, ($nextCode - 1), $nextCase, true);
                    if ($tokens[$prevCode]['code'] !== T_COMMENT) {
                        $error = 'There must be a comment when fall-through is intentional in a non-empty case body';
                        $phpcsFile->addError($error, $nextCase, 'TerminatingComment');
                    }
                }
            }
        }
    }

    /**
     * Find the next CASE or DEFAULT statement from a point in the file.
     *
     * Note that nested switches are ignored.
     *
     * @param int $stackPtr The position to start looking at.
     * @param int $end The position to stop looking at.
     *
     * @return int | bool
     */
    private function findNextCase(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $end)
    {
        $tokens = $phpcsFile->getTokens();
        while (($stackPtr = $phpcsFile->findNext([T_CASE, T_DEFAULT, T_SWITCH], $stackPtr, $end)) !== false) {
            // Skip nested SWITCH statements; they are handled on their own.
            if ($tokens[$stackPtr]['code'] === T_SWITCH) {
                $stackPtr = $tokens[$stackPtr]['scope_closer'];
                continue;
            }

            break;
        }

        return $stackPtr;
    }
}
