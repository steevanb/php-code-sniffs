### [4.2.0](../../../compare/4.1.0...4.2.0) - 2021-05-26

- Allow `PHP ^8.0`
- Upgrade `squizlabs/php_codesniffer` dependency to `3.6.*`

### [4.1.0](../../../compare/4.0.4...4.1.0) - 2021-04-14

- `GroupUsesSniff`: force new line after open use group brace
- `GroupUsesSniff`: force new line before close use group brace

### [4.0.4](../../../compare/4.0.3...4.0.4) - 2021-02-15

- Fix `getCurrentUse` when no use found

### [4.0.3](../../../compare/4.0.2...4.0.3) - 2020-11-02

- Fix `DisallowShortArraySyntaxSpacesSniff` when array is defined without indentation (outside of class / function etc)

### [4.0.2](../../../compare/4.0.1...4.0.2) - 2020-04-28

- Fix all `Steevanb` sniffs wo where not enabled.

### [4.0.1](../../../compare/4.0.0...4.0.1) - 2020-03-19

- Fix paths in Docker image.

### [4.0.0](../../../compare/3.0.1...4.0.0) - 2020-03-19

- [BC Break] Move `ruleset.xml` from `Steevanb` to root.
- Merge `docker-php-code-sniffs` repository into this one.
- Reworked Docker image to have git and openssh for CircleCI and lower it's size.
- Move source code files into `src` directory.

### [3.0.1](../../../compare/3.0.0...3.0.1) - 2019-11-21

- Add `Symfony\Contracts` to `GroupUsesSniff::addSymfonyPrefixes()`.

### [3.0.0](../../../compare/2.0.10...3.0.0) - 2019-09-14

- [BC Break] Move `ruleset.xml` into `Steevanb`.
- Update `squizlabs/php_codesniffer` dependency to `3.5.*`
- Added `Generic.ControlStructures.DisallowYodaConditions`: ban the use of Yoda conditions.
- Added `Generic.PHP.DisallowAlternativePHPTags`
- Added `PSR12.Classes.ClosingBrace`: enforces that closing braces of classes/interfaces/traits/functions are not followed by a comment or statement.
- Added `PSR12.ControlStructures.BooleanOperatorPlacement`: enforces that boolean operators between conditions are consistently at the start or end of the line.
- Added `PSR12.ControlStructures.ControlStructureSpacing`: enforces that spacing and indents are correct inside control structure parenthesis.
- Added `PSR12.Files.DeclareStatement`: enforces the formatting of declare statements within a file.
- Added `PSR12.Files.ImportStatement`: enforces the formatting of import statements within a file.
- Added `PSR12.Files.OpenTag`: enforces that the open tag is on a line by itself when used at the start of a php-only file.
- Added `PSR12.Functions.ReturnTypeDeclaration`: enforces the formatting of return type declarations in functions and closures.
- Added `PSR12.Traits.UseDeclaration`: enforces the formatting of trait import statements within a class
- Added `steevanb\PhpCodeSniffs\Steevanb\Sniffs\PHP`: enforces that constants must have their visibility defined.
- Removed `Squiz.Commenting.FunctionCommentThrowTag`.
- Rework `StrictTypesSniff` from `RequireStrictTypesSniff of squizlabs/php_codesniffer.

### [2.0.10](../../../compare/2.0.9...2.0.10) - 2019-09-11

- Remove `Squiz.PHP.Heredoc`.

### [2.0.9](../../../compare/2.0.7...2.0.8) - 2019-05-17

- Remove `Steevanb.Functions.DisallowMultipleReturn` (really).
- Update documentation to use Docker and CircleCI.

### [2.0.8](../../../compare/2.0.7...2.0.8) - 2019-05-16

- Added `GroupUsesSniff::addFirstLevelPrefix()`.
- Fix one `BadRegroupment` error group to `MustRegroup`.

### [2.0.7](../../../compare/2.0.6...2.0.7) - 2019-05-10

- Remove `Steevanb.Functions.DisallowMultipleReturn`.

### [2.0.6](../../../compare/2.0.5...2.0.6) - 2019-05-10

- Fix `Generic.Files.LineLength` configuration.
- Allow `is_null` in `Generic.PHP.ForbiddenFunctions`.
- Remove `Squiz.ControlStructures.InlineIfDeclaration.NoBrackets`.
- Remove `Squiz.PHP.DisallowComparisonAssignment`.

### [2.0.5](../../../compare/2.0.4...2.0.5) - 2019-05-07

- Remove `Generic.WhiteSpace.LanguageConstructSpacing` sniff.
- Remove `PEAR.Files.IncludingFile` sniff.
- Remove `Squiz.Arrays.ArrayDeclaration.MultiLineNotAllowed` sniff.
- Remove `Squiz.Arrays.ArrayDeclaration.NoComma` sniff.
- Remove `Squiz.Commenting.BlockComment` sniff.
- Remove `Squiz.Commenting.InlineComment` sniff.
- Remove `Squiz.ControlStructures.InlineIfDeclaration.NotSingleLine` sniff.
- Remove `Squiz.PHP.DisallowSizeFunctionsInLoops` sniff.
- Remove `Squiz.Strings.DoubleQuoteUsage.ContainsVar` sniff.
- Remove `Squiz.WhiteSpace.LanguageConstructSpacing` sniff.
- Fix `DisallowShortArraySyntaxSpacesSniff`.
- Fix `DisallowMultipleReturnSniff` when not in function.

### [2.0.4](../../../compare/2.0.3...2.0.4) - 2019-04-25

- Remove `Squiz.Objects.ObjectInstantiation` sniff.

### [2.0.3](../../../compare/2.0.2...2.0.3) - 2019-04-19

- Fix `Squiz.Commenting.FunctionCommentThrowTag` sniff name.

### [2.0.2](../../../compare/2.0.1...2.0.2) - 2019-04-19

- Remove `Squiz.Commenting.FunctionCommentThrowTag`.

### [2.0.1](../../../compare/2.0.0...2.0.1) - 2019-04-19

- Remove `PEAR.ControlStructures.MultiLineCondition.Alignment`: bug with grouped conditions.
- Remove `Squiz.WhiteSpace.ObjectOperatorSpacing.Before`: bug with one call per line.

### [2.0.0](../../../compare/1.0.2...2.0.0) - 2019-04-16

- Update `squizlabs/php_codesniffer` to 3.2.*.
- Fix `DisallowMultipleReturnSniff` when a method contains a closure.
- Fix `DisallowSelfSniff` when used as method return type.
- Added color to sniff name when `-s` parameter is used with `./vendor/bin/phpcs`.

### [1.0.2](../../../compare/1.0.1...1.0.2) - 2019-01-29

- Fix `Steevanb_Sniffs_Functions_DisallowMultipleReturnSniff::addAllowedFunction()`.

### [1.0.1](../../../compare/1.0.0...1.0.1) - 2019-01-16

- Fix `Steevanb_Sniffs_Uses_GroupUsesSniff::addThirdLevelPrefix()`.

### 1.0.0 - 2019-01-16

- First version.
