# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

PHP CodeSniffer custom standard ("Steevanb") that extends PSR12/Squiz with additional sniffs and a custom colored terminal report. Depends on `squizlabs/php_codesniffer` (now maintained by PHPCSStandards).

## Common Commands

```bash
# Install/update dependencies (runs Composer 2.2.4 via Docker)
bin/composer install
bin/composer update

# Run phpcs (auto-detects Docker; falls back to local execution)
bin/phpcs

# Build the Docker image
bin/buildDockerImage
```

There are no unit tests in this project. CI only runs `bin/phpcs` against the project's own code.

## Architecture

### Ruleset chain

`ruleset.xml` (root) -> `src/Steevanb/ruleset.xml` (main standard definition)

- `src/Steevanb/ruleset.xml` extends PSR12 + Squiz standards, excludes ~55 specific rules, and configures properties (line length 120, forbidden functions, spacing, etc.)
- `standard.xml` is used to lint *this project itself* (references the Steevanb standard, excludes vendor/, uses the custom report)

### Custom sniffs

All in `src/Steevanb/Sniffs/`, organized by category (Arrays, CodeAnalysis, ControlStructures, Metrics, Namespaces, NamingConventions, PHP, PhpDoc, Properties, ReturnType, Syntax, Uses). Each sniff implements `PHP_CodeSniffer\Sniffs\Sniff`.

Key sniff: `GroupUsesSniff` enforces grouped `use` statements with configurable namespace level prefixes via XML properties (`firstLevelPrefixes`, `thirdLevelPrefixes`, `fourthLevelPrefixes`).

### Custom report

`src/Reports/Steevanb.php` - Colored terminal output with error/warning counts, fixable indicators, and execution timing. Supports path remapping via `addReplaceInPath()` for Docker environments.

### Docker execution model

`bin/phpcs` detects Docker availability. With Docker: runs `php:8.3.4-cli-alpine3.19` mounting the project as `/app:ro`. Without Docker: executes `docker/entrypoint.sh` directly. The entrypoint invokes `vendor/bin/phpcs` with the Steevanb standard and custom report.

Environment variables: `PHPCS_PARAMETERS` (extra CLI args), `PHPCS_BOOTSTRAP` (bootstrap file path), `PHPCS_PHP_VERSION_ID` (override PHP version detection, e.g., `80102`).

### Composer behavior

`composer.lock` is intentionally deleted after install/update via composer scripts. It is not tracked in git.
