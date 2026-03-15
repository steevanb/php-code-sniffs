<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests;

use PHPUnit\Framework\TestCase;

abstract class AbstractSniffTestCase extends TestCase
{
    abstract protected static function getSniffName(): string;

    private static string $phpcs;

    private static string $phpcbf;

    private static string $standard;

    private static string $fixturesDir;

    public static function setUpBeforeClass(): void
    {
        self::$phpcs = self::realpath(__DIR__ . '/../vendor/bin/phpcs');
        self::$phpcbf = self::realpath(__DIR__ . '/../vendor/bin/phpcbf');
        self::$standard = self::realpath(__DIR__ . '/../src/Steevanb');
        self::$fixturesDir = self::getClassDirectory() . '/Fixtures';
    }

    protected static function getRuleset(): ?string
    {
        return null;
    }

    /** @return list<array{line: int, column: int, message: string, source: string}> */
    protected static function getErrors(string $fixture): array
    {
        return static::getErrorsForFile(self::$fixturesDir . '/' . $fixture);
    }

    /** @return list<array{line: int, column: int, message: string, source: string}> */
    protected static function getErrorsForFile(string $filePath): array
    {
        $ruleset = static::getRuleset();
        $standardArg = $ruleset !== null
            ? '--standard=' . self::getClassDirectory() . '/' . $ruleset
            : '--standard=' . self::$standard . ' --sniffs=' . static::getSniffName();

        $command = sprintf(
            '%s %s --report=csv --no-colors %s 2>&1',
            self::$phpcs,
            $standardArg,
            $filePath
        );

        exec($command, $output);

        $errors = [];
        foreach ($output as $i => $line) {
            if ($i === 0 || str_starts_with($line, 'Time:')) {
                continue;
            }

            $row = str_getcsv(str_replace('\"', '""', $line), ',', '"', '');
            if (count($row) >= 6) {
                $errors[] = [
                    'line' => (int) $row[1],
                    'column' => (int) $row[2],
                    'message' => (string) $row[4],
                    'source' => (string) $row[5],
                ];
            }
        }

        return $errors;
    }

    protected static function assertNoErrors(string $fixture): void
    {
        static::assertCount(0, static::getErrors($fixture));
    }

    protected static function assertError(string $fixture, int $line, string $message, string $source): void
    {
        $errors = static::getErrors($fixture);

        static::assertCount(1, $errors);
        static::assertSame($line, $errors[0]['line']);
        static::assertSame($message, $errors[0]['message']);
        static::assertSame($source, $errors[0]['source']);
    }

    protected static function assertFixerResult(string $fixture, string $expectedFixture): void
    {
        $varDir = __DIR__ . '/../var/phpcbf';
        if (is_dir($varDir) === false) {
            mkdir($varDir, 0777, true);
        }

        $tempFile = $varDir . '/' . uniqid('phpcbf_test_') . '.php';
        copy(self::$fixturesDir . '/' . $fixture, $tempFile);

        try {
            $ruleset = static::getRuleset();
            $standardArg = $ruleset !== null
                ? '--standard=' . self::getClassDirectory() . '/' . $ruleset
                : '--standard=' . self::$standard . ' --sniffs=' . static::getSniffName();

            $command = sprintf(
                '%s %s --no-colors %s 2>&1',
                self::$phpcbf,
                $standardArg,
                $tempFile
            );

            exec($command);

            static::assertFileEquals(self::$fixturesDir . '/' . $expectedFixture, $tempFile);
            static::assertCount(0, static::getErrorsForFile($tempFile));
        } finally {
            unlink($tempFile);
        }
    }

    private static function realpath(string $path): string
    {
        $resolved = \realpath($path);
        if ($resolved === false) {
            throw new \RuntimeException('Path not found: ' . $path);
        }

        return $resolved;
    }

    private static function getClassDirectory(): string
    {
        $fileName = new \ReflectionClass(static::class)->getFileName();
        if ($fileName === false) {
            throw new \RuntimeException('Could not determine file name for class ' . static::class);
        }

        return dirname($fileName);
    }
}
