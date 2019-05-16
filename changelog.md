### [2.0.8](../../compare/2.0.7...2.0.8) - 2019-05-16

- Add `GroupUsesSniff::addFirstLevelPrefix()`.
- Fix one `BadRegroupment` error group to `MustRegroup`.

### [2.0.7](../../compare/2.0.6...2.0.7) - 2019-05-10

- Remove `Steevanb.Functions.DisallowMultipleReturn`.

### [2.0.6](../../compare/2.0.5...2.0.6) - 2019-05-10

- Fix `Generic.Files.LineLength` configuration.
- Allow `is_null` in `Generic.PHP.ForbiddenFunctions`.
- Remove `Squiz.ControlStructures.InlineIfDeclaration.NoBrackets`.
- Remove `Squiz.PHP.DisallowComparisonAssignment`.

### [2.0.5](../../compare/2.0.4...2.0.5) - 2019-05-07

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

### [2.0.4](../../compare/2.0.3...2.0.4) - 2019-04-25

- Remove `Squiz.Objects.ObjectInstantiation` sniff.

### [2.0.3](../../compare/2.0.2...2.0.3) - 2019-04-19

- Fix `Squiz.Commenting.FunctionCommentThrowTag` sniff name.

### [2.0.2](../../compare/2.0.1...2.0.2) - 2019-04-19

- Remove `Squiz.Commenting.FunctionCommentThrowTag`.

### [2.0.1](../../compare/2.0.0...2.0.1) - 2019-04-19

- Remove `PEAR.ControlStructures.MultiLineCondition.Alignment`: bug with grouped conditions.
- Remove `Squiz.WhiteSpace.ObjectOperatorSpacing.Before`: bug with one call per line.

### [2.0.0](../../compare/1.0.2...2.0.0) - 2019-04-16

- Update `squizlabs/php_codesniffer` to 3.2.*.
- Fix `DisallowMultipleReturnSniff` when a method contains a closure.
- Fix `DisallowSelfSniff` when used as method return type.
- Add color to sniff name when `-s` parameter is used with `./vendor/bin/phpcs`.

### [1.0.2](../../compare/1.0.1...1.0.2) - 2019-01-29

- Fix `Steevanb_Sniffs_Functions_DisallowMultipleReturnSniff::addAllowedFunction()`.

### [1.0.1](../../compare/1.0.0...1.0.1) - 2019-01-16

- Fix `Steevanb_Sniffs_Uses_GroupUsesSniff::addThirdLevelPrefix()`.

### 1.0.0 - 2019-01-16

- First version.
