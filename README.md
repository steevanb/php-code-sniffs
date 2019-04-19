[![version](https://img.shields.io/badge/version-2.0.1-green.svg)](https://github.com/steevanb/php-code-sniffs/tree/2.0.1)
[![php](https://img.shields.io/badge/php-^7.1-blue.svg)](https://php.net)
![Lines](https://img.shields.io/badge/code%20lines-1886-green.svg)
![Total Downloads](https://poser.pugx.org/steevanb/php-code-sniffs/downloads)

php-code-sniffs
===============

Use [squizlabs/php_codesniffer](https://github.com/squizlabs/PHP_CodeSniffer) to check your code style.

Remove some coding standards, and add a few more.

[Changelog](changelog.md).

Installation
============

```bash
composer require steevanb/php-code-sniffs ^2.0.1
```

Or if you want to use it with Docker: [steevanb/docker-php-code-sniffs](https://github.com/steevanb/docker-php-code-sniffs).

Usage
=====

Search not respected coding standards
-------------------------------------

```bash
vendor/bin/phpcs --standard=vendor/steevanb/php-code-sniffs/ruleset.xml --report=steevanb\\PhpCodeSniffs\\Reports\\Steevanb src/

# add sniff name to report
vendor/bin/phpcs --standard=vendor/steevanb/php-code-sniffs/ruleset.xml --report=steevanb\\PhpCodeSniffs\\Reports\\Steevanb -s src/

# write results in CSV file
vendor/bin/phpcs --standard=vendor/steevanb/php-code-sniffs/ruleset.xml --report-csv=foo.csv src/
```

Show enabled coding standards
-----------------------------

```bash
vendor/bin/phpcs --standard=vendor/steevanb/php-code-sniffs/ruleset.xml -e
```

Check coding standards in files need to be commited
---------------------------------------------------

```bash
git status --porcelain | grep -E '^[^D\?]{2} .*\.php$' | awk '{print $2}' | xargs -n1 bin/phpcs --standard=vendor/steevanb/php-code-sniffs/ruleset.xml --report=steevanb\\PhpCodeSniffs\\Reports\\Steevanb
```

Configure sniffs
================

Some sniffs could be configured by static methods,
like `steevanb\PhpCodeSniffs\Steevanb\Sniffs\Functions\DisallowMultipleReturnSniff`
and `steevanb\PhpCodeSniffs\Steevanb\Sniffs\Metrics\NestingLevelSniff`,
or the report `steevanb\PhpCodeSniffs\Reports\Steevanb`.

You can configure them by adding a bootstrap script to phpcs:
```bash
vendor/bin/phpcs --bootstrap=phpcs_boostrap.php (...)
```

```php
# phpcs_boostrap.php

// if you use steevanb/docker-php-code-sniffs,
// file path must not be the same between Docker and your local file system
// you can change a part of the path to files who have errors, to make file:// works in bash
steevanb\PhpCodeSniffs\Reports\Steevanb::addReplaceInPath(
    '/var/www/docker',
    '/home/foo/dev/myproject'
);

// some functions could have more than on return keyword
steevanb\PhpCodeSniffs\Steevanb\Sniffs\Functions\DisallowMultipleReturnSniff::addAllowedFunction(
    '/path/foo.php',
    'barMethod'
);

// come methods could have a nesting level greater than 5
steevanb\PhpCodeSniffs\Steevanb\Sniffs\Metrics\NestingLevelSniff::addAllowedNestingLevelMethods('foo.php', 'barMethod');

// allow some deprecated function
steevanb\PhpCodeSniffs\Steevanb\Sniffs\PHP\DeprecatedFunctionsSniff::addAllowDeprecatedFunction('deprecated_function');

// force use groups to be at 3rd level, instead of 1st or 2nd
// example : Symfony\Component\Form\{}
steevanb\PhpCodeSniffs\Steevanb\Sniffs\Uses\GroupUsesSniff::addThirdLevelPrefix('Symfony\Component');
// if you want to configure it for a Symfony project, you can use addSymfonyPrefixes()
steevanb\PhpCodeSniffs\Steevanb\Sniffs\Uses\GroupUsesSniff::addSymfonyPrefixs();
```

Coding standards
================

squizlabs/php_codesniffer
-------------------------

| Sniff |
|-------|
| [Generic.Arrays.ArrayIndent](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Generic/Sniffs/Arrays/ArrayIndentSniff.php) |
| [Generic.Arrays.DisallowLongArraySyntax](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Generic/Sniffs/Arrays/DisallowLongArraySyntaxSniff.php) |
| [Generic.ControlStructures.InlineControlStructure](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Generic/Sniffs/ControlStructures/InlineControlStructureSniff.php) |
| [Generic.Debug.ClosureLinter](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Generic/Sniffs/Debug/ClosureLinterSniff.php) |
| [Generic.Files.ByteOrderMark](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Generic/Sniffs/Files/ByteOrderMarkSniff.php) |
| [Generic.Files.LineEndings](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Generic/Sniffs/Files/LineEndingsSniff.php) |
| [Generic.Files.LineLength](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Generic/Sniffs/Files/LineLengthSniff.php) |
| [Generic.Formatting.DisallowMultipleStatements](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Generic/Sniffs/Formatting/DisallowMultipleStatementsSniff.php) |
| [Generic.Formatting.SpaceAfterCast](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Generic/Sniffs/Formatting/SpaceAfterCastSniff.php) |
| [Generic.Functions.FunctionCallArgumentSpacing](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Generic/Sniffs/Functions/FunctionCallArgumentSpacingSniff.php) |
| [Generic.NamingConventions.ConstructorName](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Generic/Sniffs/NamingConventions/ConstructorNameSniff.php) |
| [Generic.NamingConventions.UpperCaseConstantName](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Generic/Sniffs/NamingConventions/UpperCaseConstantNameSniff.php) |
| [Generic.PHP.DisallowShortOpenTag](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Generic/Sniffs/PHP/DisallowShortOpenTagSniff.php) |
| [Generic.PHP.ForbiddenFunctions](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Generic/Sniffs/PHP/ForbiddenFunctionsSniff.php) |
| [Generic.PHP.LowerCaseConstant](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Generic/Sniffs/PHP/LowerCaseConstantSniff.php) |
| [Generic.PHP.LowerCaseKeyword](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Generic/Sniffs/PHP/LowerCaseKeywordSniff.php) |
| [Generic.WhiteSpace.DisallowTabIndent](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Generic/Sniffs/WhiteSpace/DisallowTabIndentSniff.php) |
| [Generic.WhiteSpace.IncrementDecrementSpacing](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Generic/Sniffs/WhiteSpace/IncrementDecrementSpacingSniff.php) |
| [Generic.WhiteSpace.LanguageConstructSpacing](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Generic/Sniffs/WhiteSpace/LanguageConstructSpacingSniff.php) |
| [Generic.WhiteSpace.ScopeIndent](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Generic/Sniffs/WhiteSpace/ScopeIndentSniff.php) |

| Sniff |
|-------|
| [PEAR.ControlStructures.MultiLineCondition](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/PEAR/Sniffs/ControlStructures/MultiLineConditionSniff.php) |
| [PEAR.Files.IncludingFile](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/PEAR/Sniffs/Files/IncludingFileSniff.php) |
| [PEAR.Formatting.MultiLineAssignment](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/PEAR/Sniffs/Formatting/MultiLineAssignmentSniff.php) |
| [PEAR.Functions.FunctionCallSignature](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/PEAR/Sniffs/Functions/FunctionCallSignatureSniff.php) |
| [PEAR.Functions.ValidDefaultValue](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/PEAR/Sniffs/Functions/ValidDefaultValueSniff.php) |

| Sniff |
|-------|
| [PSR1.Classes.ClassDeclaration](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/PSR1/Sniffs/Classes/ClassDeclarationSniff.php) |
| [PSR1.Files.SideEffects](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/PSR1/Sniffs/Files/SideEffectsSniff.php) |
| [PSR1.Methods.CamelCapsMethodName](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/PSR1/Sniffs/Methods/CamelCapsMethodNameSniff.php) |

| Sniff |
|-------|
| [PSR2.Classes.ClassDeclaration](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/PSR2/Sniffs/Classes/ClassDeclarationSniff.php) |
| [PSR2.Classes.PropertyDeclaration](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/PSR2/Sniffs/Classes/PropertyDeclarationSniff.php) |
| [PSR2.ControlStructures.ControlStructureSpacing](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/PSR2/Sniffs/ControlStructures/ControlStructureSpacingSniff.php) |
| [PSR2.ControlStructures.ElseIfDeclaration](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/PSR2/Sniffs/ControlStructures/ElseIfDeclarationSniff.php) |
| [PSR2.ControlStructures.SwitchDeclaration](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/PSR2/Sniffs/ControlStructures/SwitchDeclarationSniff.php) |
| [PSR2.Files.ClosingTag](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/PSR2/Sniffs/Files/ClosingTagSniff.php) |
| [PSR2.Files.EndFileNewline](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/PSR2/Sniffs/Files/EndFileNewlineSniff.php) |
| [PSR2.Methods.FunctionCallSignature](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/PSR2/Sniffs/Methods/FunctionCallSignatureSniff.php) |
| [PSR2.Methods.FunctionClosingBrace](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/PSR2/Sniffs/Methods/FunctionClosingBraceSniff.php) |
| [PSR2.Methods.MethodDeclaration](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/PSR2/Sniffs/Methods/MethodDeclarationSniff.php) |
| [PSR2.Namespaces.NamespaceDeclaration](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/PSR2/Sniffs/Namespaces/NamespaceDeclarationSniff.php) |
| [PSR2.Namespaces.UseDeclaration](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/PSR2/Sniffs/Namespaces/UseDeclarationSniff.php) |

| Sniff |
|-------|
| [Squiz.Arrays.ArrayBracketSpacing](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Arrays/ArrayBracketSpacingSniff.php) |
| [Squiz.Arrays.ArrayDeclaration](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Arrays/ArrayDeclarationSniff.php) |
| [Squiz.CSS.ClassDefinitionClosingBraceSpace](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/CSS/ClassDefinitionClosingBraceSpaceSniff.php) |
| [Squiz.CSS.ClassDefinitionNameSpacing](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/CSS/ClassDefinitionNameSpacingSniff.php) |
| [Squiz.CSS.ClassDefinitionOpeningBraceSpace](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/CSS/ClassDefinitionOpeningBraceSpaceSniff.php) |
| [Squiz.CSS.ColonSpacing](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/CSS/ColonSpacingSniff.php) |
| [Squiz.CSS.ColourDefinition](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/CSS/ColourDefinitionSniff.php) |
| [Squiz.CSS.DisallowMultipleStyleDefinitions](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/CSS/DisallowMultipleStyleDefinitionsSniff.php) |
| [Squiz.CSS.DuplicateClassDefinition](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/CSS/DuplicateClassDefinitionSniff.php) |
| [Squiz.CSS.DuplicateStyleDefinition](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/CSS/DuplicateStyleDefinitionSniff.php) |
| [Squiz.CSS.EmptyClassDefinition](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/CSS/EmptyClassDefinitionSniff.php) |
| [Squiz.CSS.EmptyStyleDefinition](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/CSS/EmptyStyleDefinitionSniff.php) |
| [Squiz.CSS.ForbiddenStyles](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/CSS/ForbiddenStylesSniff.php) |
| [Squiz.CSS.Indentation](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/CSS/IndentationSniff.php) |
| [Squiz.CSS.LowercaseStyleDefinition](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/CSS/LowercaseStyleDefinitionSniff.php) |
| [Squiz.CSS.MissingColon](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/CSS/MissingColonSniff.php) |
| [Squiz.CSS.NamedColours](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/CSS/NamedColoursSniff.php) |
| [Squiz.CSS.Opacity](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/CSS/OpacitySniff.php) |
| [Squiz.CSS.SemicolonSpacing](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/CSS/SemicolonSpacingSniff.php) |
| [Squiz.CSS.ShorthandSize](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/CSS/ShorthandSizeSniff.php) |
| [Squiz.Classes.ClassDeclaration](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Classes/ClassDeclarationSniff.php) |
| [Squiz.Classes.ClassFileName](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Classes/ClassFileNameSniff.php) |
| [Squiz.Classes.DuplicateProperty](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Classes/DuplicatePropertySniff.php) |
| [Squiz.Classes.LowercaseClassKeywords](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Classes/LowercaseClassKeywordsSniff.php) |
| [Squiz.Classes.SelfMemberReference](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Classes/SelfMemberReferenceSniff.php) |
| [Squiz.Classes.ValidClassName](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Classes/ValidClassNameSniff.php) |
| [Squiz.Commenting.BlockComment](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Commenting/BlockCommentSniff.php) |
| [Squiz.Commenting.ClassComment](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Commenting/ClassCommentSniff.php) |
| [Squiz.Commenting.EmptyCatchComment](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Commenting/EmptyCatchCommentSniff.php) |
| [Squiz.Commenting.FunctionCommentThrowTag](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Commenting/FunctionCommentThrowTagSniff.php) |
| [Squiz.Commenting.InlineComment](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Commenting/InlineCommentSniff.php) |
| [Squiz.Commenting.PostStatementComment](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Commenting/PostStatementCommentSniff.php) |
| [Squiz.ControlStructures.ControlSignature](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/ControlStructures/ControlSignatureSniff.php) |
| [Squiz.ControlStructures.ForEachLoopDeclaration](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/ControlStructures/ForEachLoopDeclarationSniff.php) |
| [Squiz.ControlStructures.ForLoopDeclaration](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/ControlStructures/ForLoopDeclarationSniff.php) |
| [Squiz.ControlStructures.InlineIfDeclaration](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/ControlStructures/InlineIfDeclarationSniff.php) |
| [Squiz.ControlStructures.LowercaseDeclaration](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/ControlStructures/LowercaseDeclarationSniff.php) |
| [Squiz.ControlStructures.SwitchDeclaration](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/ControlStructures/SwitchDeclarationSniff.php) |
| [Squiz.Debug.JSLint](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Debug/JSLintSniff.php) |
| [Squiz.Debug.JavaScriptLint](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Debug/JavaScriptLintSniff.php) |
| [Squiz.Formatting.OperatorBracket](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Formatting/OperatorBracketSniff.php) |
| [Squiz.Functions.FunctionDeclaration](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Functions/FunctionDeclarationSniff.php) |
| [Squiz.Functions.FunctionDeclarationArgumentSpacing](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Functions/FunctionDeclarationArgumentSpacingSniff.php) |
| [Squiz.Functions.FunctionDuplicateArgument](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Functions/FunctionDuplicateArgumentSniff.php) |
| [Squiz.Functions.GlobalFunction](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Functions/GlobalFunctionSniff.php) |
| [Squiz.Functions.LowercaseFunctionKeywords](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Functions/LowercaseFunctionKeywordsSniff.php) |
| [Squiz.Functions.MultiLineFunctionDeclaration](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Functions/MultiLineFunctionDeclarationSniff.php) |
| [Squiz.NamingConventions.ValidFunctionName](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/NamingConventions/ValidFunctionNameSniff.php) |
| [Squiz.NamingConventions.ValidVariableName](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/NamingConventions/ValidVariableNameSniff.php) |
| [Squiz.Objects.DisallowObjectStringIndex](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Objects/DisallowObjectStringIndexSniff.php) |
| [Squiz.Objects.ObjectInstantiation](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Objects/ObjectInstantiationSniff.php) |
| [Squiz.Objects.ObjectMemberComma](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Objects/ObjectMemberCommaSniff.php) |
| [Squiz.Operators.ComparisonOperatorUsage](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Operators/ComparisonOperatorUsageSniff.php) |
| [Squiz.Operators.IncrementDecrementUsage](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Operators/IncrementDecrementUsageSniff.php) |
| [Squiz.Operators.ValidLogicalOperators](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Operators/ValidLogicalOperatorsSniff.php) |
| [Squiz.PHP.CommentedOutCode](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/PHP/CommentedOutCodeSniff.php) |
| [Squiz.PHP.DisallowBooleanStatement](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/PHP/DisallowBooleanStatementSniff.php) |
| [Squiz.PHP.DisallowComparisonAssignment](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/PHP/DisallowComparisonAssignmentSniff.php) |
| [Squiz.PHP.DisallowMultipleAssignments](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/PHP/DisallowMultipleAssignmentsSniff.php) |
| [Squiz.PHP.DisallowSizeFunctionsInLoops](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/PHP/DisallowSizeFunctionsInLoopsSniff.php) |
| [Squiz.PHP.DiscouragedFunctions](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/PHP/DiscouragedFunctionsSniff.php) |
| [Squiz.PHP.EmbeddedPhp](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/PHP/EmbeddedPhpSniff.php) |
| [Squiz.PHP.Eval](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/PHP/EvalSniff.php) |
| [Squiz.PHP.GlobalKeyword](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/PHP/GlobalKeywordSniff.php) |
| [Squiz.PHP.Heredoc](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/PHP/HeredocSniff.php) |
| [Squiz.PHP.InnerFunctions](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/PHP/InnerFunctionsSniff.php) |
| [Squiz.PHP.LowercasePHPFunctions](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/PHP/LowercasePHPFunctionsSniff.php) |
| [Squiz.PHP.NonExecutableCode](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/PHP/NonExecutableCodeSniff.php) |
| [Squiz.Scope.MemberVarScope](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Scope/MemberVarScopeSniff.php) |
| [Squiz.Scope.MethodScope](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Scope/MethodScopeSniff.php) |
| [Squiz.Scope.StaticThisUsage](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Scope/StaticThisUsageSniff.php) |
| [Squiz.Strings.ConcatenationSpacing](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Strings/ConcatenationSpacingSniff.php) |
| [Squiz.Strings.DoubleQuoteUsage](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Strings/DoubleQuoteUsageSniff.php) |
| [Squiz.Strings.EchoedStrings](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/Strings/EchoedStringsSniff.php) |
| [Squiz.WhiteSpace.CastSpacing](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/WhiteSpace/CastSpacingSniff.php) |
| [Squiz.WhiteSpace.FunctionClosingBraceSpace](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/WhiteSpace/FunctionClosingBraceSpaceSniff.php) |
| [Squiz.WhiteSpace.FunctionOpeningBraceSpace](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/WhiteSpace/FunctionOpeningBraceSpaceSniff.php) |
| [Squiz.WhiteSpace.FunctionSpacing](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/WhiteSpace/FunctionSpacingSniff.php) |
| [Squiz.WhiteSpace.LanguageConstructSpacing](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/WhiteSpace/LanguageConstructSpacingSniff.php) |
| [Squiz.WhiteSpace.LogicalOperatorSpacing](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/WhiteSpace/LogicalOperatorSpacingSniff.php) |
| [Squiz.WhiteSpace.MemberVarSpacing](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/WhiteSpace/MemberVarSpacingSniff.php) |
| [Squiz.WhiteSpace.ObjectOperatorSpacing](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/WhiteSpace/ObjectOperatorSpacingSniff.php) |
| [Squiz.WhiteSpace.OperatorSpacing](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/WhiteSpace/OperatorSpacingSniff.php) |
| [Squiz.WhiteSpace.PropertyLabelSpacing](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/WhiteSpace/PropertyLabelSpacingSniff.php) |
| [Squiz.WhiteSpace.ScopeClosingBrace](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/WhiteSpace/ScopeClosingBraceSniff.php) |
| [Squiz.WhiteSpace.ScopeKeywordSpacing](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/WhiteSpace/ScopeKeywordSpacingSniff.php) |
| [Squiz.WhiteSpace.SemicolonSpacing](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/WhiteSpace/SemicolonSpacingSniff.php) |
| [Squiz.WhiteSpace.SuperfluousWhitespace](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Squiz/Sniffs/WhiteSpace/SuperfluousWhitespaceSniff.php) |

| Sniff |
|-------|
| [Zend.Debug.CodeAnalyzer](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Zend/Sniffs/Debug/CodeAnalyzerSniff.php) |
| [Zend.Files.ClosingTag](https://github.com/squizlabs/PHP_CodeSniffer/blob/3.4.2/src/Standards/Zend/Sniffs/Files/ClosingTagSniff.php) |
  
steevanb/php-code-sniffer
-------------------------

| Sniff |
|-------|
| [Steevanb.Arrays.DisallowShortArraySyntaxSpaces](https://github.com/steevanb/php-code-sniffs/blob/2.0.0/Steevanb/Sniffs/Arrays/DisallowShortArraySyntaxSpacesSniff.php) |
| [Steevanb.Classes.ClassNameIsFileName](https://github.com/steevanb/php-code-sniffs/blob/2.0.0/Steevanb/Sniffs/Classes/ClassNameIsFileNameSniff.php) |
| [Steevanb.CodeAnalysis.EmptyStatement](https://github.com/steevanb/php-code-sniffs/blob/2.0.0/Steevanb/Sniffs/CodeAnalysis/EmptyStatementSniff.php) |
| [Steevanb.CodeAnalysis.StrictTypes](https://github.com/steevanb/php-code-sniffs/blob/2.0.0/Steevanb/Sniffs/CodeAnalysis/StrictTypesSniff.php) |
| [Steevanb.Comparators.DisallowExclamationPoint](https://github.com/steevanb/php-code-sniffs/blob/2.0.0/Steevanb/Sniffs/Comparators/DisallowExclamationPointSniff.php) |
| [Steevanb.ControlStructures.ElseIfDeclaration](https://github.com/steevanb/php-code-sniffs/blob/2.0.0/Steevanb/Sniffs/ControlStructures/ElseIfDeclarationSniff.php) |
| [Steevanb.Functions.DisallowMultipleReturn](https://github.com/steevanb/php-code-sniffs/blob/2.0.0/Steevanb/Sniffs/Functions/DisallowMultipleReturnSniff.php) |
| [Steevanb.Metrics.NestingLevel](https://github.com/steevanb/php-code-sniffs/blob/2.0.0/Steevanb/Sniffs/Metrics/NestingLevelSniff.php) |
| [Steevanb.Namespaces.UseDeclaration](https://github.com/steevanb/php-code-sniffs/blob/2.0.0/Steevanb/Sniffs/Namespaces/UseDeclarationSniff.php) |
| [Steevanb.NamingConventions.CamelCapsFunctionName](https://github.com/steevanb/php-code-sniffs/blob/2.0.0/Steevanb/Sniffs/NamingConventions/CamelCapsFunctionNameSniff.php) |
| [Steevanb.NamingConventions.ValidVariableName](https://github.com/steevanb/php-code-sniffs/blob/2.0.0/Steevanb/Sniffs/NamingConventions/ValidVariableNameSniff.php) |
| [Steevanb.PHP.DeprecatedFunctions](https://github.com/steevanb/php-code-sniffs/blob/2.0.0/Steevanb/Sniffs/PHP/DeprecatedFunctionsSniff.php) |
| [Steevanb.PHP.DisallowSelf](https://github.com/steevanb/php-code-sniffs/blob/2.0.0/Steevanb/Sniffs/PHP/DisallowSelfSniff.php) |
| [Steevanb.PHP.DisallowMultipleEmptyLines](https://github.com/steevanb/php-code-sniffs/blob/2.0.0/Steevanb/Sniffs/PHP/DisallowMultipleEmptyLinesSniff.php) |
| [Steevanb.Syntax.Concatenation](https://github.com/steevanb/php-code-sniffs/blob/2.0.0/Steevanb/Sniffs/Syntax/ConcatenationSniff.php) |
| [Steevanb.Uses.GroupUses](https://github.com/steevanb/php-code-sniffs/blob/2.0.0/Steevanb/Sniffs/Uses/GroupUsesSniff.php) |
