[![version](https://img.shields.io/badge/version-1.0.0-green.svg)](https://github.com/steevanb/php-code-sniffs/tree/1.0.0)
[![php](https://img.shields.io/badge/php-^7.1-blue.svg)](https://php.net)
![Lines](https://img.shields.io/badge/code%20lines-XXXX-green.svg)
![Total Downloads](https://poser.pugx.org/steevanb/php-code-sniffs/downloads)

php-code-sniffs
===============

Use [squizlabs/php_codesniffer](https://github.com/squizlabs/PHP_CodeSniffer) to check your code style.

Remove some coding standards, and add a few more.

Installation
============

```bash
composer require steevanb/php-code-sniffs ^1.0
```

Or if you want to use it with Docker: [steevanb/docker-php-code-sniffs](https://github.com/steevanb/docker-php-code-sniffs).

Usage
=====

Search not respected coding standards
-------------------------------------

```bash
vendor/bin/phpcs --standard=vendor/steevanb/php-code-sniffs/ruleset.xml --report=Steevanb src/

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
git status --porcelain | grep -E '^[^D\?]{2} .*\.php$' | awk '{print $2}' | xargs -n1 bin/phpcs --standard=vendor/steevanb/php-code-sniffs/ruleset.xml --report=Steevanb
```

Configure sniffs
================

Some sniffs could be configured by static methods,
like `Steevanb_Sniffs_Functions_DisallowMultipleReturnSniff` and `Steevanb_Sniffs_Metrics_NestingLevelSniff`,
or the report `PHP_CodeSniffer_Reports_Steevanb`.

You can configure them by adding a bootstrap script to phpcs:
```bash
vendor/bin/phpcs --bootstrap=phpcs_boostrap.php (...)
```

```php
# phpcs_boostrap.php

// if you use steevanb/docker-php-code-sniffs,
// file path must not be the same between Docker and your local file system
// you can change a part of the path to files who have errors, to make file:// works in bash
PHP_CodeSniffer_Reports_Steevanb::addReplaceInPath(
    '/var/www/docker',
    '/home/foo/dev/myproject'
);

// some functions could have more than on return keyword
Steevanb_Sniffs_Functions_DisallowMultipleReturnSniff::addAllowedFunction(
    '/path/foo.php',
    'barMethod'
);

// come methods could have a nesting level greater than 5
Steevanb_Sniffs_Metrics_NestingLevelSniff::addAllowedNestingLevelMethods('foo.php', 'barMethod');

// allow some deprecated function
Steevanb_Sniffs_PHP_DeprecatedFunctionsSniff::addAllowDeprecatedFunction('deprecated_function');

// force use groups to be at 3rd level, instead of 1st or 2nd
// example : Symfony\Component\Form\{}
Steevanb_Sniffs_Uses_GroupUsesSniff::addThirdLevelPrefix('Symfony\Component');
// if you want to configure it for a Symfony project, you can use addSymfonyPrefixs()
Steevanb_Sniffs_Uses_GroupUsesSniff::addSymfonyPrefixs();
```

Coding standards
================

squizlabs/php_codesniffer
-------------------------

| Sniff | Apply on |
|-------|----------|
| [Generic_Sniffs_Arrays_DisallowLongArraySyntaxSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/Arrays/DisallowLongArraySyntaxSniff.php) | `array()` |
| [Generic_Sniffs_Classes_DuplicateClassNameSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/Classes/DuplicateClassNameSniff.php) | `class` |
| [Generic_Sniffs_CodeAnalysis_ForLoopShouldBeWhileLoopSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/CodeAnalysis/ForLoopShouldBeWhileLoopSniff.php) | `for` |
| [Generic_Sniffs_CodeAnalysis_ForLoopWithTestFunctionCallSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/CodeAnalysis/ForLoopWithTestFunctionCallSniff.php) | `for` |
| [Generic_Sniffs_CodeAnalysis_JumbledIncrementerSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/CodeAnalysis/JumbledIncrementerSniff.php) | `for` |
| [Generic_Sniffs_CodeAnalysis_UnconditionalIfStatementSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/CodeAnalysis/UnconditionalIfStatementSniff.php) | `if`, `elseif` |
| [Generic_Sniffs_CodeAnalysis_UnnecessaryFinalModifierSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/CodeAnalysis/UnnecessaryFinalModifierSniff.php) | `final class` |
| [Generic_Sniffs_CodeAnalysis_UnusedFunctionParameterSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/CodeAnalysis/UnusedFunctionParameterSniff.php) | `function`, closure |
| [Generic_Sniffs_CodeAnalysis_UselessOverridingMethodSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/CodeAnalysis/UselessOverridingMethodSniff.php) | `function` |
| [Generic_Sniffs_Commenting_FixmeSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/Commenting/FixmeSniff.php) | `#`, `//`, `/* */`, `/** */` |
| [Generic_Sniffs_ControlStructures_InlineControlStructureSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/ControlStructures/InlineControlStructureSniff.php) | `if`, `else`, `elseif`, `for`, `foreach`, `do`, `while`, `switch` |
| [Generic_Sniffs_Debug_ClosureLinterSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/Debug/ClosureLinterSniff.php) | `<?php`, `<?`, `<%` |
| [Generic_Sniffs_Files_EndFileNewlineSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/Files/EndFileNewlineSniff.php) | `<?php`, `<?`, `<%` |
| [Generic_Sniffs_Files_InlineHTMLSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/Files/InlineHTMLSniff.php) | T_INLINE_HTML |
| [Generic_Sniffs_Files_LineEndingsSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/Files/LineEndingsSniff.php) | `<?php`, `<?`, `<%` |
| [Generic_Sniffs_Files_LineLengthSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/Files/LineLengthSniff.php) | `<?php`, `<?`, `<%` (max length: 120) |
| [Generic_Sniffs_Files_OneClassPerFileSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/Files/OneClassPerFileSniff.php) | `class` |
| [Generic_Sniffs_Files_OneInterfacePerFileSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/Files/OneInterfacePerFileSniff.php) | `interface` |
| [Generic_Sniffs_Files_OneTraitPerFileSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/Files/OneTraitPerFileSniff.php) | `trait` |
| [Generic_Sniffs_Formatting_DisallowMultipleStatementsSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/Formatting/DisallowMultipleStatementsSniff.php) | `;` |
| [Generic_Sniffs_Formatting_SpaceAfterCastSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/Formatting/SpaceAfterCastSniff.php) | `(int) $var` |
| [Generic_Sniffs_Functions_CallTimePassByReferenceSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/Functions/CallTimePassByReferenceSniff.php) | `foo(&$parameter)` |
| [Generic_Sniffs_Functions_FunctionCallArgumentSpacingSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/Functions/FunctionCallArgumentSpacingSniff.php) | `function`, `$parameter` |
| [Generic_Sniffs_Functions_OpeningFunctionBraceBsdAllmanSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/Functions/OpeningFunctionBraceBsdAllmanSniff.php) | `function`, closure |
| [Generic_Sniffs_Functions_OpeningFunctionBraceKernighanRitchieSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/Functions/OpeningFunctionBraceKernighanRitchieSniff.php) | `function`, closure |
| [Generic_Sniffs_NamingConventions_CamelCapsFunctionNameSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/NamingConventions/CamelCapsFunctionNameSniff.php) | `function` |
| [Generic_Sniffs_NamingConventions_ConstructorNameSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/NamingConventions/ConstructorNameSniff.php) | `class MyClass { function MyClass() }` |
| [Generic_Sniffs_NamingConventions_UpperCaseConstantNameSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/NamingConventions/UpperCaseConstantNameSniff.php) | `const` |
| [Generic_Sniffs_PHP_BacktickOperatorSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/PHP/BacktickOperatorSniff.php) | ` |
| [Generic_Sniffs_PHP_CharacterBeforePHPOpeningTagSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/PHP/CharacterBeforePHPOpeningTagSniff.php) | `<?`, `<?php`, `<%` |
| [Generic_Sniffs_PHP_DisallowAlternativePHPTagsSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/PHP/DisallowAlternativePHPTagsSniff.php) | `<%`, `<%= %>`, `<script>` |
| [Generic_Sniffs_PHP_DisallowShortOpenTagSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/PHP/DisallowShortOpenTagSniff.php) | `<?`, `<?= ?>` |
| [Generic_Sniffs_PHP_ForbiddenFunctionsSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/PHP/ForbiddenFunctionsSniff.php) | `sizeof`, `delete` |
| [Generic_Sniffs_PHP_LowerCaseConstantSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/PHP/LowerCaseConstantSniff.php) | `TRUE`, `FALSE`, `NULL` |
| [Generic_Sniffs_PHP_LowerCaseKeywordSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/PHP/LowerCaseKeywordSniff.php) | All PHP keywords |
| [Generic_Sniffs_PHP_NoSilencedErrorsSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/PHP/NoSilencedErrorsSniff.php) | `@$var` |
| [Generic_Sniffs_PHP_SAPIUsageSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/PHP/SAPIUsageSniff.php) | `php_sapi_name()` |
| [Generic_Sniffs_WhiteSpace_DisallowTabIndentSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/WhiteSpace/DisallowTabIndentSniff.php) | `<?php`, `<?`, `<%` |
| [Generic_Sniffs_WhiteSpace_ScopeIndentSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Generic/Sniffs/WhiteSpace/ScopeIndentSniff.php) | `<?php`, `<?`, `<%` |

| Sniff | Apply on |
|-------|----------|
| [PEAR_Sniffs_Classes_ClassDeclarationSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/PEAR/Sniffs/Classes/ClassDeclarationSniff.php) | `class`, `interface`, `trait` |
| [PEAR_Sniffs_ControlStructures_ControlSignatureSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/PEAR/Sniffs/ControlStructures/ControlSignatureSniff.php) | `do`, `while`, `for`, `foreach`, `if`, `else if`, `elseif`, `else` |
| [PEAR_Sniffs_Commenting_InlineCommentSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/PEAR/Sniffs/Commenting/InlineCommentSniff.php) | `#` |
| [PEAR_Sniffs_Functions_FunctionCallSignatureSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/PEAR/Sniffs/Functions/FunctionCallSignatureSniff.php) | `eval`, `exit`, `include`, `include_once`, `require`, `require_once`, `isset`, `unset`, `empty` |
| [PEAR_Sniffs_Functions_FunctionDeclarationSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/PEAR/Sniffs/Functions/FunctionDeclarationSniff.php) | `function`, closure |
| [PEAR_Sniffs_Functions_ValidDefaultValueSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/PEAR/Sniffs/Functions/ValidDefaultValueSniff.php) | `function foo($bar = 'default', $baz)` |
| [PEAR_Sniffs_WhiteSpace_ScopeClosingBraceSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/PEAR/Sniffs/WhiteSpace/ScopeClosingBraceSniff.php) | PHP keywords with scope `{}` |

| Sniff | Apply on |
|-------|----------|
| [PSR1_Sniffs_Classes_ClassDeclarationSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/PSR1/Sniffs/Classes/ClassDeclarationSniff.php) | `class`, `interface`, `trait` |
| [PSR1_Sniffs_Files_SideEffectsSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/PSR1/Sniffs/Files/SideEffectsSniff.php) | `<?php`, `<?`, `<%` |

| Sniff | Apply on |
|-------|----------|
| [PSR2_Sniffs_Namespaces_UseDeclarationSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/PSR2/Sniffs/Namespaces/UseDeclarationSniff.php) | `use` |
| [PSR2_Sniffs_Namespaces_NamespaceDeclarationSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/PSR2/Sniffs/Namespaces/NamespaceDeclarationSniff.php) | `namespace` |
| [PSR2_Sniffs_Files_ClosingTagSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/PSR2/Sniffs/Files/ClosingTagSniff.php) | `?>`, `%>` |
| [PSR2_Sniffs_Files_EndFileNewlineSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/PSR2/Sniffs/Files/EndFileNewlineSniff.php) | `<?php`, `<?`, `<%` |
| [PSR2_Sniffs_ControlStructures_ElseIfDeclarationSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/PSR2/Sniffs/ControlStructures/ElseIfDeclarationSniff.php) | `else if` |
| [PSR2_Sniffs_Methods_FunctionClosingBraceSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/PSR2/Sniffs/Methods/FunctionClosingBraceSniff.php) | `function`, closure |
| [PSR2_Sniffs_Methods_FunctionCallSignatureSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/PSR2/Sniffs/Methods/FunctionCallSignatureSniff.php) | `foo()` |
| [PSR2_Sniffs_Methods_MethodDeclarationSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/PSR2/Sniffs/Methods/MethodDeclarationSniff.php) | `function` |
| [PSR2_Sniffs_Classes_PropertyDeclarationSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/PSR2/Sniffs/Classes/PropertyDeclarationSniff.php) | Class property declaration |
| [PSR2_Sniffs_Classes_ClassDeclarationSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/PSR2/Sniffs/Classes/ClassDeclarationSniff.php) | `class`, `interface`, `trait` |

| Sniff | Apply on |
|-------|----------|
| [Squiz_Sniffs_Arrays_ArrayBracketSpacingSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Squiz/Sniffs/Arrays/ArrayBracketSpacingSniff.php) | `[]` |
| [Squiz_Sniffs_Classes_ValidClassNameSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Squiz/Sniffs/Classes/ValidClassNameSniff.php) | `class`, `interface`, `trait` |
| [Squiz_Sniffs_ControlStructures_ControlSignatureSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Squiz/Sniffs/ControlStructures/ControlSignatureSniff.php) | `try`, `catch`, `finally`, `do`, `while`, `for`, `foreach`, `if`, `else`, `elseif`, `switch` |
| [Squiz_Sniffs_ControlStructures_ForEachLoopDeclarationSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Squiz/Sniffs/ControlStructures/ForEachLoopDeclarationSniff.php) | `foreach` |
| [Squiz_Sniffs_ControlStructures_ForLoopDeclarationSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Squiz/Sniffs/ControlStructures/ForLoopDeclarationSniff.php) | `for` |
| [Squiz_Sniffs_ControlStructures_LowercaseDeclarationSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Squiz/Sniffs/ControlStructures/LowercaseDeclarationSniff.php) | `if`, `else`, `elseif`, `foreach`, `for`, `do`, `while`, `switch`, `try`, `catch` |
| [Squiz_Sniffs_Functions_FunctionDeclarationSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Squiz/Sniffs/Functions/FunctionDeclarationSniff.php) | `function` |
| [Squiz_Sniffs_Functions_FunctionDeclarationArgumentSpacingSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Squiz/Sniffs/Functions/FunctionDeclarationArgumentSpacingSniff.php) | `function`, closure |
| [Squiz_Sniffs_Functions_GlobalFunctionSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Squiz/Sniffs/Functions/GlobalFunctionSniff.php) | `function` |
| [Squiz_Sniffs_Functions_LowercaseFunctionKeywordsSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Squiz/Sniffs/Functions/LowercaseFunctionKeywordsSniff.php) | `function`, `public`, `protected`, `private`, `static` |
| [Squiz_Sniffs_Functions_MultiLineFunctionDeclarationSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Squiz/Sniffs/Functions/MultiLineFunctionDeclarationSniff.php) | `function` |
| [Squiz_Sniffs_PHP_CommentedOutCodeSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Squiz/Sniffs/PHP/CommentedOutCodeSniff.php) | `#`, `//`, `/* */`, `/** */` |
| [Squiz_Sniffs_PHP_LowercasePHPFunctionsSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Squiz/Sniffs/PHP/LowercasePHPFunctionsSniff.php) | All PHP functions |
| [Squiz_Sniffs_Scope_MemberVarScopeSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Squiz/Sniffs/Scope/MemberVarScopeSniff.php) | Class properties |
| [Squiz_Sniffs_Scope_MethodScopeSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Squiz/Sniffs/Scope/MethodScopeSniff.php) | `function` |
| [Squiz_Sniffs_Strings_DoubleQuoteUsageSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Squiz/Sniffs/Strings/DoubleQuoteUsageSniff.php) | `"` |
| [Squiz_Sniffs_WhiteSpace_ScopeClosingBraceSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Squiz/Sniffs/WhiteSpace/ScopeClosingBraceSniff.php) | PHP keywords with scope `{}` |
| [Squiz_Sniffs_WhiteSpace_ScopeKeywordSpacingSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Squiz/Sniffs/WhiteSpace/ScopeKeywordSpacingSniff.php) | `public`, `protected`, `private`, `static` |
| [Squiz_Sniffs_WhiteSpace_SuperfluousWhitespaceSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Squiz/Sniffs/WhiteSpace/SuperfluousWhitespaceSniff.php) | All files |

| Sniff | Apply on |
|-------|----------|
| [Zend_Sniffs_Debug_CodeAnalyzerSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Zend/Sniffs/Debug/CodeAnalyzerSniff.php) | All PHP files |
| [Zend_Sniffs_Files_ClosingTagSniff](https://github.com/squizlabs/PHP_CodeSniffer/blob/2.9/CodeSniffer/Standards/Zend/Sniffs/Files/ClosingTagSniff.php) | `<?php`, `<?`, `<%` |

leaphub/phpcs-symfony2-standard
-------------------------------

| Sniff | Apply on |
|-------|----------|
| [Symfony2_Sniffs_Classes_MultipleClassesOneFileSniff](https://github.com/leaphub/phpcs-symfony2-standard/blob/v2.0.3/Sniffs/Classes/MultipleClassesOneFileSniff.php) | `class` |
| [Symfony2_Sniffs_Formatting_BlankLineBeforeReturnSniff](https://github.com/leaphub/phpcs-symfony2-standard/blob/v2.0.3/Sniffs/Formatting/BlankLineBeforeReturnSniff.php) | `return` |
| [Symfony2_Sniffs_NamingConventions_InterfaceSuffixSniff](https://github.com/leaphub/phpcs-symfony2-standard/blob/v2.0.3/Sniffs/NamingConventions/InterfaceSuffixSniff.php) | `interface` |
| [Symfony2_Sniffs_WhiteSpace_DiscourageFitzinatorSniff](https://github.com/leaphub/phpcs-symfony2-standard/blob/v2.0.3/Sniffs/WhiteSpace/DiscourageFitzinatorSniff.php) | ` ` |

steevanb/php-code-sniffs
------------------------

| Sniff | Apply on |
|-------|----------|
| [Steevanb_Sniffs_Arrays_DisallowShortArraySyntaxSpacesSniff](https://github.com/steevanb/php-code-sniffs/blob/master/Steevanb/Sniffs/Arrays/DisallowShortArraySyntaxSpacesSniff.php) | `[]` |
| [Steevanb_Sniffs_Classes_ClassNameIsFileNameSniff](https://github.com/steevanb/php-code-sniffs/blob/master/Steevanb/Sniffs/Classes/ClassNameIsFileNameSniff.php) | `class`, `interface`, `trait` |
| [Steevanb_Sniffs_Comparators_DisallowExclamationPointSniff](https://github.com/steevanb/php-code-sniffs/blob/master/Steevanb/Sniffs/Comparators/DisallowExclamationPointSniff.php) | `!` |
| [Steevanb_Sniffs_CodeAnalysis_EmptyStatementSniff](https://github.com/steevanb/php-code-sniffs/blob/master/Steevanb/Sniffs/CodeAnalysis/EmptyStatementSniff.php) | `try`, `finally`, `do`, `while`, `if`, `else`, `elseif`, `for`, `foreach`, `switch` |
| [Steevanb_Sniffs_CodeAnalysis_StrictTypesSniff](https://github.com/steevanb/php-code-sniffs/blob/master/Steevanb/Sniffs/CodeAnalysis/StrictTypesSniff.php) | `declare(stric_types=1)` |
| [Steevanb_Sniffs_ControlStructures_SwitchDeclarationSniff](https://github.com/steevanb/php-code-sniffs/blob/master/Steevanb/Sniffs/ControlStructures/SwitchDeclarationSniff.php) | `switch` |
| [Steevanb_Sniffs_Functions_DisallowMultipleReturnSniff](https://github.com/steevanb/php-code-sniffs/blob/master/Steevanb/Sniffs/Functions/DisallowMultipleReturnSniff.php) | `return` |
| [Steevanb_Sniffs_Metrics_NestingLevelSniff](https://github.com/steevanb/php-code-sniffs/blob/master/Steevanb/Sniffs/Metrics/NestingLevelSniff.php) | `function` |
| [Steevanb_Sniffs_Namespaces_UseDeclarationSniff](https://github.com/steevanb/php-code-sniffs/blob/master/Steevanb/Sniffs/Namespaces/UseDeclarationSniff.php) | `use` |
| [Steevanb_Sniffs_PHP_DeprecatedFunctionsSniff](https://github.com/steevanb/php-code-sniffs/blob/master/Steevanb/Sniffs/PHP/DeprecatedFunctionsSniff.php) | All PHP deprecated functions |
| [Steevanb_Sniffs_PHP_DisallowMultipleEmptyLinesSniff](https://github.com/steevanb/php-code-sniffs/blob/master/Steevanb/Sniffs/PHP/DisallowMultipleEmptyLinesSniff.php) | Empty lines |
| [Steevanb_Sniffs_PHP_DisallowSelfSniff](https://github.com/steevanb/php-code-sniffs/blob/master/Steevanb/Sniffs/PHP/DisallowSelfSniff.php) | `self` |
| [Steevanb_Sniffs_Syntax_ConcatenationSniff](https://github.com/steevanb/php-code-sniffs/blob/master/Steevanb/Sniffs/Syntax/ConcatenationSniff.php) | Concatenation character `.` |
| [Steevanb_Sniffs_Uses_GroupUsesSniff](https://github.com/steevanb/php-code-sniffs/blob/master/Steevanb/Sniffs/Uses/GroupUsesSniff.php) | `use` |
| [Steevanb_Sniffs_NamingConventions_CamelCapsFunctionNameSniff](https://github.com/steevanb/php-code-sniffs/blob/master/Steevanb/Sniffs/NamingConventions/CamelCapsFunctionNameSniff.php) | `function` |
| [Steevanb_Sniffs_NamingConventions_ValidVariableNameSniff](https://github.com/steevanb/php-code-sniffs/blob/master/Steevanb/Sniffs/NamingConventions/ValidVariableNameSniff.php) | All variables |
