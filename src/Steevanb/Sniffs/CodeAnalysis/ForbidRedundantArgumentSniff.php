<?php

declare(strict_types=1);

namespace Steevanb\PhpCodeSniffs\Steevanb\Sniffs\CodeAnalysis;

use PHP_CodeSniffer\{
    Files\File,
    Sniffs\Sniff
};

/**
 * Detects function/method/constructor arguments that are identical to the parameter default value.
 * Redundant arguments should be removed.
 * Uses Reflection to resolve parameter defaults from any autoloaded class or built-in PHP function.
 * Falls back to in-file definition scanning when Reflection is not available.
 */
class ForbidRedundantArgumentSniff implements Sniff
{
    private const string ERROR_CODE = 'RedundantArgument';

    /** @var array<string, string> */
    private array $fileNamespaces = [];

    /** @var array<string, array<string, string>> */
    private array $fileUseMaps = [];

    /** @var array<string, array<string, list<array{name: string, hasDefault: bool, default: ?string}>>> */
    private array $fileDefinitions = [];

    /** @var array<string, bool> */
    private array $parsedFiles = [];

    /** @var array<string, list<array{name: string, hasDefault: bool, defaultValue: mixed, useReflection: bool}>|false> */
    private array $reflectionCache = [];

    public function register(): array
    {
        return [T_STRING, T_ATTRIBUTE];
    }

    public function process(File $phpcsFile, int $stackPtr): void
    {
        $this->ensureFileParsed($phpcsFile);

        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_ATTRIBUTE) {
            $this->processAttribute($phpcsFile, $stackPtr);

            return;
        }

        $nextPtr = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);
        if ($nextPtr === false || $tokens[$nextPtr]['code'] !== T_OPEN_PARENTHESIS) {
            return;
        }

        $prevPtr = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
        if ($prevPtr !== false && $tokens[$prevPtr]['code'] === T_FUNCTION) {
            return;
        }

        $callInfo = $this->resolveCall($phpcsFile, $stackPtr, $prevPtr);
        if ($callInfo === null) {
            return;
        }

        $params = $this->getParameterDefaults($phpcsFile, $callInfo);
        if ($params === null) {
            return;
        }

        $openParen = $nextPtr;
        if (array_key_exists('parenthesis_closer', $tokens[$openParen]) === false) {
            return;
        }

        $closeParen = $tokens[$openParen]['parenthesis_closer'];

        $arguments = $this->parseCallArguments($phpcsFile, $openParen, $closeParen);
        if ($arguments === []) {
            return;
        }

        $this->checkArguments($phpcsFile, $params, $arguments, $openParen, $closeParen);
    }

    private function processAttribute(File $phpcsFile, int $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();
        $filename = $phpcsFile->getFilename();

        $classNameParts = [];
        $i = $stackPtr + 1;

        while ($i < $phpcsFile->numTokens) {
            if ($tokens[$i]['code'] === T_STRING || $tokens[$i]['code'] === T_NS_SEPARATOR) {
                $classNameParts[] = $tokens[$i]['content'];
            } elseif ($tokens[$i]['code'] !== T_WHITESPACE) {
                break;
            }

            $i++;
        }

        if ($classNameParts === []) {
            return;
        }

        $className = implode('', $classNameParts);

        $openParen = $phpcsFile->findNext(T_WHITESPACE, $i, null, true);
        if ($openParen === false || $tokens[$openParen]['code'] !== T_OPEN_PARENTHESIS) {
            return;
        }

        if (array_key_exists('parenthesis_closer', $tokens[$openParen]) === false) {
            return;
        }

        $closeParen = $tokens[$openParen]['parenthesis_closer'];

        $fqcn = str_starts_with($className, '\\') === true
            ? ltrim($className, '\\')
            : $this->resolveClassFqcn($filename, $this->extractShortName($className));

        $callInfo = [
            'fqcn' => $fqcn,
            'method' => '__construct',
            'shortKey' => $this->extractShortName($className) . '::__construct',
        ];

        $params = $this->getParameterDefaults($phpcsFile, $callInfo);
        if ($params === null) {
            return;
        }

        $arguments = $this->parseCallArguments($phpcsFile, $openParen, $closeParen);
        if ($arguments === []) {
            return;
        }

        $this->checkArguments($phpcsFile, $params, $arguments, $openParen, $closeParen);
    }

    private function ensureFileParsed(File $phpcsFile): void
    {
        $filename = $phpcsFile->getFilename();
        if (array_key_exists($filename, $this->parsedFiles) === true) {
            return;
        }

        $this->parsedFiles[$filename] = true;
        $this->fileNamespaces[$filename] = '';
        $this->fileUseMaps[$filename] = [];
        $this->fileDefinitions[$filename] = [];

        $tokens = $phpcsFile->getTokens();

        for ($i = 0; $i < $phpcsFile->numTokens; $i++) {
            $code = $tokens[$i]['code'];

            if ($code === T_NAMESPACE) {
                $this->fileNamespaces[$filename] = $this->parseNamespaceDeclaration($phpcsFile, $i);

                continue;
            }

            if ($code === T_USE && ($tokens[$i]['conditions'] ?? []) === []) {
                $this->parseUseStatement($phpcsFile, $i, $filename);

                continue;
            }

            if ($code === T_FUNCTION) {
                $this->parseInFileDefinition($phpcsFile, $i, $filename);
            }
        }
    }

    private function parseNamespaceDeclaration(File $phpcsFile, int $stackPtr): string
    {
        $tokens = $phpcsFile->getTokens();
        $namespace = '';

        for ($i = $stackPtr + 1; $i < $phpcsFile->numTokens; $i++) {
            if (in_array($tokens[$i]['code'], [T_SEMICOLON, T_OPEN_CURLY_BRACKET], true) === true) {
                break;
            }

            if ($tokens[$i]['code'] !== T_WHITESPACE) {
                $namespace .= $tokens[$i]['content'];
            }
        }

        return $namespace;
    }

    private function parseUseStatement(File $phpcsFile, int $stackPtr, string $filename): void
    {
        $tokens = $phpcsFile->getTokens();
        $parts = [];
        $prefix = '';
        $inGroup = false;
        $alias = null;
        $expectingAlias = false;

        for ($i = $stackPtr + 1; $i < $phpcsFile->numTokens; $i++) {
            $code = $tokens[$i]['code'];
            $content = $tokens[$i]['content'];

            if ($code === T_SEMICOLON) {
                if ($parts !== []) {
                    $fqcn = $inGroup === true ? $prefix . '\\' . implode('', $parts) : implode('', $parts);
                    $shortName = $alias ?? $this->extractShortName($fqcn);
                    $this->fileUseMaps[$filename][$shortName] = $fqcn;
                }

                break;
            }

            if ($code === T_OPEN_USE_GROUP) {
                $prefix = rtrim(implode('', $parts), '\\');
                $parts = [];
                $inGroup = true;

                continue;
            }

            if ($code === T_CLOSE_USE_GROUP) {
                if ($parts !== []) {
                    $fqcn = $prefix . '\\' . implode('', $parts);
                    $shortName = $alias ?? $this->extractShortName($fqcn);
                    $this->fileUseMaps[$filename][$shortName] = $fqcn;
                }

                continue;
            }

            if ($code === T_COMMA) {
                if ($parts !== []) {
                    $fqcn = $inGroup === true ? $prefix . '\\' . implode('', $parts) : implode('', $parts);
                    $shortName = $alias ?? $this->extractShortName($fqcn);
                    $this->fileUseMaps[$filename][$shortName] = $fqcn;
                }

                $parts = [];
                $alias = null;
                $expectingAlias = false;

                continue;
            }

            if ($code === T_AS) {
                $expectingAlias = true;

                continue;
            }

            if ($code === T_WHITESPACE || $code === T_FUNCTION || $code === T_CONST) {
                continue;
            }

            if ($expectingAlias === true && $code === T_STRING) {
                $alias = $content;
                $expectingAlias = false;

                continue;
            }

            $parts[] = $content;
        }
    }

    private function extractShortName(string $fqcn): string
    {
        $parts = explode('\\', $fqcn);

        return end($parts);
    }

    private function parseInFileDefinition(File $phpcsFile, int $stackPtr, string $filename): void
    {
        $tokens = $phpcsFile->getTokens();

        $namePtr = $phpcsFile->findNext([T_STRING, T_OPEN_PARENTHESIS], $stackPtr + 1, $stackPtr + 6);
        if ($namePtr === false || $tokens[$namePtr]['code'] !== T_STRING) {
            return;
        }

        $functionName = $tokens[$namePtr]['content'];
        $className = $this->findEnclosingClassName($phpcsFile, $stackPtr);
        $key = $className !== null ? $className . '::' . $functionName : $functionName;

        $parenPtr = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $namePtr + 1, $namePtr + 3);
        if ($parenPtr === false || array_key_exists('parenthesis_closer', $tokens[$parenPtr]) === false) {
            return;
        }

        $params = $this->parseDefinitionParameters($phpcsFile, $parenPtr, $tokens[$parenPtr]['parenthesis_closer']);
        $this->fileDefinitions[$filename][$key] = $params;
    }

    private function findEnclosingClassName(File $phpcsFile, int $stackPtr): ?string
    {
        $tokens = $phpcsFile->getTokens();

        foreach ([T_CLASS, T_INTERFACE, T_TRAIT, T_ENUM] as $type) {
            $condition = $phpcsFile->getCondition($stackPtr, $type);
            if ($condition !== false) {
                $classNamePtr = $phpcsFile->findNext(T_STRING, $condition + 1, $condition + 5);
                if ($classNamePtr !== false) {
                    return $tokens[$classNamePtr]['content'];
                }
            }
        }

        return null;
    }

    /** @return array{fqcn: string, method: ?string, shortKey: string}|null */
    private function resolveCall(File $phpcsFile, int $stackPtr, int|false $prevPtr): ?array
    {
        $tokens = $phpcsFile->getTokens();
        $filename = $phpcsFile->getFilename();
        $functionName = $tokens[$stackPtr]['content'];

        if ($prevPtr === false) {
            return [
                'fqcn' => $this->resolveFunctionFqcn($filename, $functionName),
                'method' => null,
                'shortKey' => $functionName,
            ];
        }

        $prevCode = $tokens[$prevPtr]['code'];

        if ($prevCode === T_NEW) {
            return [
                'fqcn' => $this->resolveClassFqcn($filename, $functionName),
                'method' => '__construct',
                'shortKey' => $functionName . '::__construct',
            ];
        }

        if ($prevCode === T_OBJECT_OPERATOR || $prevCode === T_NULLSAFE_OBJECT_OPERATOR) {
            $objectPtr = $phpcsFile->findPrevious(T_WHITESPACE, $prevPtr - 1, null, true);
            if (
                $objectPtr !== false
                && $tokens[$objectPtr]['code'] === T_VARIABLE
                && $tokens[$objectPtr]['content'] === '$this'
            ) {
                $className = $this->findEnclosingClassName($phpcsFile, $stackPtr);
                if ($className !== null) {
                    return [
                        'fqcn' => $this->resolveClassFqcn($filename, $className),
                        'method' => $functionName,
                        'shortKey' => $className . '::' . $functionName,
                    ];
                }
            }

            return null;
        }

        if ($prevCode === T_DOUBLE_COLON) {
            $classPtr = $phpcsFile->findPrevious(T_WHITESPACE, $prevPtr - 1, null, true);
            if ($classPtr === false) {
                return null;
            }

            $classCode = $tokens[$classPtr]['code'];

            if ($classCode === T_SELF || $classCode === T_STATIC) {
                $className = $this->findEnclosingClassName($phpcsFile, $stackPtr);
                if ($className !== null) {
                    return [
                        'fqcn' => $this->resolveClassFqcn($filename, $className),
                        'method' => $functionName,
                        'shortKey' => $className . '::' . $functionName,
                    ];
                }

                return null;
            }

            if ($classCode === T_STRING) {
                $className = $tokens[$classPtr]['content'];

                return [
                    'fqcn' => $this->resolveClassFqcn($filename, $className),
                    'method' => $functionName,
                    'shortKey' => $className . '::' . $functionName,
                ];
            }

            return null;
        }

        return [
            'fqcn' => $this->resolveFunctionFqcn($filename, $functionName),
            'method' => null,
            'shortKey' => $functionName,
        ];
    }

    private function resolveClassFqcn(string $filename, string $className): string
    {
        if (array_key_exists($className, $this->fileUseMaps[$filename]) === true) {
            return $this->fileUseMaps[$filename][$className];
        }

        $namespace = $this->fileNamespaces[$filename];

        return $namespace !== '' ? $namespace . '\\' . $className : $className;
    }

    private function resolveFunctionFqcn(string $filename, string $functionName): string
    {
        if (array_key_exists($functionName, $this->fileUseMaps[$filename]) === true) {
            return $this->fileUseMaps[$filename][$functionName];
        }

        $namespace = $this->fileNamespaces[$filename];

        return $namespace !== '' ? $namespace . '\\' . $functionName : $functionName;
    }

    /**
     * @param array{fqcn: string, method: ?string, shortKey: string} $callInfo
     * @return list<array{name: string, hasDefault: bool, defaultValue: mixed, useReflection: bool}>|null
     */
    private function getParameterDefaults(File $phpcsFile, array $callInfo): ?array
    {
        $reflectionParams = $this->tryReflection($callInfo['fqcn'], $callInfo['method']);
        if ($reflectionParams !== null) {
            return $reflectionParams;
        }

        $filename = $phpcsFile->getFilename();
        if (array_key_exists($callInfo['shortKey'], $this->fileDefinitions[$filename]) === true) {
            return array_map(
                static fn(array $param): array => [
                    'name' => $param['name'],
                    'hasDefault' => $param['hasDefault'],
                    'defaultValue' => $param['default'],
                    'useReflection' => false,
                ],
                $this->fileDefinitions[$filename][$callInfo['shortKey']]
            );
        }

        return null;
    }

    /** @return list<array{name: string, hasDefault: bool, defaultValue: mixed, useReflection: bool}>|null */
    private function tryReflection(string $fqcn, ?string $method): ?array
    {
        $cacheKey = $method !== null ? $fqcn . '::' . $method : 'function::' . $fqcn;

        if (array_key_exists($cacheKey, $this->reflectionCache) === true) {
            $cached = $this->reflectionCache[$cacheKey];

            return $cached === false ? null : $cached;
        }

        try {
            if ($method !== null) {
                $reflector = new \ReflectionMethod($fqcn, $method);
            } else {
                try {
                    $reflector = new \ReflectionFunction($fqcn);
                } catch (\ReflectionException) {
                    $shortName = strrchr($fqcn, '\\');
                    if ($shortName === false) {
                        $this->reflectionCache[$cacheKey] = false;

                        return null;
                    }

                    $reflector = new \ReflectionFunction(substr($shortName, 1));
                }
            }
        } catch (\ReflectionException) {
            $this->reflectionCache[$cacheKey] = false;

            return null;
        }

        $params = [];
        foreach ($reflector->getParameters() as $param) {
            $hasDefault = $param->isDefaultValueAvailable();
            $params[] = [
                'name' => '$' . $param->getName(),
                'hasDefault' => $hasDefault,
                'defaultValue' => $hasDefault === true ? $param->getDefaultValue() : null,
                'useReflection' => true,
            ];
        }

        $this->reflectionCache[$cacheKey] = $params;

        return $params;
    }

    /** @return list<array{name: string, hasDefault: bool, default: ?string}> */
    private function parseDefinitionParameters(File $phpcsFile, int $openParen, int $closeParen): array
    {
        $tokens = $phpcsFile->getTokens();
        $params = [];
        $depth = 0;
        $currentParam = null;
        $collectingDefault = false;
        $defaultTokens = [];

        for ($i = $openParen + 1; $i < $closeParen; $i++) {
            $code = $tokens[$i]['code'];

            if (
                in_array(
                    $code,
                    [T_OPEN_PARENTHESIS, T_OPEN_SHORT_ARRAY, T_OPEN_SQUARE_BRACKET, T_OPEN_CURLY_BRACKET],
                    true
                ) === true
            ) {
                $depth++;
                if ($collectingDefault === true) {
                    $defaultTokens[] = $tokens[$i]['content'];
                }

                continue;
            }

            if (
                in_array(
                    $code,
                    [T_CLOSE_PARENTHESIS, T_CLOSE_SHORT_ARRAY, T_CLOSE_SQUARE_BRACKET, T_CLOSE_CURLY_BRACKET],
                    true
                ) === true
            ) {
                $depth--;
                if ($collectingDefault === true) {
                    $defaultTokens[] = $tokens[$i]['content'];
                }

                continue;
            }

            if ($code === T_COMMA && $depth === 0) {
                if ($currentParam !== null) {
                    $params[] = [
                        'name' => $currentParam,
                        'hasDefault' => $collectingDefault,
                        'default' => $collectingDefault === true
                            ? $this->normalizeValue(implode('', $defaultTokens))
                            : null,
                    ];
                }

                $currentParam = null;
                $collectingDefault = false;
                $defaultTokens = [];

                continue;
            }

            if ($code === T_VARIABLE && $currentParam === null) {
                $currentParam = $tokens[$i]['content'];

                continue;
            }

            if ($code === T_EQUAL && $currentParam !== null && $collectingDefault === false && $depth === 0) {
                $collectingDefault = true;

                continue;
            }

            if ($collectingDefault === true && ($code !== T_WHITESPACE || $defaultTokens !== [])) {
                $defaultTokens[] = $tokens[$i]['content'];
            }
        }

        if ($currentParam !== null) {
            $params[] = [
                'name' => $currentParam,
                'hasDefault' => $collectingDefault,
                'default' => $collectingDefault === true
                    ? $this->normalizeValue(implode('', $defaultTokens))
                    : null,
            ];
        }

        return $params;
    }

    /**
     * @return list<array{
     *     name: ?string,
     *     value: string,
     *     isNamed: bool,
     *     firstTokenPtr: int,
     *     lastTokenPtr: int
     * }>
     */
    private function parseCallArguments(File $phpcsFile, int $openParen, int $closeParen): array
    {
        $tokens = $phpcsFile->getTokens();
        $arguments = [];
        $depth = 0;
        $argName = null;
        $firstTokenPtr = null;
        $lastTokenPtr = null;
        $valueTokens = [];

        $firstNonWs = $phpcsFile->findNext(T_WHITESPACE, $openParen + 1, $closeParen, true);
        if ($firstNonWs === false) {
            return [];
        }

        for ($i = $openParen + 1; $i <= $closeParen; $i++) {
            $code = $tokens[$i]['code'];

            if (
                in_array(
                    $code,
                    [T_OPEN_PARENTHESIS, T_OPEN_SHORT_ARRAY, T_OPEN_SQUARE_BRACKET, T_OPEN_CURLY_BRACKET],
                    true
                ) === true
            ) {
                $depth++;
                if ($firstTokenPtr === null) {
                    $firstTokenPtr = $i;
                }

                $lastTokenPtr = $i;
                $valueTokens[] = $tokens[$i]['content'];

                continue;
            }

            if (
                in_array(
                    $code,
                    [T_CLOSE_PARENTHESIS, T_CLOSE_SHORT_ARRAY, T_CLOSE_SQUARE_BRACKET, T_CLOSE_CURLY_BRACKET],
                    true
                ) === true
            ) {
                if ($depth === 0) {
                    if ($firstTokenPtr !== null) {
                        $arguments[] = [
                            'name' => $argName,
                            'value' => $this->normalizeValue(implode('', $valueTokens)),
                            'isNamed' => $argName !== null,
                            'firstTokenPtr' => $firstTokenPtr,
                            'lastTokenPtr' => (int) $lastTokenPtr,
                        ];
                    }

                    break;
                }

                $depth--;
                $lastTokenPtr = $i;
                $valueTokens[] = $tokens[$i]['content'];

                continue;
            }

            if ($code === T_COMMA && $depth === 0) {
                if ($firstTokenPtr !== null) {
                    $arguments[] = [
                        'name' => $argName,
                        'value' => $this->normalizeValue(implode('', $valueTokens)),
                        'isNamed' => $argName !== null,
                        'firstTokenPtr' => $firstTokenPtr,
                        'lastTokenPtr' => (int) $lastTokenPtr,
                    ];
                }

                $argName = null;
                $valueTokens = [];
                $firstTokenPtr = null;
                $lastTokenPtr = null;

                continue;
            }

            if ($code === T_WHITESPACE) {
                if ($valueTokens !== []) {
                    $valueTokens[] = $tokens[$i]['content'];
                    $lastTokenPtr = $i;
                }

                continue;
            }

            if ($code === T_PARAM_NAME && $depth === 0 && $valueTokens === []) {
                $argName = '$' . $tokens[$i]['content'];
                if ($firstTokenPtr === null) {
                    $firstTokenPtr = $i;
                }

                $lastTokenPtr = $i;

                continue;
            }

            if ($code === T_COLON && $argName !== null && $valueTokens === []) {
                $lastTokenPtr = $i;

                continue;
            }

            if ($firstTokenPtr === null) {
                $firstTokenPtr = $i;
            }

            $lastTokenPtr = $i;
            $valueTokens[] = $tokens[$i]['content'];
        }

        return $arguments;
    }

    /**
     * @param list<array{name: string, hasDefault: bool, defaultValue: mixed, useReflection: bool}> $params
     * @param list<array{name: ?string, value: string, isNamed: bool, firstTokenPtr: int, lastTokenPtr: int}> $arguments
     */
    private function checkArguments(
        File $phpcsFile,
        array $params,
        array $arguments,
        int $openParen,
        int $closeParen
    ): void {
        $paramsByName = [];
        foreach ($params as $index => $param) {
            $paramsByName[$param['name']] = $index;
        }

        $hasNamedArgs = false;
        foreach ($arguments as $arg) {
            if ($arg['isNamed'] === true) {
                $hasNamedArgs = true;

                break;
            }
        }

        if ($hasNamedArgs === true) {
            $this->checkNamedArguments($phpcsFile, $params, $paramsByName, $arguments, $openParen, $closeParen);

            return;
        }

        $this->checkPositionalArguments($phpcsFile, $params, $arguments, $openParen, $closeParen);
    }

    /**
     * @param list<array{name: string, hasDefault: bool, defaultValue: mixed, useReflection: bool}> $params
     * @param list<array{name: ?string, value: string, isNamed: bool, firstTokenPtr: int, lastTokenPtr: int}> $arguments
     */
    private function checkPositionalArguments(
        File $phpcsFile,
        array $params,
        array $arguments,
        int $openParen,
        int $closeParen
    ): void {
        $lastNonRedundant = -1;

        for ($i = count($arguments) - 1; $i >= 0; $i--) {
            if (
                array_key_exists($i, $params) === false
                || $params[$i]['hasDefault'] === false
                || $this->argumentMatchesDefault($arguments[$i]['value'], $params[$i]) === false
            ) {
                $lastNonRedundant = $i;

                break;
            }
        }

        $firstRedundant = $lastNonRedundant + 1;
        if ($firstRedundant >= count($arguments)) {
            return;
        }

        for ($i = $firstRedundant; $i < count($arguments); $i++) {
            $fix = $phpcsFile->addFixableError(
                sprintf(
                    'Argument %d passes the default value for parameter %s and should be removed',
                    $i + 1,
                    $params[$i]['name']
                ),
                $arguments[$i]['firstTokenPtr'],
                self::ERROR_CODE
            );

            if ($fix === true && $i === $firstRedundant) {
                $this->fixTrailingArguments($phpcsFile, $arguments, $firstRedundant, $openParen, $closeParen);
            }
        }
    }

    /**
     * @param list<array{name: string, hasDefault: bool, defaultValue: mixed, useReflection: bool}> $params
     * @param array<string, int> $paramsByName
     * @param list<array{name: ?string, value: string, isNamed: bool, firstTokenPtr: int, lastTokenPtr: int}> $arguments
     */
    private function checkNamedArguments(
        File $phpcsFile,
        array $params,
        array $paramsByName,
        array $arguments,
        int $openParen,
        int $closeParen
    ): void {
        $redundantIndexes = [];

        foreach ($arguments as $argIndex => $arg) {
            if ($arg['isNamed'] === false || $arg['name'] === null) {
                continue;
            }

            if (array_key_exists($arg['name'], $paramsByName) === false) {
                continue;
            }

            $paramIndex = $paramsByName[$arg['name']];
            if (
                $params[$paramIndex]['hasDefault'] === true
                && $this->argumentMatchesDefault($arg['value'], $params[$paramIndex]) === true
            ) {
                $redundantIndexes[] = $argIndex;
            }
        }

        if ($redundantIndexes === []) {
            return;
        }

        foreach ($redundantIndexes as $argIndex) {
            $arg = $arguments[$argIndex];
            $paramName = ltrim((string) $arg['name'], '$');

            $fix = $phpcsFile->addFixableError(
                sprintf(
                    'Named argument "%s" passes the default value and should be removed',
                    $paramName
                ),
                $arg['firstTokenPtr'],
                self::ERROR_CODE
            );

            if ($fix === true) {
                $this->fixNamedArgument(
                    $phpcsFile,
                    $arguments,
                    $argIndex,
                    $redundantIndexes,
                    $openParen,
                    $closeParen
                );
            }
        }
    }

    /** @param array{name: string, hasDefault: bool, defaultValue: mixed, useReflection: bool} $param */
    private function argumentMatchesDefault(string $argumentValue, array $param): bool
    {
        if ($param['hasDefault'] === false) {
            return false;
        }

        if ($param['useReflection'] === true) {
            $evaluated = $this->evaluateLiteral($argumentValue);
            if ($evaluated instanceof \stdClass) {
                return false;
            }

            return $evaluated === $param['defaultValue'];
        }

        return $argumentValue === $param['defaultValue'];
    }

    /**
     * Try to evaluate a normalized literal string into a PHP value.
     * Returns null when the expression cannot be safely evaluated.
     */
    private function evaluateLiteral(string $value): mixed
    {
        $lower = strtolower($value);

        if ($lower === 'null') {
            return null;
        }

        if ($lower === 'true') {
            return true;
        }

        if ($lower === 'false') {
            return false;
        }

        if ($value === '[]') {
            return [];
        }

        if (preg_match('/^-?\d+$/', $value) === 1) {
            return (int) $value;
        }

        if (preg_match('/^-?\d+\.\d+$/', $value) === 1) {
            return (float) $value;
        }

        if (preg_match("/^'(.*)'$/s", $value, $matches) === 1) {
            return str_replace(['\\\\', "\\'"], ['\\', "'"], $matches[1]);
        }

        if (preg_match('/^"(.*)"$/s', $value, $matches) === 1) {
            return stripcslashes($matches[1]);
        }

        return new \stdClass();
    }

    /** @param list<array{name: ?string, value: string, isNamed: bool, firstTokenPtr: int, lastTokenPtr: int}> $arguments */
    private function fixTrailingArguments(
        File $phpcsFile,
        array $arguments,
        int $firstRedundant,
        int $openParen,
        int $closeParen
    ): void {
        $phpcsFile->fixer->beginChangeset();

        if ($firstRedundant === 0) {
            for ($i = $openParen + 1; $i < $closeParen; $i++) {
                $phpcsFile->fixer->replaceToken($i, '');
            }
        } else {
            $lastKeptArg = $arguments[$firstRedundant - 1];
            $removeFrom = $lastKeptArg['lastTokenPtr'] + 1;

            for ($i = $removeFrom; $i < $closeParen; $i++) {
                $phpcsFile->fixer->replaceToken($i, '');
            }
        }

        $phpcsFile->fixer->endChangeset();
    }

    /**
     * @param list<array{name: ?string, value: string, isNamed: bool, firstTokenPtr: int, lastTokenPtr: int}> $arguments
     * @param list<int> $allRedundantIndexes
     */
    private function fixNamedArgument(
        File $phpcsFile,
        array $arguments,
        int $argIndex,
        array $allRedundantIndexes,
        int $openParen,
        int $closeParen
    ): void {
        $phpcsFile->fixer->beginChangeset();

        $nonRedundantCount = count($arguments) - count($allRedundantIndexes);

        if ($nonRedundantCount === 0) {
            for ($i = $openParen + 1; $i < $closeParen; $i++) {
                $phpcsFile->fixer->replaceToken($i, '');
            }
        } else {
            $arg = $arguments[$argIndex];
            $isLast = $argIndex === count($arguments) - 1;

            if ($isLast === true) {
                $prevArg = $arguments[$argIndex - 1];
                $removeFrom = $prevArg['lastTokenPtr'] + 1;

                for ($i = $removeFrom; $i <= $arg['lastTokenPtr']; $i++) {
                    $phpcsFile->fixer->replaceToken($i, '');
                }
            } else {
                $nextArg = $arguments[$argIndex + 1];
                $removeTo = $nextArg['firstTokenPtr'];

                for ($i = $arg['firstTokenPtr']; $i < $removeTo; $i++) {
                    $phpcsFile->fixer->replaceToken($i, '');
                }
            }
        }

        $phpcsFile->fixer->endChangeset();
    }

    private function normalizeValue(string $value): string
    {
        $value = trim($value);
        $value = (string) preg_replace('/\s+/', ' ', $value);

        $lower = strtolower($value);
        if (in_array($lower, ['true', 'false', 'null'], true) === true) {
            return $lower;
        }

        return $value;
    }
}
