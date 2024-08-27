<?php

declare(strict_types=1);

namespace steevanb\PhpCodeSniffs\Reports;

use PHP_CodeSniffer\{
    Files\File,
    Reports\Report,
    Util\Timing
};

class Steevanb implements Report
{
    /** @var string[] */
    protected static $replacesInPath = [];

    public static function addReplaceInPath(string $search, string $replace): void
    {
        static::$replacesInPath[$search] = $replace;
    }

    protected static function replaceInPath(string $path): string
    {
        $return = $path;
        foreach (static::$replacesInPath as $search => $replace) {
            $return = str_replace($search, $replace, $return);
        }

        return $return;
    }

    /**
     * Generate a partial report for a single processed file.
     *
     * Function should return TRUE if it printed or stored data about the file
     * and FALSE if it ignored the file. Returning TRUE indicates that the file and
     * its data should be counted in the grand totals.
     *
     * @param array $report Prepared report data.
     * @param boolean $showSources Show sources?
     * @param int $width Maximum allowed line width.
     */
    public function generateFileReport($report, File $phpcsFile, $showSources = false, $width = 80): bool
    {
        if ($report['errors'] === 0 && $report['warnings'] === 0) {
            return false;
        }

        // The length of the word ERROR or WARNING; used for padding.
        if ($report['warnings'] > 0) {
            $typeLength = 7;
        } else {
            $typeLength = 5;
        }

        // Work out the max line number length for formatting.
        $maxLineNumLength = max(array_map('strlen', array_keys($report['messages'])));

        // The padding that all lines will require that are
        // printing an error message overflow.
        $paddingLine2 = str_repeat(' ', ($maxLineNumLength + 1));
        $paddingLine2 .= ' | ';
        $paddingLine2 .= str_repeat(' ', $typeLength);
        $paddingLine2 .= ' | ';
        if ($report['fixable'] > 0) {
            $paddingLine2 .= '    ';
        }

        $paddingLength = strlen($paddingLine2);

        // Make sure the report width isn't too big.
        $maxErrorLength = 0;
        foreach ($report['messages'] as $line => $lineErrors) {
            foreach ($lineErrors as $column => $colErrors) {
                foreach ($colErrors as $error) {
                    $length = strlen($error['message']);
                    if ($showSources === true) {
                        $length += (strlen($error['source']) + 3);
                    }

                    $maxErrorLength = max($maxErrorLength, ($length + 1));
                }
            }
        }

        $file = $report['filename'];
        $fileLength = strlen($file);
        $maxWidth = max(($fileLength + 6), ($maxErrorLength + $paddingLength));
        $width = min($width, $maxWidth);
        if ($width < 70) {
            $width = 70;
        }

        echo "\e[46;1m file://" . static::replaceInPath($file) . " \e[00m ";
        if ($report['errors'] > 0) {
            echo "\e[41;1m " . $report['errors'] . " \e[00m ";
        }
        if ($report['warnings'] > 0) {
            echo "\e[33;43;1m " . $report['warnings'] . " \e[00m ";
        }
        echo PHP_EOL;

        // The maximum amount of space an error message can use.
        $maxErrorSpace = ($width - $paddingLength - 1);
        if ($showSources === true) {
            // Account for the chars used to print colors.
            $maxErrorSpace += 8;
        }

        foreach ($report['messages'] as $line => $lineErrors) {
            foreach ($lineErrors as $column => $colErrors) {
                foreach ($colErrors as $error) {
                    $message = $error['message'];
                    $message = str_replace("\n", "\n" . $paddingLine2, $message);
                    if ($showSources === true) {
                        $message = "\e[1m" . $message . "\e[0m (\e[36m" . $error['source'] . "\e[0m)";
                    }

                    // The padding that goes on the front of the line.
                    $padding  = ($maxLineNumLength - strlen((string) $line));

                    if ($error['type'] === 'ERROR') {
                        echo "  \e[41;1m " . str_repeat(' ', $padding) . $line . " \e[0m ";
                    } else {
                        echo "  \e[33;43;1m " . str_repeat(' ', $padding) . $line . " \e[0m ";
                    }

                    if ($report['fixable'] > 0) {
                        echo '[';
                        if ($error['fixable'] === true) {
                            echo 'x';
                        } else {
                            echo ' ';
                        }

                        echo '] ';
                    }

                    echo
                        wordwrap(
                            $message,
                            $maxErrorSpace,
                            PHP_EOL . $paddingLine2
                        )
                        . PHP_EOL;
                }
            }
        }

        echo PHP_EOL;

        return true;
    }

    /**
     * Prints all errors and warnings for each file processed.
     *
     * @param string $cachedData Any partial report data that was returned from
     * @param int $totalFiles Total number of files processed during the run.
     * @param int $totalErrors Total number of errors found during the run.
     * @param int $totalWarnings Total number of warnings found during the run.
     * @param int $totalFixable Total number of problems that can be fixed.
     * @param boolean $showSources Show sources?
     * @param int $width Maximum allowed line width.
     * @param boolean $toScreen Is the report being printed to screen?
     *
     * @return void
     */
    public function generate(
        $cachedData,
        $totalFiles,
        $totalErrors,
        $totalWarnings,
        $totalFixable,
        $showSources = false,
        $width = 80,
        $interactive = false,
        $toScreen = true
    ) {
        echo $cachedData;

        if ($toScreen === true && $interactive === false) {
            $time = $this->calculateTime();

            $parts = [];
            $filesLabel = $this->getFilesLabel($totalFiles, $totalErrors, $totalWarnings);
            if (is_string($filesLabel)) {
                $parts[] = $filesLabel;
            }

            $parts[] = $this->getErrorsLabel($totalErrors);
            $parts[] = $this->getWarningsLabel($totalWarnings);
            $parts[] = $time;

            echo implode(' - ', $parts) . PHP_EOL;
        }
    }

    protected function getFilesLabel(int $totalFiles, int $totalErrors, int $totalWarnings): ?string
    {
        return ($totalErrors > 0 || $totalWarnings > 0)
            ? $totalFiles . ' file' . ($totalFiles > 1 ? 's' : null)
            : null;
    }

    protected function getErrorsLabel(int $totalErrors): string
    {
        $color = $totalErrors === 0 ? "\e[42m\e[30m" : "\e[41m";

        return $color . ' ' . $totalErrors . ' error' . ($totalErrors !== 1 ? 's' : null) . " \e[0m";
    }

    protected function getWarningsLabel(int $totalWarnings): string
    {
        $return = $totalWarnings . ' warning' . ($totalWarnings !== 1 ? 's' : null);

        if ($totalWarnings > 0) {
            $return = "\e[43m\e[30m " . $return . " \e[0m";
        }

        return $return;
    }

    protected function calculateTime(): string
    {
        $time = ((microtime(true) - $this->getStartTime()) * 1000);

        if ($time > 60000) {
            $mins = floor($time / 60000);
            $secs = round((($time % 60000) / 1000), 2);
            $return = $mins . ' mins';
            if ($secs !== 0) {
                $return .= ', ' . $secs . ' secs';
            }
        } elseif ($time > 1000) {
            $return = round(($time / 1000), 2) . ' secs';
        } else {
            $return = round($time) . 'ms';
        }

        return $return;
    }

    protected function getStartTime(): float
    {
        // thanks to not add getter ...
        $reflection = new \ReflectionProperty(Timing::class, 'startTime');
        $reflection->setAccessible(true);
        $return = $reflection->getValue();
        $reflection->setAccessible(false);

        return $return;
    }
}
