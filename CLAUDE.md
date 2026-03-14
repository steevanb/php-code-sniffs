# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

PHP CodeSniffer custom standard ("Steevanb") that extends PSR12/Squiz with additional sniffs and a custom colored terminal report. Depends on `squizlabs/php_codesniffer` (now maintained by PHPCSStandards).

## Common Commands

```bash
# Initial setup (pull Docker image + composer update)
bin/dev/start

# Run phpcs (auto-detects Docker; falls back to local execution)
bin/ci/phpcs

# Run unit tests
bin/ci/phpunit

# Build the Docker image
bin/buildDockerImage
```

## Architecture

### Ruleset chain

`ruleset.xml` (root) -> `src/Steevanb/ruleset.xml` (main standard definition)

- `src/Steevanb/ruleset.xml` extends PSR12 + Squiz standards, excludes ~55 specific rules, and configures properties (line length 120, forbidden functions, spacing, etc.)
- `config/ci/phpcs.xml` is used to lint *this project itself* (references the Steevanb standard, excludes vendor/, uses the custom report)

### Custom sniffs

All in `src/Steevanb/Sniffs/`, organized by category (Arrays, CodeAnalysis, ControlStructures, Metrics, Namespaces, NamingConventions, PHP, PhpDoc, Properties, ReturnType, Syntax, Uses). Each sniff implements `PHP_CodeSniffer\Sniffs\Sniff`.

Key sniff: `GroupUsesSniff` enforces grouped `use` statements with configurable namespace level prefixes via XML properties (`firstLevelPrefixes`, `thirdLevelPrefixes`, `fourthLevelPrefixes`).

### Custom report

`src/Reports/Steevanb.php` - Colored terminal output with error/warning counts, fixable indicators, and execution timing. Supports path remapping via `addReplaceInPath()` for Docker environments.

### Docker execution model

`bin/ci/phpcs` detects Docker availability. With Docker: runs `php:8.4.19-cli-alpine3.23` mounting the project as `/app:ro`. Without Docker: executes `docker/entrypoint.sh` directly. The entrypoint invokes `vendor/bin/phpcs` with the Steevanb standard and custom report.

Environment variables: `PHPCS_PARAMETERS` (extra CLI args), `PHPCS_BOOTSTRAP` (bootstrap file path), `PHPCS_PHP_VERSION_ID` (override PHP version detection, e.g., `80102`).

### Tests

`tests/` mirrors the directory structure of `src/` for the class under test. Each tested class gets its own directory named after the class, containing:
- `<ClassName>Test.php` — the test class
- `Fixtures/` — test fixture files if needed

Example for `src/Steevanb/Sniffs/Formatting/DisallowMultipleStatementsSniff.php`:
```
tests/Steevanb/Sniffs/Formatting/DisallowMultipleStatements/
    DisallowMultipleStatementsSniffTest.php
    Fixtures/
        EmptyHooksOneLine.php
```

### Composer behavior

`composer.lock` is intentionally deleted after install/update via composer scripts. It is not tracked in git.

`bin/composer` must not be modified. It uses the `composer:2.2.4` Docker image.

## Code style

### Class member ordering (`Steevanb.Classes.ClassMemberOrder`)

Strict ordering by weight (lower weight must come first):

1. Trait `use` statements (weight 10)
2. Abstract public properties (weight 20)
3. Abstract protected properties (weight 21)
4. Abstract public methods (weight 30)
5. Abstract protected methods (weight 31)
6. Public constants (weight 40)
7. Protected constants (weight 41)
8. Private constants (weight 42)
9. Public static methods (weight 50)
10. Protected static methods (weight 51)
11. Private static methods (weight 52)
12. Public properties (weight 60)
13. Protected properties (weight 61)
14. Private properties (weight 62)
15. `__construct` (weight 70)
16. Magic methods `__*` (weight 80)
17. Public methods (weight 90)
18. Protected methods (weight 91)
19. Private methods (weight 92)

Maximum line length is 120 characters.

Example:
```php
abstract class Foo
{
    use FooTrait;

    abstract public string $abstractPublicProp { get; }
    abstract protected string $abstractProtectedProp { get; }

    abstract public function abstractPublicMethod(): void;
    abstract protected function abstractProtectedMethod(): void;

    public const string PUBLIC_CONST = 'a';
    protected const string PROTECTED_CONST = 'b';
    private const string PRIVATE_CONST = 'c';

    public static function staticPublicMethod(): void { }
    protected static function staticProtectedMethod(): void { }
    private static function staticPrivateMethod(): void { }

    public string $publicProp = 'a';
    protected string $protectedProp = 'b';
    private string $privateProp = 'c';

    public function __construct() { }

    public function __toString(): string { }

    public function publicMethod(): void { }
    protected function protectedMethod(): void { }
    private function privateMethod(): void { }
}
```

## Working guidelines

Parallelize all independent operations: file edits, searches, reads, tool calls. When multiple actions don't depend on each other, execute them simultaneously.
