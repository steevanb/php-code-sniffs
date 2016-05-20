[![version](https://img.shields.io/badge/version-development-green.svg)](https://github.com/steevanb/php-code-sniffs)

PHP Code Sniffs
===============

Add some sniffs for [squizlabs/php_codesniffer](https://github.com/squizlabs/PHP_CodeSniffer)

Installation
============

You will need squizlabs/php_codesniffer to use this sniffs.
I do not include it in steevanb/php-code-sniffs, if you want to use a fork.
```json
// composer.json
{
    "require-dev": {
        "squizlabs/php_codesniffer": "2.*",
        "steevanb/php-code-sniffs": "dev-master"
    }
}
```

Then, add sniffs to your ruleset.xml :
```xml
<!-- ruleset.xml -->
<?xml version="1.0"?>
<ruleset name="FooRuleset">
    <rule ref="vendor/steevanb/php-code-sniffs"></rule>
</ruleset>
```

Most used commands
==================

Summary of most used commands to check your code style :
```bash
# execute sniffs, and output errors
./vendor/bin/phpcs --standard=ruleset.xml
# execute sniffs, and write results in foo.csv
./vendor/bin/phpcs --standard=ruleset.xml --report-csv=foo.csv
# show list of configured sniffs
./vendor/bin/phpcs --standard=ruleset.xml -e
```
