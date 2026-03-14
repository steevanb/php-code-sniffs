<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Tests;

use PHPUnit\Framework\TestCase;

abstract class AbstractSniffTestCase extends TestCase
{
    abstract protected static function getSniffName(): string;

    public static function setUpBeforeClass(): void
    {
        static::$phpcs = realpath(__DIR__ . '/../vendor/bin/phpcs');
        static::$phpcbf = realpath(__DIR__ . '/../vendor/bin/phpcbf');
        static::$standard = realpath(__DIR__ . '/../src/Steevanb');
        static::$fixturesDir = dirname(new \ReflectionClass(static::class)->getFileName()) . '/Fixtures';
    }

    protected static function getRuleset(): ?string
    {
        return null;
    }

    /** @return list<array{line: int, column: int, message: string, source: string}> */
    protected static function getErrors(string $fixture): array
    {
        return static::getErrorsForFile(static::$fixturesDir . '/' . $fixture);
    }

    /** @return list<array{line: int, column: int, message: string, source: string}> */
    protected static function getErrorsForFile(string $filePath): array
    {
        $ruleset = static::getRuleset();
        $standardArg = $ruleset !== null
            ? '--standard=' . dirname(new \ReflectionClass(static::class)->getFileName()) . '/' . $ruleset
            : '--standard=' . static::$standard . ' --sniffs=' . static::getSniffName();

        $command = sprintf(
            '%s %s --report=csv --no-colors %s 2>&1',
            static::$phpcs,
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
                    'message' => $row[4],
                    'source' => $row[5],
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
        copy(static::$fixturesDir . '/' . $fixture, $tempFile);

        try {
            $ruleset = static::getRuleset();
            $standardArg = $ruleset !== null
                ? '--standard=' . dirname(new \ReflectionClass(static::class)->getFileName()) . '/' . $ruleset
                : '--standard=' . static::$standard . ' --sniffs=' . static::getSniffName();

            $command = sprintf(
                '%s %s --no-colors %s 2>&1',
                static::$phpcbf,
                $standardArg,
                $tempFile
            );

            exec($command);

            static::assertFileEquals(static::$fixturesDir . '/' . $expectedFixture, $tempFile);
            static::assertCount(0, static::getErrorsForFile($tempFile));
        } finally {
            unlink($tempFile);
        }
    }

    private static string $phpcs;

    private static string $phpcbf;

    private static string $standard;

    private static string $fixturesDir;
}
